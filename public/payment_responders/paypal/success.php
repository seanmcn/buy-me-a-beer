<?php
require_once( plugin_dir_path( __DIR__ ) . "payment_services/paypal.php" );

if ( $_REQUEST ) {

	$paymentId = $_REQUEST['paymentId'];
	$token     = $_REQUEST['token'];
	$payerId   = $_REQUEST['PayerID'];

	$paypal = new BuyMeABeerPaypal();
	$paypal->executePayment( $paymentId, $payerId );
}