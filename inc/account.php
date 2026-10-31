<?php
/**
 * My account (page built in Elementor with WooCommerce's [woocommerce_my_account]): when visitors can create an
 * account, logging in and registering share one card. A switch on top ("Log in" / "Register") shows one form at a
 * time; it is two radio buttons, so it works without JavaScript. After a failed registration the register form stays
 * open. The social login buttons sit once under both forms (inc/social-login.php). Styles in my-account.css (=== Forms ===).
 *
 * The register form asks for two consents instead of WooCommerce's privacy text: "I have read the privacy policy"
 * (required) and e-mails about deals and new products (optional, unticked). Both are saved on the customer with the
 * date (user meta `delicja_privacy_accepted`, `delicja_marketing_consent`); the marketing consent can be changed in
 * My account → Account details. Using it (a newsletter tool) is Robo's.
 *
 * Logged in: the dashboard shows cards (latest order, orders, delivery address, details) instead of WooCommerce's two
 * sentences, the order lists show the status as a label, a pickup order that is ready says so on its page, and the
 * Downloads section is left out (the shop sells no downloads). Styles in my-account.css (=== Account pages ===).
 */

defined( 'ABSPATH' ) || exit;

/**
 * True when visitors can create an account on the My account page.
 */
function delicja_account_registration(): bool {
	return 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
}

