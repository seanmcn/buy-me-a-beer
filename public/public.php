<?php
require_once( plugin_dir_path( __DIR__ ) . "includes/config.php" );

class BuyMeABeerPublic {
	private $version;

	// public $currencyMappings = $currencyMappings;

	public function __construct( $version ) {
		$this->version      = $version;
		$this->bmabCurrency = get_option( 'bmabCurrency', 'USD' );
	}

	public function displayPostWidget( $content ) {
		if ( ! is_home() ) {
			wp_register_script( 'bmabJs', WP_PLUGIN_URL . '/buymeabeer/public/js/main.js', array( 'jquery' ) );
			wp_enqueue_script( 'bmabJs' );

			wp_register_style( 'bmabCss', WP_PLUGIN_URL . '/buymeabeer/public/css/main.css' );
			wp_enqueue_style( 'bmabCss' );

			$postId = get_the_ID();

			$bmabMode = get_option( 'bmabDisplayMode', 'automatic' );

			$bmabActive = $bmabMode == 'manual' ? get_post_meta( $postId, 'bmabActive', true ) : 1;

			$descriptionId = get_post_meta( $postId, 'bmabDescriptionId', true );
			if ( $bmabActive == 1 ) {
				$pqs = $this->getPQs();
				if ( $descriptionId !== "" ) {
					$descriptionFull = $this->getDescription( $descriptionId ) !== null ? $this->getDescription( $descriptionId ) :
						$this->getDefaultDescription();
				} else {
					$descriptionFull = $this->getDefaultDescription();
				}

				$title       = $descriptionFull->title;
				$description = $descriptionFull->description;
				$image       = $descriptionFull->image;

				ob_start();
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/postWidget.php';
				$template = ob_get_contents();
				$content .= $template;
				ob_end_clean();
			}
		}

		return $content;

	}

	function getDescription( $id ) {
		global $wpdb;
		$table       = $wpdb->prefix . DESCRIPTIONS_TABLE;
		$description = $wpdb->get_row( "SELECT * FROM $table WHERE id=$id" );

		return $description;
	}

	function getDefaultDescription() {
		global $wpdb;
		$table       = $wpdb->prefix . DESCRIPTIONS_TABLE;
		$description = $wpdb->get_row( "SELECT * FROM $table WHERE default_option=1" );

		return $description;
	}

	function getPQs() {
		global $wpdb;
		$table = $wpdb->prefix . PRICEQUANITY_TABLE;
		$pqs   = $wpdb->get_results( "SELECT * FROM $table" );
		foreach ( $pqs as $key => $value ) {
			$pqs[ $key ]->price = $this->formatAsCurrency( $value->price );
		}

		return $pqs;
	}

	function formatAsCurrency( $value ) {
		global $currencyMappings;
		$currency = $this->bmabCurrency;
		$newValue = $currencyMappings[ $currency ]['pre'] . $value . $currencyMappings[ $currency ]['post'];

		return $newValue;
	}
}