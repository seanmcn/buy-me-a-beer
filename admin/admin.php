<?php
require_once(ABSPATH. "wp-content/plugins/buymeabeer/admin/config.php");

class BuyMeABeerAdmin {

    private $version;

    public function __construct( $version ) {
        $this->version = $version;
    }

    function getTitlesAndDescriptions() {
        global $wpdb;
        $table = $wpdb->prefix . DESCRIPTIONS_TABLE;
        $titlesAndDescriptions = $wpdb->get_results("SELECT * FROM $table");
        return $titlesAndDescriptions;
    }

    function getDescription($id) {
        global $wpdb;
        $table = $wpdb->prefix . DESCRIPTIONS_TABLE;
        $description = $wpdb->get_row("SELECT * FROM $table WHERE id=$id");
        $json = json_encode($description);
        return $json;
    }

    function getDescriptions() {
        global $wpdb;
        $table = $wpdb->prefix . DESCRIPTIONS_TABLE;
        $descriptions = $wpdb->get_results("SELECT * FROM $table");
        $json = json_encode($descriptions);
        return $json;
    }

    function addDescription($title, $description, $image) {
        global $wpdb;
        $table = $wpdb->prefix . DESCRIPTIONS_TABLE;

        $wpdb->insert(
            $table,
            array(
                'title' => $title,
                'description' => $description,
                'image' => $image
            ),
            array(
                '%s',
                '%s',
                '%s'
            )
        );
        return true;
    }

    function updateDescription($id, $title, $description, $image) {
        global $wpdb;
        $table = $wpdb->prefix . DESCRIPTIONS_TABLE;

        $wpdb->update($table,
            array(
                'title' => $title,
                'description' => $description,
                'image' => $image
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

    function deleteDescription($id) {
        global $wpdb;
        $table = $wpdb->prefix . DESCRIPTIONS_TABLE;
        $wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );
        return true;
    }

    function getPQ($id) {
        global $wpdb;
        $table = $wpdb->prefix . PRICEQUANITY_TABLE;
        $description = $wpdb->get_row("SELECT * FROM $table WHERE id=$id");
        $json = json_encode($description);
        return $json;
    }

    function getPQs() {
        global $wpdb;
        $table = $wpdb->prefix . PRICEQUANITY_TABLE;
        $pqs = $wpdb->get_results("SELECT * FROM $table");
        $json = json_encode($pqs);
        return $json;
    }

    function addPQ($name, $price) {
        global $wpdb;
        $table = $wpdb->prefix . PRICEQUANITY_TABLE;

        $wpdb->insert($table,
            array(
                'name' => $name,
                'price' => $price
            ),
            array(
                '%s',
                '%d'
            )
        );
        return true;
    }
    function updatePQ($id, $name, $price) {
        global $wpdb;
        $table = $wpdb->prefix . PRICEQUANITY_TABLE;

        $wpdb->update($table,
            array(
                'name' => $name,
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

    function deletePQ($id) {
        global $wpdb;
        $table = $wpdb->prefix . PRICEQUANITY_TABLE;
        $wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );
        return true;
    }

    function updateSettings($paypalEmail, $paypalMode, $paypalClientId, $paypalSecret, $currency){
        $settings = array(
            'bmabPaypalEmail' => $paypalEmail,
            'bmabPaypalMode' => $paypalMode,
            'bmabPaypalClientId' => $paypalClientId,
            'bmabPaypalSecret' => $paypalSecret,
            'bmabCurrency' => $currency
        );

        // Add / Update each setting as a wordpress option
        foreach($settings as $setting => $value){

            if ( get_option( $setting ) !== false ) {
                update_option( $setting, $value );
            } else {
                $deprecated = null;
                $autoload = 'no';
                add_option( $setting, $value, $deprecated, $autoload );
            }
        }
        return true;

    }

    public function installation() {
        global $wpdb;
        global $dbVersion;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $paymentsTable = $wpdb->prefix . PAYMENTS_TABLE;
        $priceQuantityTable = $wpdb->prefix . PRICEQUANITY_TABLE;
        $descriptionsTable = $wpdb->prefix . DESCRIPTIONS_TABLE;

        $charset_collate = $wpdb->get_charset_collate();

        $priceQuantitySql =  "CREATE TABLE $priceQuantityTable (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name varchar(300),
		price text,
		UNIQUE KEY id (id)
	) $charset_collate;";

        dbDelta($priceQuantitySql);

        $descriptionsSql = "CREATE TABLE $descriptionsTable (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		title varchar(300),
		description text,
		image varchar(300),
		default_option int(1) DEFAULT 0 NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

        dbDelta($descriptionsSql);

        $paymentsSql = "CREATE TABLE $paymentsTable (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		paypal_id varchar(300),
		email varchar(300),
		first_name varchar(300),
		last_name varchar(300),
		address text,
		payment_method varchar(300),
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

        dbDelta($paymentsSql);

        add_option( 'bmabDatabaseVersion', $dbVersion );
    }

    /**
     * Front end CSS
     */
    public function adminEnqueueStyles() {

    }

    public function adminEnqueueScripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('thickbox');
        wp_enqueue_script( 'media-upload');
        wp_enqueue_media();

        wp_register_script('bmabImageUploaderJs',WP_PLUGIN_URL.'/buymeabeer/admin/js/imageUploader.js', array
        ('jquery','media-upload','thickbox'));
        wp_enqueue_script('bmabImageUploaderJs');

        wp_register_script('bmabAdminJs',WP_PLUGIN_URL.'/buymeabeer/admin/js/main.js', array('jquery'));
        wp_enqueue_script('bmabAdminJs');

        /* Admin CSS needs to be loaded here */
        wp_register_style('bmabAdminCss', WP_PLUGIN_URL .'/buymeabeer/admin/css/main.css');
        wp_enqueue_style('bmabAdminCss');

        wp_enqueue_style('thickbox');

    }
    function adminMenu () {
        add_options_page( 'Buy Me A Beer','Buy Me A Beer','manage_options','buymeabeer', array( $this,
            'adminSettingsPage'
        ) );
    }

    function  adminSettingsPage () {
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
        $titlesAndDescriptions = $this->getTitlesAndDescriptions();
        require_once plugin_dir_path( __FILE__ ) . 'partials/postManager.php';
    }

    public function savePostWidget($postId) {
        $option = $_REQUEST['bmabTitleDescripID'];
        update_post_meta($postId, 'bmabDescriptionId', $option);
    }
}