// After the notices (priority 10), so an error shows above the switch.
add_action( 'woocommerce_before_customer_login_form', function () {
	if ( ! delicja_account_registration() ) {
		return;
	}

	// Only picks the open form; WooCommerce checks the nonce when it handles the registration.
	$register = isset( $_POST['register'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

	printf(
		'<div class="dl-auth-switch" role="radiogroup" aria-label="%1$s">'
		. '<input class="dl-auth-radio" type="radio" name="dl-auth" id="dl-auth-login" value="login"%2$s>'
		. '<label class="dl-auth-option" for="dl-auth-login">%3$s</label>'
		. '<input class="dl-auth-radio" type="radio" name="dl-auth" id="dl-auth-register" value="register"%4$s>'
		. '<label class="dl-auth-option" for="dl-auth-register">%5$s</label>'
		. '</div>',
		esc_attr__( 'Log in or register', 'delicja' ),
		$register ? '' : ' checked',
		esc_html__( 'Log in', 'delicja' ),
		$register ? ' checked' : '',
		esc_html__( 'Register', 'delicja' )
	);
}, 20 );

/**
 * Checkbox row for a consent. $checked keeps the tick after a failed form.
 */
function delicja_consent_checkbox( string $name, string $label, bool $checked, bool $required = false ): string {
	return sprintf(
		'<p class="form-row dl-consent-row"><label class="dl-consent" for="%1$s"><input type="checkbox" name="%1$s" id="%1$s" value="1"%2$s%3$s><span>%4$s%5$s</span></label></p>',
		esc_attr( $name ),
		$checked ? ' checked' : '',
		$required ? ' required' : '',
		$label,
		$required ? '&nbsp;<span class="required" aria-hidden="true">*</span>' : ''
	);
}

/**
 * Label of the marketing consent (register form and Account details).
 */
function delicja_marketing_label(): string {
	return esc_html__( 'Send me e-mails about deals and new products. I can unsubscribe at any time.', 'delicja' );
}

// The consents replace WooCommerce's privacy text in the register form.
add_action( 'init', function () {
	remove_action( 'woocommerce_register_form', 'wc_registration_privacy_policy_text', 20 );
} );

add_action( 'woocommerce_register_form', function () {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- only keeps the ticks after a failed registration.
	$url     = get_privacy_policy_url();
	$privacy = $url
		? sprintf(
			/* translators: %s: link to the privacy policy */
			esc_html__( 'I have read the %s.', 'delicja' ),
			'<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html__( 'privacy policy', 'delicja' ) . '</a>'
		)
		: esc_html__( 'I have read the privacy policy.', 'delicja' );

	echo delicja_consent_checkbox( 'dl_privacy', $privacy, ! empty( $_POST['dl_privacy'] ), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo delicja_consent_checkbox( 'dl_marketing', delicja_marketing_label(), ! empty( $_POST['dl_marketing'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	// phpcs:enable
}, 20 );

// WooCommerce has checked the nonce before these run. The filter also runs when the checkout creates an account; only
// the My account register form (its button is named "register") has the checkbox.
add_filter( 'woocommerce_registration_errors', function ( $errors ) {
	if ( isset( $_POST['register'] ) && empty( $_POST['dl_privacy'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$errors->add( 'dl_privacy', esc_html__( 'Please confirm that you have read the privacy policy.', 'delicja' ) );
	}

	return $errors;
} );

add_action( 'woocommerce_created_customer', function ( $customer_id ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	if ( ! empty( $_POST['dl_privacy'] ) ) {
		update_user_meta( $customer_id, 'delicja_privacy_accepted', current_time( 'mysql' ) );
	}
	if ( ! empty( $_POST['dl_marketing'] ) ) {
		update_user_meta( $customer_id, 'delicja_marketing_consent', current_time( 'mysql' ) );
	}
	// phpcs:enable
} );

// Account details: the customer can give or withdraw the marketing consent.
add_action( 'woocommerce_edit_account_form', function () {
	$checked = (bool) get_user_meta( get_current_user_id(), 'delicja_marketing_consent', true );

	echo delicja_consent_checkbox( 'dl_marketing', delicja_marketing_label(), $checked ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
} );

add_action( 'woocommerce_save_account_details', function ( $user_id ) {
	if ( empty( $_POST['dl_marketing'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		delete_user_meta( $user_id, 'delicja_marketing_consent' );
	} elseif ( ! get_user_meta( $user_id, 'delicja_marketing_consent', true ) ) {
		update_user_meta( $user_id, 'delicja_marketing_consent', current_time( 'mysql' ) );
	}
} );

// The shop sells no downloadable products.
add_filter( 'woocommerce_account_menu_items', function ( $items ) {
	unset( $items['downloads'] );

	return $items;
} );

/**
 * Order status as a label, e.g. <span class="dl-status dl-status--processing">Processing</span>.
 */
function delicja_order_status_label( WC_Order $order ): string {
	return sprintf(
		'<span class="dl-status dl-status--%s">%s</span>',
		esc_attr( $order->get_status() ),
		esc_html( wc_get_order_status_name( $order->get_status() ) )
	);
}

add_action( 'woocommerce_my_account_my_orders_column_order-status', function ( $order ) {
	echo delicja_order_status_label( $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
} );

// Dashboard: cards instead of WooCommerce's text (hidden by my-account.css when the cards are there).
add_action( 'woocommerce_account_dashboard', function () {
	$customer = new WC_Customer( get_current_user_id() );
	$orders   = wc_get_orders(
		[
			'customer_id' => $customer->get_id(),
			'limit'       => 1,
			'orderby'     => 'date',
			'order'       => 'DESC',
		]
	);
	$latest   = $orders[0] ?? null;
	$count    = $customer->get_order_count();
	$name     = $customer->get_first_name() ?: $customer->get_display_name();
	$address  = WC()->countries->get_formatted_address(
		[
			'address_1' => $customer->get_shipping_address_1() ?: $customer->get_billing_address_1(),
			'address_2' => $customer->get_shipping_address_1() ? $customer->get_shipping_address_2() : $customer->get_billing_address_2(),
			'postcode'  => $customer->get_shipping_postcode() ?: $customer->get_billing_postcode(),
			'city'      => $customer->get_shipping_city() ?: $customer->get_billing_city(),
			'country'   => $customer->get_shipping_country() ?: ( $customer->get_billing_country() ?: 'DK' ),
		],
		', '
	);
	$marketing = (bool) get_user_meta( $customer->get_id(), 'delicja_marketing_consent', true );
	?>
	<div class="dl-dash">
		<div class="dl-dash-head">
			<p class="dl-dash-hello">
				<?php
				/* translators: %s: the customer's first name */
				printf( esc_html__( 'Good to see you, %s!', 'delicja' ), esc_html( $name ) );
				?>
			</p>
			<p class="dl-dash-lead"><?php esc_html_e( 'Follow your orders, change your addresses and your details here.', 'delicja' ); ?></p>
		</div>

		<div class="dl-dash-grid">
			<?php if ( $latest ) : ?>
				<section class="dl-dash-card dl-dash-card--order">
					<h2 class="dl-dash-title"><?php esc_html_e( 'Latest order', 'delicja' ); ?></h2>
					<p class="dl-dash-order">
						<span class="dl-dash-number">
							<?php
							/* translators: %s: order number */
							printf( esc_html__( 'Order #%s', 'delicja' ), esc_html( $latest->get_order_number() ) );
							?>
						</span>
						<?php echo delicja_order_status_label( $latest ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</p>
					<p class="dl-dash-meta">
						<?php
						echo esc_html( wc_format_datetime( $latest->get_date_created() ) );
						echo ' · ';
						/* translators: %d: number of items */
						echo esc_html( sprintf( _n( '%d item', '%d items', $latest->get_item_count(), 'delicja' ), $latest->get_item_count() ) );
						echo ' · ';
						echo wp_kses_post( $latest->get_formatted_order_total() );
						?>
					</p>
					<a class="button dl-dash-button" href="<?php echo esc_url( $latest->get_view_order_url() ); ?>"><?php esc_html_e( 'See the order', 'delicja' ); ?></a>
				</section>
			<?php endif; ?>

			<a class="dl-dash-card dl-dash-card--link dl-dash-card--orders" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">
				<h2 class="dl-dash-title"><?php esc_html_e( 'Orders', 'delicja' ); ?></h2>
				<p class="dl-dash-value">
					<?php
					/* translators: %d: number of orders */
					echo esc_html( $count ? sprintf( _n( '%d order', '%d orders', $count, 'delicja' ), $count ) : __( 'No orders yet', 'delicja' ) );
					?>
				</p>
				<span class="dl-dash-more"><?php esc_html_e( 'All orders', 'delicja' ); ?></span>
			</a>

			<a class="dl-dash-card dl-dash-card--link dl-dash-card--address" href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>">
				<h2 class="dl-dash-title"><?php esc_html_e( 'Delivery address', 'delicja' ); ?></h2>
				<p class="dl-dash-value"><?php echo $address ? esc_html( $address ) : esc_html__( 'Not added yet', 'delicja' ); ?></p>
				<span class="dl-dash-more"><?php esc_html_e( 'Change addresses', 'delicja' ); ?></span>
			</a>

			<a class="dl-dash-card dl-dash-card--link dl-dash-card--details" href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>">
				<h2 class="dl-dash-title"><?php esc_html_e( 'Your details', 'delicja' ); ?></h2>
				<p class="dl-dash-value"><?php echo esc_html( $customer->get_email() ); ?></p>
				<p class="dl-dash-meta">
					<?php echo esc_html( $marketing ? __( 'E-mails about deals: on', 'delicja' ) : __( 'E-mails about deals: off', 'delicja' ) ); ?>
				</p>
				<span class="dl-dash-more"><?php esc_html_e( 'Change details or password', 'delicja' ); ?></span>
			</a>
		</div>

		<p class="dl-dash-shop">
			<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Back to the shop', 'delicja' ); ?></a>
		</p>
	</div>
	<?php
} );

// Order page: a pickup order that is ready says where and until when to collect it.
add_action( 'woocommerce_view_order', function ( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order || 'ready-pickup' !== $order->get_status() ) {
		return;
	}
	$deadline = function_exists( 'delicja_pickup_deadline_text' ) ? delicja_pickup_deadline_text( $order ) : '';
	?>
	<div class="dl-pickup-ready">
		<p class="dl-pickup-ready-title"><?php esc_html_e( 'Your order is ready in the shop', 'delicja' ); ?></p>
		<p>
			<?php
			echo esc_html__( 'Collect it at Bethaniagade 5, 7400 Herning.', 'delicja' );
			if ( $deadline ) {
				echo ' ';
				/* translators: %s: date and time, e.g. "Tuesday 28 October, 17:00" */
				printf( esc_html__( 'Please collect it by %s.', 'delicja' ), '<strong>' . esc_html( $deadline ) . '</strong>' );
			}
			?>
		</p>
	</div>
	<?php
}, 5 );
