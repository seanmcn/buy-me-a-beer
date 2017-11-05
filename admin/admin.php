<?php

namespace bmab;

class BuyMeABeerAdmin {

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

	public function installation() {
		global $wpdb, $bmabConfig;
		global $dbVersion;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$charset = $wpdb->get_charset_collate();

		// Group Table
		$table = $wpdb->prefix . $bmabConfig->tables['groups'];
		$sql   = "CREATE TABLE $table (
			id int NOT NULL AUTO_INCREMENT,
			name VARCHAR(300),
			UNIQUE KEY id(id)
		) $charset;";

		// Item Table
		$table = $wpdb->prefix . $bmabConfig->tables['items'];
		$sql   .= "CREATE TABLE $table (
			id INT NOT NULL AUTO_INCREMENT,
			name VARCHAR(300),
			price INT NOT NULL,
			UNIQUE KEY id(id)
		) $charset;";

		// Item Groups Table
		$table = $wpdb->prefix . $bmabConfig->tables['item_groups'];
		$sql   .= "CREATE TABLE $table (
			item_id INT NOT NULL,
			group_id INT NOT NULL,
			PRIMARY KEY(item_id, group_id)
		) $charset;";

		// Payments Table
		$table = $wpdb->prefix . $bmabConfig->tables['payments'];
		$sql   .= "CREATE TABLE $table (
			id INT NOT NULL AUTO_INCREMENT,
			paypal_id varchar(300),
			amount int(100),
			email varchar(300),
			first_name varchar(300),
			last_name varchar(300),
			address text,
			payment_method varchar(300),
			widget_id int(100),
			url text,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			UNIQUE KEY id (id)
		) $charset;";

		// Widget Table
		$table = $wpdb->prefix . $bmabConfig->tables['widgets'];
		$sql   .= "CREATE TABLE $table (
			id INT NOT NULL AUTO_INCREMENT,
			name VARCHAR(300),
			title VARCHAR(300),
			description text,
			image VARCHAR(300),
			is_default BOOLEAN,
			UNIQUE KEY id(id)
		) $charset;";

		// Widget Group Table
		$table = $wpdb->prefix . $bmabConfig->tables['widget_groups'];
		$sql   .= "CREATE TABLE $table (
			widget_id INT NOT NULL,
			group_id INT NOT NULL,
			PRIMARY KEY(widget_id, group_id)
		) $charset;";

		// Run queries and store schema version.
		dbDelta( $sql );
		add_option( 'bmabDatabaseVersion', $dbVersion );

		// Seed the database
		$this->item_repo->create( "1 Beer", 3 );
		$this->item_repo->create( "6 Beers", 15 );
		$this->item_repo->create( "12 Beers", 25 );
		$this->item_repo->create( '36 Beers', 45 );
		$this->item_repo->create( "Keg of Beer", 125 );

		// Todo add group 'Default'
		$this->widget_repo->create( 'Did I help you out?', 'If so how about buying me some beer?', '' );
	}

	/**
	 * Front end CSS
	 */
	public function adminEnqueueStyles() {

	}

	public function adminEnqueueScripts() {
		global $currencyMappings;
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

		wp_register_script( 'bmabImageUploaderJs', plugins_url( 'admin/js/imageUploader.js', __DIR__ ),
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
		$currency     = $this->app->currency;
		$currencyPre  = $currencyMappings[ $currency ]['pre'];
		$currencyPost = $currencyMappings[ $currency ]['post'];
		wp_localize_script( 'bmabAdminJs', 'BuyMeABeer', array(
			'currencyPre'  => $currencyPre,
			'currencyPost' => $currencyPost
		) );

		wp_enqueue_script( 'bmabAdminJs' );

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
		require_once plugin_dir_path( __FILE__ ) . 'partials/settingsManager.php';
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
		require_once plugin_dir_path( __FILE__ ) . 'partials/postManager.php';
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