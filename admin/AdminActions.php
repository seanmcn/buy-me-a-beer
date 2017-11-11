<?php

namespace bmab;

/**
 * Class AdminActions
 * @package bmab
 */
class AdminActions {

	private $app;

	/** @var WidgetRepository widget_repo */
	protected $widget_repo;

	/** @var ItemRepository widget_repo */
	protected $item_repo;

	public function __construct( $app ) {
		$this->app         = $app;
		$this->widget_repo = $this->app->repos['widgets'];
		$this->item_repo   = $this->app->repos['items'];
	}

	/**
	 * Front end CSS
	 */
	public function adminEnqueueStyles() {

	}

	public function adminEnqueueScripts() {
		/* Admin JS gets loaded here */
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_media();

		wp_register_script( 'bmabNoty', plugins_url( 'admin/js/vendor/jquery.noty.packaged.min.js', __DIR__ ),
			array
			(
				'jquery'
			) );

		wp_register_script( 'bmabImageUploaderJs', plugins_url( 'admin/js/helper/imageUploader.js', __DIR__ ),
			array
			(
				'jquery',
				'media-upload',
				'thickbox'
			) );
		wp_enqueue_script( 'bmabImageUploaderJs' );

		wp_register_script( 'bmabAdminJs', plugins_url( 'admin/js/main.js', __DIR__ ),
			array(
				'jquery',
				'bmabNoty'
			) );

		wp_register_script( 'bmabAdminJsGroups', plugins_url( 'admin/js/groups.js', __DIR__ ),
			array(
				'bmabAdminJs'
			) );

		wp_register_script( 'bmabAdminJsItems', plugins_url( 'admin/js/items.js', __DIR__ ),
			array(
				'bmabAdminJs'
			) );

		wp_register_script( 'bmabAdminJsWidgets', plugins_url( 'admin/js/widgets.js', __DIR__ ),
			array(
				'bmabAdminJs'
			) );

		$currency     = $this->app->currency;
		$currencyPre  = $this->app->config->currencyMappings[ $currency ]['pre'];
		$currencyPost = $this->app->config->currencyMappings[ $currency ]['post'];


		wp_localize_script( 'bmabAdminJs', 'BuyMeABeer', array(
			'currencyPre'  => $currencyPre,
			'currencyPost' => $currencyPost
		) );

		// Todo: Minify later.
		wp_enqueue_script( 'bmabAdminJs' );
		wp_enqueue_script( 'bmabAdminJsGroups' );
		wp_enqueue_script( 'bmabAdminJsItems' );
		wp_enqueue_script( 'bmabAdminJsWidgets' );

		/* Admin CSS gets loaded here */
		wp_register_style( 'bmabAdminCss', plugins_url( 'admin/css/main.css', __DIR__ ) );
		wp_enqueue_style( 'bmabAdminCss' );

		wp_enqueue_style( 'thickbox' );

	}

	function adminMenu() {
		add_options_page( 'Buy Me A Beer', 'Buy Me A Beer', 'manage_options', 'buymeabeer', array(
			$this,
			'adminSettingsPage'
		) );
	}

	function adminSettingsPage() {
		require_once plugin_dir_path( __FILE__ ) . 'partials/settings.php';
	}

	public function addPostWidget() {

		add_meta_box(
			'buy-me-a-beer-option',
			'Buy Me A Beer Options',
			array( $this, 'renderPostWidget' ),
			'post',
			'normal',
			'core'
		);

	}

	public function renderPostWidget() {
		$bmabMode   = get_option( 'bmabDisplayMode', 'automatic' );
		$postId     = get_the_ID();
		$bmabActive = $bmabMode == 'manual' ? get_post_meta( $postId, 'bmabActive', true ) : 1;
		//Todo sean: figure this out
//		if(isEditMode) {
//			$postId = get_the_ID();
//			$bmabActive = $bmabMode == 'manual' ? get_post_meta($postId, 'bmabActive', true) : 1;
//		}

		$bmabWidgets = $this->widget_repo->getAll();
		require_once plugin_dir_path( __FILE__ ) . 'partials/createPost.php';
	}

	public function savePostWidget( $postId ) {
		$bmabMode   = get_option( 'bmabDisplayMode', 'automatic' );
		$bmabActive = isset( $_REQUEST['bmabActive'] ) ? $_REQUEST['bmabActive'] : null;
		$widgetId   = isset( $_REQUEST['bmabWidgetId'] ) ? $_REQUEST['bmabWidgetId'] : null;

		update_post_meta( $postId, 'bmabWidgetId', $widgetId );

		if ( $bmabMode == 'manual' ) {
			update_post_meta( $postId, 'bmabActive', $bmabActive );
		}
	}
}