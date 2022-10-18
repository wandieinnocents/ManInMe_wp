<?php

namespace SmashBalloon\YouTubeFeed\Helpers;

class Util {
	public static function isPro() {
		return defined( 'SBY_PRO' ) && SBY_PRO === true;
	}

	public static function isProduction() {
		return empty($_ENV['SBY_DEVELOPMENT']) || $_ENV['SBY_DEVELOPMENT'] !== 'true';
	}

	public static function ajaxPreflightChecks() {
		check_ajax_referer( 'sby_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(); // This auto-dies.
		}
	}
}
