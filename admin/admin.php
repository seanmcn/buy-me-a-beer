<?php
require_once( plugin_dir_path( __DIR__ ) . "includes/config.php" );

class BuyMeABeerAdmin {

	private $version;

	public function __construct( $version ) {
		$this->version      = $version;
		$this->bmabCurrency = get_option( 'bmabCurrency', 'USD' );
	}

	function getTitlesAndDescriptions() {
		global $wpdb;
		$table                 = $wpdb->prefix . DESCRIPTIONS_TABLE;
		$titlesAndDescriptions = $wpdb->get_results( "SELECT * FROM $table" );

		return $titlesAndDescriptions;
	}

	function getDescription( $id ) {
		global $wpdb;
		$table       = $wpdb->prefix . DESCRIPTIONS_TABLE;
		$description = $wpdb->get_row( "SELECT * FROM $table WHERE id=$id" );
		$json        = json_encode( $description );

		return $json;
	}

	function getDescriptions() {
		global $wpdb;
		$table        = $wpdb->prefix . DESCRIPTIONS_TABLE;
		$descriptions = $wpdb->get_results( "SELECT * FROM $table" );
		$json         = json_encode( $descriptions );

		return $json;
	}

	function addDescription( $title, $description, $image ) {
		global $wpdb;
		$table = $wpdb->prefix . DESCRIPTIONS_TABLE;

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
			$this->makeDefaultPQ( $id );
		}

		return true;
	}

	function makeDefaultPQ( $id ) {
		global $wpdb;
		$table = $wpdb->prefix . DESCRIPTIONS_TABLE;

		//Check if there is a default and remove it first
		$currentDefault = $wpdb->query( "SELECT * FROM $table WHERE default_option='1'" );
		if ( $wpdb->num_rows != 0 ) {
			$currentDefaultId = $currentDefault['id'];
			$wpdb->update( $table,
				array(
					'default_option' => 0,
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
				'default_option' => 1,
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

	function updateDescription( $id, $title, $description, $image ) {
		global $wpdb;
		$table = $wpdb->prefix . DESCRIPTIONS_TABLE;

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

	function deleteDescription( $id ) {
		global $wpdb;
		$table = $wpdb->prefix . DESCRIPTIONS_TABLE;
		$wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );

		return true;
	}

	function getPQ( $id ) {
		global $wpdb;
		$table       = $wpdb->prefix . PRICEQUANITY_TABLE;
		$description = $wpdb->get_row( "SELECT * FROM $table WHERE id=$id" );
		$json        = json_encode( $description );

		return $json;
	}

	function getPQs() {
		global $wpdb;
		$table = $wpdb->prefix . PRICEQUANITY_TABLE;
		$pqs   = $wpdb->get_results( "SELECT * FROM $table" );

		foreach ( $pqs as $key => $value ) {
			$pqs[ $key ]->price = $this->formatAsCurrency( $value->price );
		}

		$json = json_encode( $pqs );

		return $json;
	}

	function formatAsCurrency( $value ) {
		global $currencyMappings;
		$currency = $this->bmabCurrency;
		$newValue = $currencyMappings[ $currency ]['pre'] . $value . $currencyMappings[ $currency ]['post'];

		return $newValue;
	}

	function addPQ( $name, $price ) {
		global $wpdb;
		$table = $wpdb->prefix . PRICEQUANITY_TABLE;

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

	function updatePQ( $id, $name, $price ) {
		global $wpdb;
		$table = $wpdb->prefix . PRICEQUANITY_TABLE;

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

	function deletePQ( $id ) {
		global $wpdb;
		$table = $wpdb->prefix . PRICEQUANITY_TABLE;
		$wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );

		return true;
	}

	function updateSettings( $paypalEmail, $paypalMode, $paypalClientId, $paypalSecret, $currency, $displayMode ) {
		$settings = array(
			'bmabPaypalEmail'    => $paypalEmail,
			'bmabPaypalMode'     => $paypalMode,
			'bmabPaypalClientId' => $paypalClientId,
			'bmabPaypalSecret'   => $paypalSecret,
			'bmabCurrency'       => $currency,
			'bmabDisplayMode'    => $displayMode
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
		global $wpdb;
		$table    = $wpdb->prefix . PAYMENTS_TABLE;
		$descripTable = $wpdb->prefix. DESCRIPTIONS_TABLE;
		$payments = $wpdb->get_results( "SELECT * FROM $table LEFT JOIN $descripTable ON $table.description_id=$descripTable.id" );

		/* Format the $payments['linkedFrom'] & $payments['descriptionTitle'] here */
		$json = json_encode( $payments );

		return $json;
	}

	public function installation() {
		global $wpdb;
		global $dbVersion;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$paymentsTable      = $wpdb->prefix . PAYMENTS_TABLE;
		$priceQuantityTable = $wpdb->prefix . PRICEQUANITY_TABLE;
		$descriptionsTable  = $wpdb->prefix . DESCRIPTIONS_TABLE;

		$charset_collate = $wpdb->get_charset_collate();

		$priceQuantitySql = "CREATE TABLE $priceQuantityTable (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name varchar(300),
		price text,
		UNIQUE KEY id (id)
	) $charset_collate;";

		dbDelta( $priceQuantitySql );

		$descriptionsSql = "CREATE TABLE $descriptionsTable (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		title varchar(300),
		description text,
		image varchar(300),
		default_option int(1) DEFAULT 0 NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

		dbDelta( $descriptionsSql );

		$paymentsSql = "CREATE TABLE $paymentsTable (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		paypal_id varchar(300),
		amount int(100),
		email varchar(300),
		first_name varchar(300),
		last_name varchar(300),
		address text,
		payment_method varchar(300),
		description_id int(100),
		url text,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

		dbDelta( $paymentsSql );

		add_option( 'bmabDatabaseVersion', $dbVersion );

		$this->addPQ( "1 Beer", 3 );
		$this->addPQ( "6 Beers", 15 );
		$this->addPQ( "12 Beers", 25 );
		$this->addPQ( '36 Beers', 45 );
		$this->addPQ( "Keg of Beer", 125 );

		$this->addDescription( 'Did I help you out?', 'If so how about buying me some beer?', '' );
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

		$titlesAndDescriptions = $this->getTitlesAndDescriptions();
		require_once plugin_dir_path( __FILE__ ) . 'partials/postManager.php';
	}

	public function savePostWidget( $postId ) {
		$bmabMode   = get_option( 'bmabDisplayMode', 'automatic' );
		$option     = isset( $_REQUEST['bmabTitleDescripID'] ) ? $_REQUEST['bmabTitleDescripID'] : null;
		$bmabActive = isset( $_REQUEST['bmabActive'] ) ? $_REQUEST['bmabActive'] : null;
		update_post_meta( $postId, 'bmabDescriptionId', $option );
		if ( $bmabMode == 'manual' ) {
			update_post_meta( $postId, 'bmabActive', $bmabActive );
		}
	}
}