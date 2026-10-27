<?php
/**
 * Pickup orders (shipping method "Pickup in the shop", local_pickup):
 * - The shop marks an order "Ready for pickup" (order status, also as a bulk action and a button in the order list).
 *   The customer gets an e-mail with the date to collect it by: the end of the third day the shop is open, counting
 *   the day it was marked ready if the shop is open at least two more hours that day.
 * - After the second of those days the customer gets a reminder.
 * - Not collected by the end of the third day: the order is cancelled. Frisbii Pay releases the card reservation
 *   and WooCommerce puts the goods back in stock; the customer gets WooCommerce's "Cancelled order" e-mail with a
 *   line saying why.
 * - A pickup order still not marked ready a day after it was placed: one e-mail to the shop, because the card
 *   reservation is only valid about 7 days.
 * A check runs every hour (Action Scheduler, delicja_pickup_check). Public holidays are not taken into account.
 * E-mail classes in inc/pickup/emails.php, templates in woocommerce/emails/, admin styles in assets/css/admin.css.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shop opening hours by ISO weekday (1 = Monday), as shown on the site; null = closed.
 */
function delicja_opening_hours(): array {
	return [
		1 => null,
		2 => [ '11:00', '17:00' ],
		3 => [ '11:00', '17:00' ],
		4 => [ '11:00', '17:30' ],
		5 => [ '11:00', '17:30' ],
		6 => [ '10:00', '16:00' ],
		7 => [ '10:00', '16:00' ],
	];
}

/**
 * Opening hours as text lines, consecutive days with the same hours together, e.g. "Tuesday–Wednesday: 11:00–17:00".
 */
function delicja_opening_hours_lines(): array {
	global $wp_locale;
	$groups = [];
	foreach ( delicja_opening_hours() as $n => $hours ) {
		$text = $hours ? implode( '–', $hours ) : __( 'closed', 'delicja' );
		$last = array_key_last( $groups );
		if ( null !== $last && $groups[ $last ]['text'] === $text ) {
			$groups[ $last ]['to'] = $n;
		} else {
			$groups[] = [ 'from' => $n, 'to' => $n, 'text' => $text ];
		}
	}

	$name = fn( $n ) => $wp_locale->get_weekday( $n % 7 );

	return array_map(
		fn( $g ) => ( $g['from'] === $g['to'] ? $name( $g['from'] ) : $name( $g['from'] ) . '–' . $name( $g['to'] ) ) . ': ' . $g['text'],
		$groups
	);
}

/**
 * Closing times of the next three days the shop is open after $from (that day counts if the shop is open at least
 * two more hours).
 *
 * @return DateTimeImmutable[]
 */
function delicja_pickup_days( int $from ): array {
	$hours = delicja_opening_hours();
	$day   = ( new DateTimeImmutable( '@' . $from ) )->setTimezone( wp_timezone() )->setTime( 0, 0 );
	$days  = [];

	for ( $i = 0; $i < 21 && count( $days ) < 3; $i++ ) {
		$date = $day->modify( "+{$i} days" );
		$open = $hours[ (int) $date->format( 'N' ) ];
		if ( ! $open ) {
			continue;
		}
		[ $h, $m ] = array_map( 'intval', explode( ':', $open[1] ) );
		$close     = $date->setTime( $h, $m );
		if ( $close->getTimestamp() >= $from + 2 * HOUR_IN_SECONDS ) {
			$days[] = $close;
		}
	}

	return $days;
}

/**
 * True when the order is collected in the shop.
 */
function delicja_is_pickup_order( WC_Order $order ): bool {
	return $order->has_shipping_method( 'local_pickup' );
}

/**
 * Date to collect the order by, e.g. "Tuesday 28 October, 17:00"; empty when the order is not ready.
 */
function delicja_pickup_deadline_text( WC_Order $order ): string {
	$ready = (int) $order->get_meta( '_delicja_ready_at' );
	$days  = $ready ? delicja_pickup_days( $ready ) : [];

	return $days ? wp_date( 'l j F, H:i', $days[2]->getTimestamp() ) : '';
}

