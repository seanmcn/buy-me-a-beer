<?php
require_once( plugin_dir_path( __DIR__ ) . "includes/config.php" );
//require_once( plugin_dir_path( __DIR__ ) . "payment_services/paypal.php" );
/**
 * Class BuyMeABeerAdminAjax
 */
class BuyMeABeerAdminAjax {

	/**
	 * @var BuyMeABeerAdmin
	 */
	protected $bmabAdmin;


	/**
	 * BuyMeABeerAjax constructor.
	 *
	 * @param BuyMeABeerAdmin $bmabAdmin
	 */
	function __construct( BuyMeABeerAdmin $bmabAdmin ) {
		$this->bmabAdmin = $bmabAdmin;
	}

	/**
	 *
	 */
	function formHandler() {
		$action = isset( $_REQUEST['run'] ) ? $_REQUEST['run'] : null;

		switch ( $action ) {
			case "saveSettings":
				$paypalEmail    = isset( $_REQUEST['paypalEmail'] ) ? $_REQUEST['paypalEmail'] : null;
				$paypalMode     = isset( $_REQUEST['paypalMode'] ) ? $_REQUEST['paypalMode'] : null;
				$paypalClientId = isset( $_REQUEST['paypalClientId'] ) ? $_REQUEST['paypalClientId'] : null;
				$paypalSecret   = isset( $_REQUEST['paypalSecret'] ) ? $_REQUEST['paypalSecret'] : null;
				$currency       = isset( $_REQUEST['currency'] ) ? $_REQUEST['currency'] : null;
				$displayMode    = isset( $_REQUEST['displayMode'] ) ? $_REQUEST['displayMode'] : null;

				$this->bmabAdmin->updateSettings( $paypalEmail, $paypalMode, $paypalClientId, $paypalSecret, $currency,
					$displayMode );
				$message = [ "message" => "Settings saved", "type" => "success" ];
				echo json_encode( $message );
				break;

			case "addDescription":
				$title       = isset( $_REQUEST['title'] ) ? $_REQUEST['title'] : null;
				$description = isset( $_REQUEST['description'] ) ? $_REQUEST['description'] : null;
				$image       = isset( $_REQUEST['image'] ) ? $_REQUEST['image'] : null;

				if ( $title == null || $description == null ) {
					$error = [ "message" => "Title and Description are required!", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$this->bmabAdmin->addDescription( $title, $description, $image );
					$message = [ "message" => "'Title / Description' created", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "editDescription":
				$id          = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				$title       = isset( $_REQUEST['title'] ) ? $_REQUEST['title'] : null;
				$description = isset( $_REQUEST['description'] ) ? $_REQUEST['description'] : null;
				$image       = isset( $_REQUEST['image'] ) ? $_REQUEST['image'] : null;

				if ( $id == null || $title == null || $description == null ) {
					$error = [ "message" => "Title and Description are required!", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$this->bmabAdmin->updateDescription( $id, $title, $description, $image );
					$message = [ "message" => "'Title / Description' saved!", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "defaultDescription":
				$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;

				if ( $id == null ) {
					$error = [ "message" => "Error setting default description", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$this->bmabAdmin->makeDefaultPQ( $id );
					$message = [ "message" => "Default option set!", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "deleteDescription":
				$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				if ( $id == null ) {
					$error = [ "message" => "Error", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$this->bmabAdmin->deleteDescription( $id );
					$message = [ "message" => "'Title / Description' has been deleted!", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "addPQ":
				$name  = isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : null;
				$price = isset( $_REQUEST['price'] ) ? $_REQUEST['price'] : null;
				if ( $name == null || $price == null ) {
					$error = [ "message" => "Name and Price are required!", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$this->bmabAdmin->addPQ( $name, $price );
					$message = [ "message" => "'Quantity / Price' created", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "editPQ":
				$id    = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				$name  = isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : null;
				$price = isset( $_REQUEST['price'] ) ? $_REQUEST['price'] : null;

				if ( $id == null || $name == null || $price == null ) {
					$error = [ "message" => "Name and Price are required!", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$this->bmabAdmin->updatePQ( $id, $name, $price );
					$message = [ "message" => "'Quantity / Price' saved", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "deletePQ":
				$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				if ( $id == null ) {
					$error = [
						"message" => "You didn't specify which 'Quantity / Price' to delete",
						"type"    => "error"
					];
					echo json_encode( $error );
				} else {
					$this->bmabAdmin->deletePQ( $id );
					$message = [ "message" => "'Quantity / Price' has been deleted!", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			default:
				$error = [ "message" => "Error", "type" => "error" ];
				echo json_encode( $error );
				break;
		}
		wp_die(); // this is required to terminate immediately and return a proper response
	}

	function contentHandler() {
		$action = isset( $_REQUEST['run'] ) ? $_REQUEST['run'] : null;

		switch ( $action ) {
			case "bmabPQ":
				echo $this->bmabAdmin->getPQs();
				break;

			case "bmabEditPQ":
				$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				if ( $id !== null ) {
					echo $this->bmabAdmin->getPQ( $id );
				}
				break;

			case "bmabDescriptions":
				echo $this->bmabAdmin->getDescriptions();
				break;

			case "bmabEditDescription":
				$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				if ( $id !== null ) {
					echo $this->bmabAdmin->getDescription( $id );
				}
				break;

			case "bmabPayments":
				echo $this->bmabAdmin->getPayments();
				break;

			default :
				echo "Error: What are you up to?";

				break;

		}
		wp_die(); // this is required to terminate immediately and return a proper response
	}
}