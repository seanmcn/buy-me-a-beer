<?php
require_once( "../../../../../wp-load.php" );
require_once(ABSPATH. "wp-content/plugins/buymeabeer/admin/config.php");

$bmab = new BuyMeABeer();
$version = $bmab->getVersion();
$bmabAdmin = new BuyMeABeerAdmin($version);

$action = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;

switch($action) {
    case "bmabPQ":
        echo $bmabAdmin->getPQs();
        break;

    case "bmabDescrip":
        echo $bmabAdmin->getDescriptions();
        break;

    case "bmabEditDescription":
        $editId = isset($_REQUEST['editId']) ? $_REQUEST['editId'] : null;
        echo $bmabAdmin->getDescription($editId);
        break;

    default :
        echo "Error: What are you up to?";

        break;
}