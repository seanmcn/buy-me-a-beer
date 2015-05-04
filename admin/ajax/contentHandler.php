<?php
require_once( "../../../../../wp-load.php" );
require_once( ABSPATH . "wp-content/plugins/buymeabeer/includes/config.php" );

$bmab      = new BuyMeABeer();
$version   = $bmab->getVersion();
$bmabAdmin = new BuyMeABeerAdmin( $version );

$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : null;

switch ($action) {

    case "bmabPQ":
        echo $bmabAdmin->getPQs();
        break;

    case "bmabEditPQ":
        $id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
        if ($id !== null) {
            echo $bmabAdmin->getPQ( $id );
        }
        break;

    case "bmabDescriptions":
        echo $bmabAdmin->getDescriptions();
        break;

    case "bmabEditDescription":
        $id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
        if ($id !== null) {
            echo $bmabAdmin->getDescription( $id );
        }
        break;

    case "bmabPayments":
        echo $bmabAdmin->getPayments();
        break;

    default :
        echo "Error: What are you up to?";

        break;

}