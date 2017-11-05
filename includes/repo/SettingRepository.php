<?php

namespace bmab;

/**
 * Class SettingRepository
 * @package bmab
 */
class SettingRepository extends BaseRepository {
	CONST PAYPAL_EMAIL = 'bmabPaypalEmail';
	CONST PAYPAL_MODE = 'bmabPaypalMode';
	CONST PAYPAL_CLIENT = 'bmabPaypalClientId';
	CONST PAYPAL_SECRET = 'bmabPaypalSecret';
	CONST CURRENCY = 'bmabCurrency';
	CONST DISPLAY_MODE = 'bmabDisplayMode';
	CONST SUCCESS_PAGE = 'bmabSuccessPage';
	CONST ERROR_PAGE = 'bmabErrorPage';

	public function __construct( App $app ) {
		parent::__construct( $app );
	}

	public function get( $option, $default = null ) {
		return get_option( $option, $default );
	}

	public function update(
		$paypalEmail,
		$paypalMode,
		$paypalClientId,
		$paypalSecret,
		$currency,
		$displayMode,
		$successPage,
		$errorPage
	) {
		$settings = array(
			self::PAYPAL_EMAIL  => $paypalEmail,
			self::PAYPAL_MODE   => $paypalMode,
			self::PAYPAL_CLIENT => $paypalClientId,
			self::PAYPAL_SECRET => $paypalSecret,
			self::CURRENCY      => $currency,
			self::DISPLAY_MODE  => $displayMode,
			self::SUCCESS_PAGE  => $successPage,
			self::ERROR_PAGE    => $errorPage
		);

		foreach ( $settings as $setting => $value ) {

			if ( get_option( $setting ) !== false ) {
				update_option( $setting, $value );
			} else {
				$deprecated = null;
				$autoload   = 'no';
				add_option( $setting, $value, $deprecated, $autoload );
			}
		}

	}
}