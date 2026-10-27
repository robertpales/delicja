<?php
/**
 * Pickup order e-mail to the customer, plain text (see ../customer-pickup.php).
 *
 * @var WC_Order $order
 * @var string   $deadline Date to collect the order by.
 * @var bool     $reminder True in the reminder.
 */

defined( 'ABSPATH' ) || exit;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/* translators: %s: Customer first name */
echo sprintf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ) . "\n\n";

if ( $reminder ) {
	/* translators: 1: order number, 2: date and time to collect it by */
	echo sprintf( esc_html__( 'Your order #%1$s is still waiting for you in the shop. Please collect it by %2$s.', 'delicja' ), esc_html( $order->get_order_number() ), esc_html( $deadline ) ) . "\n\n";
} else {
	/* translators: 1: order number, 2: date and time to collect it by */
	echo sprintf( esc_html__( 'Your order #%1$s is packed and waiting for you in the shop. Please collect it by %2$s.', 'delicja' ), esc_html( $order->get_order_number() ), esc_html( $deadline ) ) . "\n\n";
}
echo esc_html__( 'The amount is reserved on your card and charged when you collect the order. If it is not collected by then, we cancel the order and release the reservation.', 'delicja' ) . "\n\n";
echo esc_html__( 'Delicja, Bethaniagade 5, 7400 Herning', 'delicja' ) . "\n";
echo esc_html( implode( "\n", delicja_opening_hours_lines() ) ) . "\n\n";
echo esc_html__( 'Questions? Call +45 51 43 03 21 or write to info@delicja.dk.', 'delicja' ) . "\n\n";

do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n----------------------------------------\n\n";

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n\n----------------------------------------\n\n";

if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
