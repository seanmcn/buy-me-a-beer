<?php
require_once( "../../../../../wp-load.php" );
require_once( ABSPATH . "wp-content/plugins/buymeabeer/includes/config.php" );

$bmab      = new BuyMeABeer();
$version   = $bmab->getVersion();
$bmabAdmin = new BuyMeABeerAdmin( $version );

$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : null;

switch ($action) {
    case "saveSettings":
        $paypalEmail    = isset( $_REQUEST['paypalEmail'] ) ? $_REQUEST['paypalEmail'] : null;
        $paypalMode     = isset( $_REQUEST['paypalMode'] ) ? $_REQUEST['paypalMode'] : null;
        $paypalClientId = isset( $_REQUEST['paypalClientId'] ) ? $_REQUEST['paypalClientId'] : null;
        $paypalSecret   = isset( $_REQUEST['paypalSecret'] ) ? $_REQUEST['paypalSecret'] : null;
        $currency       = isset( $_REQUEST['currency'] ) ? $_REQUEST['currency'] : null;
        $displayMode    = isset( $_REQUEST['displayMode'] ) ? $_REQUEST['displayMode'] : null;

        $bmabAdmin->updateSettings( $paypalEmail, $paypalMode, $paypalClientId, $paypalSecret, $currency, $displayMode );
        $message = [ "message" => "Settings saved", "type" => "success" ];
        echo json_encode( $message );
        break;

    case "addDescription":
        $title       = isset( $_REQUEST['title'] ) ? $_REQUEST['title'] : null;
        $description = isset( $_REQUEST['description'] ) ? $_REQUEST['description'] : null;
        $image       = isset( $_REQUEST['image'] ) ? $_REQUEST['image'] : null;

        if ($title == null || $description == null) {
            $error = [ "message" => "Title and Description are required!", "type" => "error" ];
            echo json_encode( $error );
        } else {
            $bmabAdmin->addDescription( $title, $description, $image );
            $message = [ "message" => "'Title / Description' created", "type" => "success" ];
            echo json_encode( $message );
        }
        break;

    case "editDescription":
        $id          = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
        $title       = isset( $_REQUEST['title'] ) ? $_REQUEST['title'] : null;
        $description = isset( $_REQUEST['description'] ) ? $_REQUEST['description'] : null;
        $image       = isset( $_REQUEST['image'] ) ? $_REQUEST['image'] : null;

        if ($id == null || $title == null || $description == null) {
            $error = [ "message" => "Title and Description are required!", "type" => "error" ];
            echo json_encode( $error );
        } else {
            $bmabAdmin->updateDescription( $id, $title, $description, $image );
            $message = [ "message" => "'Title / Description' saved!", "type" => "success" ];
            echo json_encode( $message );
        }
        break;

    case "defaultDescription":
        $id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;

        if ($id == null) {
            $error = [ "message" => "Error setting default description", "type" => "error" ];
            echo json_encode( $error );
        } else {
            $bmabAdmin->makeDefaultPQ( $id );
            $message = [ "message" => "Default option set!", "type" => "success" ];
            echo json_encode( $message );
        }
        break;

    case "deleteDescription":
        $id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
        if ($id == null) {
            $error = [ "message" => "Error", "type" => "error" ];
            echo json_encode( $error );
        } else {
            $bmabAdmin->deleteDescription( $id );
            $message = [ "message" => "'Title / Description' has been deleted!", "type" => "success" ];
            echo json_encode( $message );
        }
        break;

    case "addPQ":
        $name  = isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : null;
        $price = isset( $_REQUEST['price'] ) ? $_REQUEST['price'] : null;
        if ($name == null || $price == null) {
            $error = [ "message" => "Name and Price are required!", "type" => "error" ];
            echo json_encode( $error );
        } else {
            $bmabAdmin->addPQ( $name, $price );
            $message = [ "message" => "'Quantity / Price' created", "type" => "success" ];
            echo json_encode( $message );
        }
        break;

    case "editPQ":
        $id    = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
        $name  = isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : null;
        $price = isset( $_REQUEST['price'] ) ? $_REQUEST['price'] : null;

        if ($id == null || $name == null || $price == null) {
            $error = [ "message" => "Name and Price are required!", "type" => "error" ];
            echo json_encode( $error );
        } else {
            $bmabAdmin->updatePQ( $id, $name, $price );
            $message = [ "message" => "'Quantity / Price' saved", "type" => "success" ];
            echo json_encode( $message );
        }
        break;

    case "deletePQ":
        $id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
        if ($id == null) {
            $error = [ "message" => "You didn't specify which 'Quantity / Price' to delete", "type" => "error" ];
            echo json_encode( $error );
        } else {
            $bmabAdmin->deletePQ( $id );
            $message = [ "message" => "'Quantity / Price' has been deleted!", "type" => "success" ];
            echo json_encode( $message );
        }
        break;

    default:
        $error = [ "message" => "Error", "type" => "error" ];
        echo json_encode( $error );
        break;
}