<?php

namespace bmab;

require_once plugin_dir_path( __DIR__ ) . "includes/Config.php";
require_once plugin_dir_path( __DIR__ ) . 'includes/Installer.php';
require_once plugin_dir_path( __DIR__ ) . 'includes/WordpressLoader.php';

require_once plugin_dir_path( __DIR__ ) . "includes/repo/BaseRepository.php";

require_once plugin_dir_path( __DIR__ ) . "includes/model/ItemModel.php";
require_once plugin_dir_path( __DIR__ ) . "includes/repo/ItemRepository.php";

require_once plugin_dir_path( __DIR__ ) . "includes/model/PaymentModel.php";
require_once plugin_dir_path( __DIR__ ) . "includes/repo/PaymentRepository.php";

require_once plugin_dir_path( __DIR__ ) . "includes/repo/SettingRepository.php";


require_once plugin_dir_path( __DIR__ ) . "includes/model/WidgetModel.php";
require_once plugin_dir_path( __DIR__ ) . "includes/repo/WidgetRepository.php";

require_once plugin_dir_path( __DIR__ ) . "payment_services/paypal.php";


require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/AdminActions.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/AdminAjaxActions.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/PublicActions.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/PublicAjaxActions.php';

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
	 * @var Config
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
	 * @var WordpressLoader
	 */
	protected $loader;

	/**
	 * App constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->db       = $wpdb;
		$this->config   = new Config();
		$this->currency = get_option( 'bmabCurrency', 'USD' );

		$this->repos = array(
			'widgets'  => new WidgetRepository( $this ),
			'items'    => new ItemRepository( $this ),
			'payments' => new PaymentRepository( $this ),
			'settings' => new SettingRepository( $this )
		);

		$this->loadIntoWordpress();

	}

	function formatAsCurrency( $value ) {
		return $this->config->currencyMappings[ $this->currency ]['pre'] . $value . $this->config->currencyMappings[ $this->currency ]['post'];
	}

	public function loadIntoWordpress() {
		$this->loader = new WordpressLoader();
		$this->definePublicHooks();
		$this->defineAdminHooks();
	}

	private function defineAdminHooks() {
		$admin = new AdminActions( $this );
		$this->loader->addAction( 'admin_menu', $admin, 'adminMenu' );
		$this->loader->addAction( 'admin_enqueue_styles', $admin, 'adminEnqueueStyles' );
		$this->loader->addAction( 'admin_enqueue_scripts', $admin, 'adminEnqueueScripts' );
		$this->loader->addAction( 'add_meta_boxes', $admin, 'addPostWidget' );
		$this->loader->addAction( 'save_post', $admin, 'savePostWidget' );

		// Back-end Ajax Calls
		$adminAjax = new AdminAjaxActions( $this );
		$this->loader->addAction( 'wp_ajax_bmab_formHandler', $adminAjax, 'formHandler' );
		$this->loader->addAction( 'wp_ajax_bmab_contentHandler', $adminAjax, 'contentHandler' );

	}

	private function definePublicHooks() {
		$public = new PublicActions( $this );

		// Auto post/page widget
		$this->loader->addAction( 'the_content', $public, 'displayPostWidget' );

		// Manual shortcode widget
		$this->loader->addShortCode( 'bmab_widget', $public, 'displayShortCodeWidget' );

		// Start session
		$this->loader->addAction( 'init', $public, 'session' );

		// Front-end Ajax Calls
		$publicAjax = new PublicAjaxActions( $this );
		$this->loader->addAction( 'wp_ajax_bmab_publicFormHandler', $publicAjax, 'formHandler' );
		$this->loader->addAction( 'wp_ajax_nopriv_bmab_publicFormHandler', $publicAjax, 'formHandler' );

	}

	public function run() {
		$this->loader->run();
	}

}