<?php
//require_once( plugin_dir_path( __DIR__ ) . "includes/config.php" );
require_once( plugin_dir_path( __DIR__ ) . "payment_services/paypal.php" );

/**
 * Class BuyMeABeerPublicAjax
 */
class BuyMeABeerPublicAjax {

	function formHandler() {
		$descriptionId  = isset( $_REQUEST['bmabDescriptionId'] ) ? (int) $_REQUEST['bmabDescriptionId'] : null;
		$selectedOption = isset( $_REQUEST['bmabOption'] ) ? (int) $_REQUEST['bmabOption'] : null;
		$location = isset( $_REQUEST['bmabLocation'] ) ? $_REQUEST['bmabLocation'] : null;

		//open connection
		try {
			if ( $descriptionId !== null && $selectedOption !== null ) {
				// Todo Sean: Different payment providers here
				$paypal = new BuyMeABeerPaypal();
				echo json_encode( $paypal->createPayment( $descriptionId, $selectedOption, $location ) );
				wp_die();
			} else {
				error_log( "Error: The 'Buy Me A Beer' wp plugin failed to receive a selected option ID and/or description ID" );
			}
		} catch ( Exception $e ) {
			error_log( "Error: The 'Buy Me A Beer' wp plugin has encountered an exception while creating a payment with Paypal: " );
			error_log( $e );
		}

		echo json_encode(false);
		wp_die();
	}
}