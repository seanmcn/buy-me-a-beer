<?php

namespace bmab;

/**
 * Class BuyMeABeer
 */
class BuyMeABeer {
	/**
	 * @var BuyMeABeerLoader
	 */
	protected $loader;

	/**
	 * @var string
	 */
	protected $version;

	/**
	 * @var array
	 */
	protected $app;

	/**
	 * BuyMeABeer constructor.
	 */
	public function __construct() {
		$this->app = new App();

		$this->loadDependencies();
		$this->defineAdminHooks();
		$this->definePublicHooks();
	}

	/**
	 *
	 */
	private function loadDependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/ajax.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/ajax.php';
		require_once plugin_dir_path( __FILE__ ) . 'loader.php';
		$this->loader = new BuyMeABeerLoader();
	}

	/**
	 *
	 */
	private function defineAdminHooks() {
		$admin = new BuyMeABeerAdmin( $this->app );
		$this->loader->addAction( 'admin_menu', $admin, 'adminMenu' );
		$this->loader->addAction( 'admin_enqueue_styles', $admin, 'adminEnqueueStyles' );
		$this->loader->addAction( 'admin_enqueue_scripts', $admin, 'adminEnqueueScripts' );
		$this->loader->addAction( 'add_meta_boxes', $admin, 'addPostWidget' );
		$this->loader->addAction( 'save_post', $admin, 'savePostWidget' );

		// Back-end Ajax Calls
		$adminAjax = new BuyMeABeerAdminAjax( $this->app );
		$this->loader->addAction( 'wp_ajax_bmab_formHandler', $adminAjax, 'formHandler' );
		$this->loader->addAction( 'wp_ajax_bmab_contentHandler', $adminAjax, 'contentHandler' );

	}

	/**
	 *
	 */
	private function definePublicHooks() {
		$public = new BuyMeABeerPublic( $this->app );

		// Auto post/page widget
		$this->loader->addAction( 'the_content', $public, 'displayPostWidget' );

		// Manual shortcode widget
		$this->loader->addShortCode('bmab_widget', $public, 'displayShortCodeWidget');

		// Start session
		$this->loader->addAction( 'init', $public, 'session' );

		// Front-end Ajax Calls
		$publicAjax = new BuyMeABeerPublicAjax( $this->app );
		$this->loader->addAction( 'wp_ajax_bmab_publicFormHandler', $publicAjax, 'formHandler' );
		$this->loader->addAction( 'wp_ajax_nopriv_bmab_publicFormHandler', $publicAjax, 'formHandler' );

	}

	/**
	 *
	 */
	public function activatePlugin() {
		// check user can activate plugin
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		check_admin_referer( "activate-plugin_{$plugin}" );

		$admin = new BuyMeABeerAdmin( $this->getVersion() );
		$admin->installation();
	}

	/**
	 *
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * @return string
	 */
	public function getVersion() {
		return $this->app->version;
	}

}