// Order status "Ready for pickup" (wc-ready-pickup).
add_action( 'init', function () {
	register_post_status(
		'wc-ready-pickup',
		[
			'label'                     => _x( 'Ready for pickup', 'Order status', 'delicja' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: %s: number of orders */
			'label_count'               => _n_noop( 'Ready for pickup <span class="count">(%s)</span>', 'Ready for pickup <span class="count">(%s)</span>', 'delicja' ),
		]
	);
} );

add_filter( 'wc_order_statuses', function ( $statuses ) {
	$result = [];
	foreach ( $statuses as $key => $label ) {
		$result[ $key ] = $label;
		if ( 'wc-processing' === $key ) {
			$result['wc-ready-pickup'] = _x( 'Ready for pickup', 'Order status', 'delicja' );
		}
	}

	return $result;
} );

// Bulk action in the order list (WooCommerce handles "mark_<status>" itself).
foreach ( [ 'bulk_actions-woocommerce_page_wc-orders', 'bulk_actions-edit-shop_order' ] as $hook ) {
	add_filter( $hook, function ( $actions ) {
		$actions['mark_ready-pickup'] = __( 'Change status to ready for pickup', 'delicja' );

		return $actions;
	}, 20 );
}

// Buttons in the order list: "Ready for pickup" on processing pickup orders, "Complete" on ready ones.
add_filter( 'woocommerce_admin_order_actions', function ( $actions, $order ) {
	$url = fn( $status ) => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=' . $status . '&order_id=' . $order->get_id() ), 'woocommerce-mark-order-status' );

	if ( $order->has_status( 'processing' ) && delicja_is_pickup_order( $order ) ) {
		$actions['ready-pickup'] = [
			'url'    => $url( 'ready-pickup' ),
			'name'   => __( 'Ready for pickup', 'delicja' ),
			'action' => 'ready-pickup',
		];
	}
	if ( $order->has_status( 'ready-pickup' ) ) {
		$actions['complete'] = [
			'url'    => $url( 'completed' ),
			'name'   => __( 'Complete', 'woocommerce' ),
			'action' => 'complete',
		];
	}

	return $actions;
}, 10, 2 );

add_action( 'admin_enqueue_scripts', function () {
	$file = '/assets/css/admin.css';
	wp_enqueue_style( 'delicja-admin', get_stylesheet_directory_uri() . $file, [], filemtime( get_stylesheet_directory() . $file ) );
} );

// Marked ready: remember when, so the deadline and the reminder count from then (the e-mail is sent by its class).
add_action( 'woocommerce_order_status_ready-pickup', function ( $order_id, $order ) {
	$order->update_meta_data( '_delicja_ready_at', time() );
	$order->delete_meta_data( '_delicja_pickup_reminded' );
	$order->save_meta_data();
}, 5, 2 );

// E-mails: "Ready for pickup" and the reminder (WooCommerce → Settings → Emails).
add_filter( 'woocommerce_email_classes', function ( $emails ) {
	require_once __DIR__ . '/pickup/emails.php';
	$emails['Delicja_Email_Ready_Pickup']    = new Delicja_Email_Ready_Pickup();
	$emails['Delicja_Email_Pickup_Reminder'] = new Delicja_Email_Pickup_Reminder();

	return $emails;
} );

add_filter( 'woocommerce_email_actions', function ( $actions ) {
	$actions[] = 'woocommerce_order_status_ready-pickup';
	$actions[] = 'delicja_pickup_reminder';

	return $actions;
} );

// The cancelled order e-mail to the customer says why, when the order was not collected.
add_action( 'woocommerce_email_before_order_table', function ( $order, $sent_to_admin, $plain_text, $email ) {
	if ( 'customer_cancelled_order' !== $email->id || ! $order->get_meta( '_delicja_pickup_expired' ) ) {
		return;
	}
	$text = __( 'The order was not collected in time, so we have cancelled it and released the amount reserved on your card. You are welcome to order again.', 'delicja' );

	echo $plain_text ? esc_html( $text ) . "\n\n" : '<p>' . esc_html( $text ) . '</p>';
}, 5, 4 );

// Hourly check, scheduled from wp-admin (no extra query on every front-end request).
add_action( 'admin_init', function () {
	if ( function_exists( 'as_has_scheduled_action' ) && ! as_has_scheduled_action( 'delicja_pickup_check' ) ) {
		as_schedule_recurring_action( time() + 60, HOUR_IN_SECONDS, 'delicja_pickup_check', [], 'delicja' );
	}
} );

add_action( 'delicja_pickup_check', function () {
	$now = time();

	foreach ( wc_get_orders( [ 'status' => 'ready-pickup', 'limit' => -1 ] ) as $order ) {
		if ( ! delicja_is_pickup_order( $order ) ) {
			continue;
		}
		$ready = (int) $order->get_meta( '_delicja_ready_at' );
		if ( ! $ready ) {
			$ready = $order->get_date_modified() ? $order->get_date_modified()->getTimestamp() : $now;
			$order->update_meta_data( '_delicja_ready_at', $ready );
			$order->save_meta_data();
		}
		$days = delicja_pickup_days( $ready );
		if ( count( $days ) < 3 ) {
			continue;
		}

		if ( $now >= $days[2]->getTimestamp() ) {
			$order->update_meta_data( '_delicja_pickup_expired', 1 );
			$order->save_meta_data();
			$order->update_status( 'cancelled', __( 'Not collected within 3 opening days: cancelled automatically.', 'delicja' ) );
		} elseif ( $now >= $days[1]->getTimestamp() && ! $order->get_meta( '_delicja_pickup_reminded' ) ) {
			$order->update_meta_data( '_delicja_pickup_reminded', $now );
			$order->save_meta_data();
			do_action( 'delicja_pickup_reminder', $order->get_id(), $order );
			$order->add_order_note( __( 'Pickup reminder sent to the customer.', 'delicja' ) );
		}
	}

	// Pickup orders still not ready a day after they were placed: tell the shop once.
	$late = [];
	foreach ( wc_get_orders( [ 'status' => 'processing', 'limit' => -1, 'date_created' => '<' . ( $now - DAY_IN_SECONDS ) ] ) as $order ) {
		if ( ! delicja_is_pickup_order( $order ) || $order->get_meta( '_delicja_pickup_alerted' ) ) {
			continue;
		}
		$order->update_meta_data( '_delicja_pickup_alerted', $now );
		$order->save_meta_data();
		$late[] = sprintf( '<a href="%s">#%s</a> %s', esc_url( $order->get_edit_order_url() ), esc_html( $order->get_order_number() ), esc_html( $order->get_formatted_billing_full_name() ) );
	}
	if ( $late ) {
		$settings = get_option( 'woocommerce_new_order_settings', [] );
		$to       = ! empty( $settings['recipient'] ) ? $settings['recipient'] : get_option( 'admin_email' );
		$mailer   = WC()->mailer();
		$heading  = __( 'Pickup orders not ready yet', 'delicja' );
		$body     = '<p>' . esc_html__( 'These pickup orders were placed more than a day ago and are not marked "Ready for pickup" yet. The card reservation is valid about 7 days.', 'delicja' ) . '</p><ul><li>' . implode( '</li><li>', $late ) . '</li></ul>';
		$mailer->send( $to, $heading, $mailer->wrap_message( $heading, $body ) );
	}
} );
