<?php
require_once( "../../../../../wp-load.php" );
require_once(ABSPATH. "wp-content/plugins/buymeabeer/admin/config.php");

$bmab = new BuyMeABeer();
$version = $bmab->getVersion();
$bmabAdmin = new BuyMeABeerAdmin($version);

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;

switch($action) {
    case "saveSettings":
        $bmabAdmin->updateSettings($_REQUEST['paypalMode'], $_REQUEST['paypalClientId'],$_REQUEST['paypalSecret'],
            $_REQUEST['currency']);
        break;

    case "addDescription":
        $bmabAdmin->addDescription($_REQUEST['title'], $_REQUEST['description'], $_REQUEST['image']);
        break;

    case "editDescription":
            //Todo Sean : Handle edit descriptions sending
        break;

    case "addPQ":
        $bmabAdmin->addPQ($_REQUEST['name'], $_REQUEST['price']);
        break;

    case "editPQ":
        //Todo Sean : Handle edit pages sending
        break;

    default:
        echo "Error: What are you up to?";
        break;
}