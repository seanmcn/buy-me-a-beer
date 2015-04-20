<?php
class BuyMeABeerPublic {

    private $version;

    public function __construct( $version ) {
        $this->version = $version;
    }

    public function displayPostWidget( $content ) {
        wp_register_script( 'bmabJs', WP_PLUGIN_URL . '/buymeabeer/public/js/main.js', array( 'jquery' ) );
        wp_enqueue_script( 'bmabJs' );

        wp_register_style( 'bmabCss', WP_PLUGIN_URL . '/buymeabeer/public/css/main.css' );
        wp_enqueue_style( 'bmabCss' );

        $postId = get_the_ID();
        $descriptionId =  get_post_meta($postId, 'bmabDescriptionId', true);
        if($descriptionId) {
            $pqs = $this->getPQs();
            $descriptionFull = $this->getDescription( $descriptionId );
            $title = $descriptionFull->title;
            $description = $descriptionFull->description;
            $image = $descriptionFull->image;

            ob_start();
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/postWidget.php';
            $template = ob_get_contents();
            $content .= $template;
            ob_end_clean();
        }

        return $content;

    }

    function getDescription($id) {
        global $wpdb;
        $table = $wpdb->prefix . DESCRIPTIONS_TABLE;
        $description = $wpdb->get_row("SELECT * FROM $table WHERE id=$id");
        return $description;
    }
    function getPQs() {
        global $wpdb;
        $table = $wpdb->prefix . PRICEQUANITY_TABLE;
        $pqs = $wpdb->get_results("SELECT * FROM $table");
        return $pqs;
    }
}