<?php

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
	protected $plugin_slug;
	/**
	 * @var string
	 */
	protected $version;

	/**
	 * BuyMeABeer constructor.
	 */
	public function __construct() {
		$this->plugin_slug = 'buyMeABeer';
		$this->version     = '0.1';

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
		require_once plugin_dir_path( __FILE__ ) . 'loader.php';
		$this->loader = new BuyMeABeerLoader();
	}

	/**
	 *
	 */
	private function defineAdminHooks() {
		$admin = new BuyMeABeerAdmin( $this->getVersion() );
		$this->loader->addAction( 'admin_menu', $admin, 'adminMenu' );
		$this->loader->addAction( 'admin_enqueue_styles', $admin, 'adminEnqueueStyles' );
		$this->loader->addAction( 'admin_enqueue_scripts', $admin, 'adminEnqueueScripts' );
		$this->loader->addAction( 'add_meta_boxes', $admin, 'addPostWidget' );
		$this->loader->addAction( 'save_post', $admin, 'savePostWidget' );

		// Ajax Calls
		$ajax = new BuyMeABeerAjax($admin);
		$this->loader->addAction( 'wp_ajax_bmab_formHandler', $ajax, 'formHandler' );
		$this->loader->addAction( 'wp_ajax_bmab_contentHandler', $ajax, 'contentHandler' );
	}

	/**
	 *
	 */
	private function definePublicHooks() {
		$public = new BuyMeABeerPublic( $this->getVersion() );
		$this->loader->addAction( 'the_content', $public, 'displayPostWidget' );
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
		return $this->version;
	}

}