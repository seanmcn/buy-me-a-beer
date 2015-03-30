<?php
class BuyMeABeerPublic {

    private $version;

    public function __construct( $version ) {
        $this->version = $version;
    }

    public function displayPostWidget( $content ) {

        ob_start();
        $title = '';
        $description = '';
        $options = '';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/postWidget.php';
        $template = ob_get_contents();
        $content .= $template;

        ob_end_clean();

        return $content;

    }
}