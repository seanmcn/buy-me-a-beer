<?php
//require_once( plugin_dir_path( __DIR__ ) . "includes/config.php" );
require_once( plugin_dir_path( __DIR__ ) . "payment_services/paypal.php" );

/**
 * Class BuyMeABeerPublicAjax
 */
class BuyMeABeerPublicAjax {

	function formHandler() {
		$widgetId = isset( $_REQUEST['bmabWidgetId'] ) ? (int) $_REQUEST['bmabWidgetId'] : null;
		$itemId   = isset( $_REQUEST['bmabItemId'] ) ? (int) $_REQUEST['bmabItemId'] : null;
		$location = isset( $_REQUEST['bmabLocation'] ) ? $_REQUEST['bmabLocation'] : null;

		//open connection
		try {
			if ( $widgetId !== null && $itemId !== null ) {
				// Todo Sean: Different payment providers here
				$paypal = new BuyMeABeerPaypal();
				echo json_encode( $paypal->createPayment( $widgetId, $itemId, $location ) );
				wp_die();
			} else {
				error_log( "Error: The 'Buy Me A Beer' wp plugin failed to receive a selected item or widget id" );
			}
		} catch ( Exception $e ) {
			error_log( "Error: The 'Buy Me A Beer' wp plugin has encountered an exception while creating a payment with Paypal: " );
			error_log( $e );
		}

		echo json_encode(false);
		wp_die();
	}
}