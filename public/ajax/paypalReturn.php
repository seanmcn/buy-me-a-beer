<?php
require_once( "../../../../../wp-load.php" );
require_once(ABSPATH. "wp-content/plugins/buymeabeer/public/paypal.php");

if($_REQUEST) {

    $paymentId = $_REQUEST['paymentId'];
    $token = $_REQUEST['token'];
    $payerId = $_REQUEST['PayerID'];

    $paypal = new BuyMeABeerPaypal();
    $paypal->executePayment($paymentId, $payerId);
}
?>