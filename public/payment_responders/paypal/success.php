<?php

namespace bmab;
// Todo Sean: figure out how to make these requires not so damn ugly
require_once( "../../../../../../wp-load.php" );
//require_once( plugin_dir_path( realpath( "../../" ) ) . "payment_services/paypal.php" );
require_once( plugin_dir_path( realpath( "../../" ) ) . "includes/App.php" );

if ( $_REQUEST ) {

	if( !session_id()) {
		session_start();
	}
	$paymentId = $_REQUEST['paymentId'];
	$token     = $_REQUEST['token']; //Todo Sean: is this worth saving?
	$payerId   = $_REQUEST['PayerID'];
	$data      =  array_key_exists('bmabPaypal', $_SESSION) ? $_SESSION['bmabPaypal'] : FALSE;

	$app    = new App();
	$paypal = new Paypal( $app );
	$paypal->executePayment( $paymentId, $payerId, $data );
}