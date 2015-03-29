<?php
require_once( "../../../../../wp-load.php" );
require_once(ABSPATH. "wp-content/plugins/buymeabeer/admin/config.php");

$bmab = new BuyMeABeer();
$version = $bmab->getVersion();
$bmabAdmin = new BuyMeABeerAdmin($version);

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;

switch($action) {
    case "saveSettings":
        $paypalMode = isset($_REQUEST['paypalMode']) ? $_REQUEST['paypalMode'] : null;
        $paypalClientId = isset($_REQUEST['paypalClientId']) ? $_REQUEST['paypalClientId'] : null;
        $paypalSecret = isset($_REQUEST['paypalSecret']) ? $_REQUEST['paypalSecret'] : null;
        $currency = isset($_REQUEST['currency']) ? $_REQUEST['currency'] : null;

        $bmabAdmin->updateSettings($paypalMode, $paypalClientId, $paypalSecret, $currency);
        $message = [ "message" => "Saved", "type" => "success"];
        return json_encode($message);
        break;

    case "addDescription":
        $title = isset($_REQUEST['title']) ? $_REQUEST['title'] : null;
        $description = isset($_REQUEST['description']) ? $_REQUEST['description'] : null;
        $image = isset($_REQUEST['image']) ? $_REQUEST['image'] : null;

        if($title == null || $description == null) {
            $error = [ "message" => "Title and Description are required!", "type" => "error"];
            return json_encode($error);
        }
        else {
            $bmabAdmin->addDescription($title, $description, $image);
            $message = [ "message" => "Created", "type" => "success"];
            return json_encode($message);
        }
        break;

    case "editDescription":
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
        $title = isset($_REQUEST['title']) ? $_REQUEST['title'] : null;
        $description = isset($_REQUEST['description']) ? $_REQUEST['description'] : null;
        $image = isset($_REQUEST['image']) ? $_REQUEST['image'] : null;

        if($id == null || $title == null || $description == null) {
            $error = [ "message" => "Title and Description are required!", "type" => "error"];
            return json_encode($error);
        }
        else {
            $bmabAdmin->editDescription($title, $description, $image);
            $message = [ "message" => "Saved", "type" => "success"];
            return json_encode($message);
        }
        break;

    case "addPQ":
        $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;
        $price = isset($_REQUEST['price']) ? $_REQUEST['price'] : null;
        if($name == null || $price == null) {
            $error = [ "message" => "Name and Price are required!", "type" => "error"];
            return json_encode($error);
        }
        else {
            $bmabAdmin->addPQ($name, $price);
            $message = [ "message" => "Created", "type" => "success"];
            return json_encode($message);
        }
        break;

    case "editPQ":
        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
        $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;
        $price = isset($_REQUEST['price']) ? $_REQUEST['price'] : null;

        if($id == null || $name == null || $price == null) {
            $error = [ "message" => "Name and Price are required!", "type" => "error"];
            return json_encode($error);
        }
        else {
            $bmabAdmin->editPQ($id, $name, $price);
            $message = [ "message" => "Saved", "type" => "success"];
            return json_encode($message);
        }
        break;

    default:
        echo "Error: What are you up to?";
        break;
}