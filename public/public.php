<?php
require_once( plugin_dir_path( __DIR__ ) . "includes/config.php" );

/**
 * Class BuyMeABeerPublic
 */
class BuyMeABeerPublic {

	/**
	 * @var float
	 */
	private $version;

	// public $currencyMappings = $currencyMappings;

	/**
	 * BuyMeABeerPublic constructor.
	 *
	 * @param $version
	 */
	public function __construct( $version ) {
		$this->version      = $version;
		$this->bmabCurrency = get_option( 'bmabCurrency', 'USD' );
	}

	/**
	 * @param $content
	 *
	 * @return string
	 */
	public function displayPostWidget( $content ) {
		if ( ! is_home() ) {

			wp_register_script( 'bmabJs', plugins_url( 'public/js/main.js', __DIR__ ), array( 'jquery' ) );

			wp_localize_script( 'bmabJs', 'BuyMeABeer', array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			) );

			wp_enqueue_script( 'bmabJs' );

			wp_register_style( 'bmabCss', plugins_url( 'public/css/main.css', __DIR__ ) );
			wp_enqueue_style( 'bmabCss' );

			$postId = get_the_ID();

			$bmabMode = get_option( 'bmabDisplayMode', 'automatic' );

			$bmabActive = $bmabMode == 'manual' ? get_post_meta( $postId, 'bmabActive', true ) : 1;

			$descriptionId = get_post_meta( $postId, 'bmabDescriptionId', true );
			if ( ! is_page() || $bmabMode == 'automatic-all' ) {
				if ( $bmabActive == 1 && ! is_page( 'bmab-success' ) ) {
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
					require_once plugin_dir_path( __DIR__ ) . 'public/partials/postWidget.php';
					$template = ob_get_contents();
					$content .= $template;
					ob_end_clean();
				}
			}

		}

		return $content;

	}

	public function displayShortCodeWidget() {
		if ( ! is_home() ) {

			wp_register_script( 'bmabJs', plugins_url( 'public/js/main.js', __DIR__ ), array( 'jquery' ) );

			wp_localize_script( 'bmabJs', 'BuyMeABeer', array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			) );

			wp_enqueue_script( 'bmabJs' );

			wp_register_style( 'bmabCss', plugins_url( 'public/css/main.css', __DIR__ ) );
			wp_enqueue_style( 'bmabCss' );

			$postId        = get_the_ID();
			$descriptionId = get_post_meta( $postId, 'bmabDescriptionId', true );

			if ( $descriptionId !== "" ) {
				$descriptionFull = $this->getDescription( $descriptionId ) !== null ? $this->getDescription( $descriptionId ) :
					$this->getDefaultDescription();
			} else {
				$descriptionFull = $this->getDefaultDescription();
			}

			$pqs         = $this->getPQs();
			$title       = $descriptionFull->title;
			$description = $descriptionFull->description;
			$image       = $descriptionFull->image;

			ob_start();
			require_once plugin_dir_path( __DIR__ ) . 'public/partials/postWidget.php';
			$template = ob_get_contents();
			ob_end_clean();

			return $template;
		}

		return '';
	}

	/**
	 * @param integer $id
	 *
	 * @return array|null|object|void
	 */
	function getDescription( $id ) {
		global $wpdb;
		$table       = $wpdb->prefix . DESCRIPTIONS_TABLE;
		$description = $wpdb->get_row( "SELECT * FROM $table WHERE id=$id" );

		return $description;
	}

	/**
	 * @return array|null|object|void
	 */
	function getDefaultDescription() {
		global $wpdb;
		$table       = $wpdb->prefix . DESCRIPTIONS_TABLE;
		$description = $wpdb->get_row( "SELECT * FROM $table WHERE default_option=1" );

		return $description;
	}

	/**
	 * @return array|null|object
	 */
	function getPQs() {
		global $wpdb;
		$table = $wpdb->prefix . PRICEQUANITY_TABLE;
		$pqs   = $wpdb->get_results( "SELECT * FROM $table" );
		foreach ( $pqs as $key => $value ) {
			$pqs[ $key ]->price = $this->formatAsCurrency( $value->price );
		}

		return $pqs;
	}

	/**
	 * @param $value
	 *
	 * @return string
	 */
	function formatAsCurrency( $value ) {
		global $currencyMappings;
		$currency = $this->bmabCurrency;
		$newValue = $currencyMappings[ $currency ]['pre'] . $value . $currencyMappings[ $currency ]['post'];

		return $newValue;
	}

	public function session() {
		if ( ! session_id() ) {
			session_start();
		}
	}
}