<?php
/**
 * Temporary access for outside helpers (e.g. Flatpay setting up the payment gateway): a user with the meta
 * `delicja_access_expires` (Unix time) cannot log in after that time, and a session still open is ended on the next
 * request. The account itself stays (delete it by hand afterwards, it is in Robo's todo). Set the date when creating
 * the account, e.g. update_user_meta( $id, 'delicja_access_expires', strtotime( '2026-11-17 23:59 Europe/Copenhagen' ) ).
 */

defined( 'ABSPATH' ) || exit;

/**
 * True when the user's temporary access has ended.
 */
function delicja_access_expired( int $user_id ): bool {
	$expires = (int) get_user_meta( $user_id, 'delicja_access_expires', true );

	return $expires && time() > $expires;
}

add_filter( 'authenticate', function ( $user ) {
	if ( $user instanceof WP_User && delicja_access_expired( $user->ID ) ) {
		return new WP_Error( 'delicja_access_expired', __( 'This temporary account has expired. Please contact the shop.', 'delicja' ) );
	}

	return $user;
}, 100 );

add_action( 'init', function () {
	if ( is_user_logged_in() && delicja_access_expired( get_current_user_id() ) ) {
		WP_Session_Tokens::get_instance( get_current_user_id() )->destroy_all();
		wp_logout();
	}
}, 1 );
