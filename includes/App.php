<?php

namespace bmab;

require_once plugin_dir_path( __DIR__ ) . 'includes/manager.php';
require_once plugin_dir_path( __DIR__ ) . "includes/config.php";
require_once plugin_dir_path( __DIR__ ) . "includes/repo/BaseRepository.php";

require_once plugin_dir_path( __DIR__ ) . "includes/model/ItemModel.php";
require_once plugin_dir_path( __DIR__ ) . "includes/repo/ItemRepository.php";

require_once plugin_dir_path( __DIR__ ) . "includes/model/PaymentModel.php";
require_once plugin_dir_path( __DIR__ ) . "includes/repo/PaymentRepository.php";

require_once plugin_dir_path( __DIR__ ) . "includes/repo/SettingRepository.php";


require_once plugin_dir_path( __DIR__ ) . "includes/model/WidgetModel.php";
require_once plugin_dir_path( __DIR__ ) . "includes/repo/WidgetRepository.php";

require_once plugin_dir_path( __DIR__ ) . "payment_services/paypal.php";


/**
 * Class App
 * @package bmab
 */
class App {
	/**
	 * @var array
	 */
	public $repos;
	/**
	 * @var BuyMeABeerConfig
	 */
	public $config;
	/**
	 * @var string
	 */
	public $version = '0.5';
	/**
	 * @var mixed|void
	 */
	public $currency;
	/**
	 * @var \wpdb
	 */
	public $db;

	/**
	 * App constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->db       = $wpdb;
		$this->config   = new BuyMeABeerConfig();
		$this->currency = get_option( 'bmabCurrency', 'USD' );

		$this->repos = array(
			'widgets'  => new WidgetRepository( $this ),
			'items'    => new ItemRepository( $this ),
			'payments' => new PaymentRepository( $this ),
			'settings' => new SettingRepository( $this )
		);
	}

	function formatAsCurrency( $value ) {
		global $currencyMappings;

		return $currencyMappings[ $this->currency ]['pre'] . $value . $currencyMappings[ $this->currency ]['post'];
	}

}