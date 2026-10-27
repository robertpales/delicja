<?php
/**
 * Pickup order e-mail to the customer: "Ready for pickup" and the reminder (inc/pickup/emails.php).
 * Built like WooCommerce's customer-processing-order.php.
 *
 * @var WC_Order $order
 * @var string   $deadline Date to collect the order by.
 * @var bool     $reminder True in the reminder.
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php echo $email_improvements_enabled ? '<div class="email-introduction">' : ''; ?>
<p>
<?php
if ( $order->get_billing_first_name() ) {
	/* translators: %s: Customer first name */
	printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) );
} else {
	esc_html_e( 'Hi,', 'woocommerce' );
}
?>
</p>
<p>
<?php
if ( $reminder ) {
	/* translators: 1: order number, 2: date and time to collect it by */
	printf( esc_html__( 'Your order #%1$s is still waiting for you in the shop. Please collect it by %2$s.', 'delicja' ), esc_html( $order->get_order_number() ), '<strong>' . esc_html( $deadline ) . '</strong>' );
} else {
	/* translators: 1: order number, 2: date and time to collect it by */
	printf( esc_html__( 'Your order #%1$s is packed and waiting for you in the shop. Please collect it by %2$s.', 'delicja' ), esc_html( $order->get_order_number() ), '<strong>' . esc_html( $deadline ) . '</strong>' );
}
?>
</p>
<p><?php esc_html_e( 'The amount is reserved on your card and charged when you collect the order. If it is not collected by then, we cancel the order and release the reservation.', 'delicja' ); ?></p>
<p>
	<strong><?php esc_html_e( 'Delicja, Bethaniagade 5, 7400 Herning', 'delicja' ); ?></strong><br>
	<?php echo implode( '<br>', array_map( 'esc_html', delicja_opening_hours_lines() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</p>
<p><?php esc_html_e( 'Questions? Call +45 51 43 03 21 or write to info@delicja.dk.', 'delicja' ); ?></p>
<?php echo $email_improvements_enabled ? '</div>' : ''; ?>

<?php
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

if ( $additional_content ) {
	echo $email_improvements_enabled ? '<table border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation"><tr><td class="email-additional-content">' : '';
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
	echo $email_improvements_enabled ? '</td></tr></table>' : '';
}

do_action( 'woocommerce_email_footer', $email );
