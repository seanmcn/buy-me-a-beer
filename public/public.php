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

			$widgetId = get_post_meta( $postId, 'bmabItemId', true );
			if ( ! is_page() || $bmabMode == 'automatic-all' ) {
				if ( $bmabActive == 1 && ! is_page( 'bmab-success' ) ) {

					// Todo: really? i assume i had some reasoning?
					if ( $widgetId !== "" ) {
						$widget = $this->getWidget( $widgetId ) !== null ? $this->getWidget( $widgetId ) :
							$this->getDefaultWidget();
					} else {
						$widget = $this->getDefaultWidget();
					}

					// todo : add to only get items for widget
					$items = $this->getItems();

					$title       = $widget->title;
					$description = $widget->description;
					$image       = $widget->image;
					$widgetId    = $widget->id;

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

			$postId   = get_the_ID();
			$widgetId = get_post_meta( $postId, 'bmabItemId', true );

			// todo ??? as above
			if ( $widgetId !== "" ) {
				$widget = $this->getWidget( $widgetId ) !== null ? $this->getWidget( $widgetId ) :
					$this->getDefaultWidget();
			} else {
				$widget = $this->getDefaultWidget();
			}


			$title       = $widget->title;
			$description = $widget->description;
			$image       = $widget->image;
			$widgetId    = $widget->id;

			ob_start();
			require_once plugin_dir_path( __DIR__ ) . 'public/partials/postWidget.php';
			$template = ob_get_contents();
			ob_end_clean();

			return $template;
		}
		return '';
	}


	function getWidget( $id ) {
		global $wpdb, $bmabConfig;
		$table       = $wpdb->prefix . $bmabConfig->tables['widgets'];
		$description = $wpdb->get_row( "SELECT * FROM $table WHERE id=$id" );
		return $description;
	}


	function getDefaultWidget() {
		global $wpdb, $bmabConfig;
		$table       = $wpdb->prefix . $bmabConfig->tables['widgets'];
		$description = $wpdb->get_row( "SELECT * FROM $table WHERE is_default" );

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