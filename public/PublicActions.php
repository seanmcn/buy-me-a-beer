<?php

namespace bmab;


class PublicActions {


	/**
	 * @var App $app
	 */
	private $app;

	/** @var ItemRepository $itemsRepo */
	protected $itemsRepo;

	/**
	 * BuyMeABeerPublic constructor.
	 *
	 * @param $app
	 */
	public function __construct( $app ) {
		$this->app       = $app;
		$this->itemsRepo = $this->app->repos['items'];
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

			$widgetId = get_post_meta( $postId, 'bmabWidgetId', true );
			$widgetId = empty( $widgetId ) ? false : $widgetId;

			if ( ! is_page() || $bmabMode == 'automatic-all' ) {
				if ( $bmabActive == 1 && ! is_page( 'bmab-success' ) ) {

					/** @var WidgetRepository $repo */
					$repo   = $this->app->repos['widgets'];
					$widget = $repo->get( $widgetId );
					$widget = $widget ? $widget : $repo->getDefaultWidget();

					// todo : add to only get items for widgets
					$items = $this->itemsRepo->getAllFormatted();

					$title       = $widget->title;
					$description = $widget->description;
					$image       = $widget->image;
					$widgetId    = $widget->id;

					ob_start();
					require_once plugin_dir_path( __DIR__ ) . 'public/partials/postWidget.php';
					$template = ob_get_contents();
					$content  .= $template;
					ob_end_clean();
				}
			}

		}

		return $content;

	}

	/**
	 * @return string
	 */
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
			$widgetId = get_post_meta( $postId, 'bmabWidgetId', true );
			$widgetId = empty( $widgetId ) ? false : $widgetId;

			/** @var WidgetRepository $repo */
			$repo   = $this->app->repos['widget'];
			$widget = $repo->get( $widgetId );
			$widget = $widget ? $widget : $repo->getDefaultWidget();

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


	/**
	 * Wordpress makes use of this.
	 */
	public function session() {
		if ( ! session_id() ) {
			session_start();
		}
	}
}