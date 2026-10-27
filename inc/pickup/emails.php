<?php
/**
 * E-mails for pickup orders (inc/pickup.php): "Ready for pickup" and the reminder. Both use the template
 * woocommerce/emails/customer-pickup.php (plain text: woocommerce/emails/plain/customer-pickup.php).
 * Loaded by the woocommerce_email_classes filter, when WC_Email exists.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shared part: recipient, placeholders and template variables.
 */
abstract class Delicja_Email_Pickup extends WC_Email {

	/**
	 * True in the reminder.
	 *
	 * @var bool
	 */
	protected $reminder = false;

	/**
	 * Sets the shared fields; $hook is the action that sends the e-mail.
	 */
	protected function setup( string $hook ) {
		$this->customer_email = true;
		$this->template_html  = 'emails/customer-pickup.php';
		$this->template_plain = 'emails/plain/customer-pickup.php';
		$this->placeholders   = [
			'{order_date}'   => '',
			'{order_number}' => '',
		];

		add_action( $hook, [ $this, 'trigger' ], 10, 2 );

		parent::__construct();
	}

	public function get_default_additional_content() {
		return __( 'See you in the shop!', 'delicja' );
	}

	/**
	 * @param int           $order_id Order ID.
	 * @param WC_Order|bool $order    Order.
	 */
	public function trigger( $order_id, $order = false ) {
		$this->setup_locale();

		if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order_id );
		}
		if ( is_a( $order, 'WC_Order' ) && delicja_is_pickup_order( $order ) ) {
			$this->object                         = $order;
			$this->recipient                      = $order->get_billing_email();
			$this->placeholders['{order_date}']   = wc_format_datetime( $order->get_date_created() );
			$this->placeholders['{order_number}'] = $order->get_order_number();

			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}
		}

		$this->restore_locale();
	}

	/**
	 * Template variables (shared by HTML and plain text).
	 */
	protected function template_args( bool $plain_text ): array {
		return [
			'order'              => $this->object,
			'email_heading'      => $this->get_heading(),
			'additional_content' => $this->get_additional_content(),
			'deadline'           => $this->object ? delicja_pickup_deadline_text( $this->object ) : '',
			'reminder'           => $this->reminder,
			'sent_to_admin'      => false,
			'plain_text'         => $plain_text,
			'email'              => $this,
		];
	}

	public function get_content_html() {
		return wc_get_template_html( $this->template_html, $this->template_args( false ) );
	}

	public function get_content_plain() {
		return wc_get_template_html( $this->template_plain, $this->template_args( true ) );
	}
}

/**
 * Sent when the shop marks the order "Ready for pickup".
 */
class Delicja_Email_Ready_Pickup extends Delicja_Email_Pickup {

	public function __construct() {
		$this->id          = 'customer_ready_pickup';
		$this->title       = __( 'Ready for pickup', 'delicja' );
		$this->description = __( 'Sent to the customer when a pickup order is marked "Ready for pickup", with the date to collect it by.', 'delicja' );

		$this->setup( 'woocommerce_order_status_ready-pickup_notification' );
	}

	public function get_default_subject() {
		return __( 'Your order #{order_number} is ready for pickup', 'delicja' );
	}

	public function get_default_heading() {
		return __( 'Your order is ready', 'delicja' );
	}
}

/**
 * Sent once after the second of the three pickup days, when the order is still not collected.
 */
class Delicja_Email_Pickup_Reminder extends Delicja_Email_Pickup {

	public function __construct() {
		$this->id          = 'customer_pickup_reminder';
		$this->title       = __( 'Pickup reminder', 'delicja' );
		$this->description = __( 'Sent to the customer when a pickup order has not been collected after two of the three pickup days.', 'delicja' );
		$this->reminder    = true;

		$this->setup( 'delicja_pickup_reminder_notification' );
	}

	public function get_default_subject() {
		return __( 'Reminder: your order #{order_number} is waiting for you', 'delicja' );
	}

	public function get_default_heading() {
		return __( 'Your order is still waiting', 'delicja' );
	}
}
