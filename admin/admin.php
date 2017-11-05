<?php
require_once( plugin_dir_path( __DIR__ ) . "includes/config.php" );

class BuyMeABeerAdmin {

	private $version;

	public function __construct( $version ) {
		$this->version      = $version;
		$this->bmabCurrency = get_option( 'bmabCurrency', 'USD' );
	}

	function getWidget( $id ) {
		global $wpdb, $bmabConfig;
		$table  = $wpdb->prefix . $bmabConfig->tables['widgets'];
		$widget = $wpdb->get_row( "SELECT * FROM $table WHERE id=$id" );

		return $widget;
	}

	function getWidgets() {
		global $wpdb, $bmabConfig;
		$table   = $wpdb->prefix . $bmabConfig->tables['widgets'];
		$widgets = $wpdb->get_results( "SELECT * FROM $table" );

		return $widgets;
	}

	function addWidget( $title, $description, $image ) {
		global $wpdb, $bmabConfig;
		$table = $wpdb->prefix . $bmabConfig->tables['widgets'];

		$wpdb->insert(
			$table,
			array(
				'title'       => $title,
				'description' => $description,
				'image'       => $image
			),
			array(
				'%s',
				'%s',
				'%s'
			)
		);

		// Check to see if there is a default already and if not make this one the default
		$wpdb->query( "SELECT * FROM $table WHERE default_option='1'" );
		if ( $wpdb->num_rows == 0 ) {
			$id = $wpdb->insert_id;
			$this->makeDefaultWidget( $id );
		}

		return true;
	}

	function makeDefaultWidget( $id ) {
		global $wpdb, $bmabConfig;
		$table = $wpdb->prefix . $bmabConfig->tables['widgets'];

		//Check if there is a default and remove it first
		$currentDefault = $wpdb->query( "SELECT * FROM $table WHERE default_option" );
		if ( $wpdb->num_rows != 0 ) {
			$currentDefaultId = $currentDefault['id'];
			$wpdb->update( $table,
				array(
					'is_default' => 0,
				),
				array(
					'id' => $currentDefaultId
				),
				array(
					'%d',
				),
				array( '%d' )
			);
		}
		// Update the new default
		$wpdb->update( $table,
			array(
				'is_default' => 1,
			),
			array(
				'id' => $id
			),
			array(
				'%d',
			),
			array( '%d' )
		);

	}

	function updateWidget( $id, $title, $description, $image ) {
		global $wpdb, $bmabConfig;
		$table = $wpdb->prefix . $bmabConfig->tables['widgets'];

		$wpdb->update( $table,
			array(
				'title'       => $title,
				'description' => $description,
				'image'       => $image
			),
			array(
				'id' => $id
			),
			array(
				'%s',
				'%s',
				'%s'
			),
			array( '%d' )
		);

		return true;
	}

	function deleteWidget( $id ) {
		global $wpdb, $bmabConfig;
		$table = $wpdb->prefix . $bmabConfig->tables['widgets'];
		$wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );

		return true;
	}

	function getItem( $id ) {
		global $wpdb, $bmabConfig;
		$table       = $wpdb->prefix . $bmabConfig->tables['items'];
		$description = $wpdb->get_row( "SELECT * FROM $table WHERE id=$id" );

		return $description;
	}

	function getItems() {
		global $wpdb, $bmabConfig;
		$table = $wpdb->prefix . $bmabConfig->tables['items'];
		$items = $wpdb->get_results( "SELECT * FROM $table" );

		foreach ( $items as $key => $value ) {
			$items[ $key ]->price = $this->formatAsCurrency( $value->price );
		}

		return $items;
	}

	function formatAsCurrency( $value ) {
		global $currencyMappings;
		$currency = $this->bmabCurrency;
		$newValue = $currencyMappings[ $currency ]['pre'] . $value . $currencyMappings[ $currency ]['post'];

		return $newValue;
	}

	function addItem( $name, $price ) {
		global $wpdb, $bmabConfig;
		$table = $wpdb->prefix . $bmabConfig->tables['items'];

		$wpdb->insert( $table,
			array(
				'name'  => $name,
				'price' => $price
			),
			array(
				'%s',
				'%d'
			)
		);

		return true;
	}

	function updateItem( $id, $name, $price ) {
		global $wpdb, $bmabConfig;
		$table = $wpdb->prefix . $bmabConfig->tables['items'];

		$wpdb->update( $table,
			array(
				'name'  => $name,
				'price' => $price
			),
			array(
				'id' => $id
			),
			array(
				'%s',
				'%d'
			),
			array(
				'%d'
			)
		);

		return true;
	}

	function deleteItem( $id ) {
		global $wpdb, $bmabConfig;
		$table = $wpdb->prefix . $bmabConfig->tables['items'];
		$wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );

		return true;
	}

	function updateSettings(
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
			'bmabPaypalEmail' => $paypalEmail,
			'bmabPaypalMode' => $paypalMode,
			'bmabPaypalClientId' => $paypalClientId,
			'bmabPaypalSecret' => $paypalSecret,
			'bmabCurrency' => $currency,
			'bmabDisplayMode' => $displayMode,
			'bmabSuccessPage' => $successPage,
			'bmabErrorPage' => $errorPage
		);

		// Add/Update each setting as a wordpress option
		foreach ( $settings as $setting => $value ) {

			if ( get_option( $setting ) !== false ) {
				update_option( $setting, $value );
			} else {
				$deprecated = null;
				$autoload   = 'no';
				add_option( $setting, $value, $deprecated, $autoload );
			}
		}

		return true;

	}

	public function getPayments() {
		global $wpdb, $bmabConfig;
		$paymentTable = $wpdb->prefix . $bmabConfig->tables['payments'];
		$widgetTable  = $wpdb->prefix . $bmabConfig->tables['widgets'];
		$payments     = $wpdb->get_results( "SELECT * FROM $paymentTable LEFT JOIN $widgetTable ON $paymentTable.widget_id=$widgetTable.id" );

		return $payments;
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
		$this->addItem( "1 Beer", 3 );
		$this->addItem( "6 Beers", 15 );
		$this->addItem( "12 Beers", 25 );
		$this->addItem( '36 Beers', 45 );
		$this->addItem( "Keg of Beer", 125 );

		// Todo add group 'Default'
		$this->addWidget( 'Did I help you out?', 'If so how about buying me some beer?', '' );
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
		$currency = $this->bmabCurrency;
		$currencyPre = $currencyMappings[ $currency ]['pre'];
		$currencyPost = $currencyMappings[ $currency ]['post'];
		wp_localize_script( 'bmabAdminJs', 'BuyMeABeer', array(
			'currencyPre' =>  $currencyPre,
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

		$bmabWidgets = $this->getWidgets();
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