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
				$successPage    = isset( $_REQUEST['successPage'] ) ? $_REQUEST['successPage'] : null;
				$errorPage      = isset( $_REQUEST['errorPage'] ) ? $_REQUEST['errorPage'] : null;

				$this->bmabAdmin->updateSettings( $paypalEmail, $paypalMode, $paypalClientId, $paypalSecret, $currency,
					$displayMode, $successPage, $errorPage );
				$message = [ "message" => "Settings saved", "type" => "success" ];
				echo json_encode( $message );
				break;

			case "addWidget":
				$title       = isset( $_REQUEST['title'] ) ? $_REQUEST['title'] : null;
				$description = isset( $_REQUEST['description'] ) ? $_REQUEST['description'] : null;
				$image       = isset( $_REQUEST['image'] ) ? $_REQUEST['image'] : null;

				if ( $title == null || $description == null ) {
					$error = [ "message" => "Title and Description are required!", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$this->bmabAdmin->addWidget( $title, $description, $image );
					$message = [ "message" => "Widget created", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "editWidget":
				$id          = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				$title       = isset( $_REQUEST['title'] ) ? $_REQUEST['title'] : null;
				$description = isset( $_REQUEST['description'] ) ? $_REQUEST['description'] : null;
				$image       = isset( $_REQUEST['image'] ) ? $_REQUEST['image'] : null;

				if ( $id == null || $title == null || $description == null ) {
					$error = [ "message" => "Title and Description are required!", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$this->bmabAdmin->updateWidget( $id, $title, $description, $image );
					$message = [ "message" => "Widget saved!", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "defaultWidget":
				$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;

				if ( $id == null ) {
					$error = [ "message" => "Error setting default widget", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$this->bmabAdmin->makeDefaultWidget( $id );
					$message = [ "message" => "Default widget set!", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "deleteWidget":
				$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				if ( $id == null ) {
					$error = [ "message" => "Error", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$this->bmabAdmin->deleteWidget( $id );
					$message = [ "message" => "Widget has been deleted!", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "addItem":
				$name  = isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : null;
				$price = isset( $_REQUEST['price'] ) ? $_REQUEST['price'] : null;
				if ( $name == null || $price == null ) {
					$error = [ "message" => "Name and Price are required!", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$this->bmabAdmin->addItem( $name, $price );
					$message = [ "message" => "Item '$name' created", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "editItem":
				$id    = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				$name  = isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : null;
				$price = isset( $_REQUEST['price'] ) ? $_REQUEST['price'] : null;

				if ( $id == null || $name == null || $price == null ) {
					$error = [ "message" => "Name and Price are required!", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$this->bmabAdmin->updateItem( $id, $name, $price );
					$message = [ "message" => "Item '$name' saved", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "deleteItem":
				$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				if ( $id == null ) {
					$error = [
						"message" => "You didn't specify which item to delete",
						"type"    => "error"
					];
					echo json_encode( $error );
				} else {
					$this->bmabAdmin->deleteItem( $id );
					$message = [ "message" => "Item has been deleted!", "type" => "success" ];
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

		$data = [ "message" => "Something went wrong while making your request!", "type" => "error" ];
		switch ( $action ) {
			case "bmabViewItems":
				$data = $this->bmabAdmin->getItems();
				break;

			case "bmabEditItem":
				$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				if ( $id !== null ) {
					$data = $this->bmabAdmin->getItem( $id );
				}
				break;

			case "bmabViewWidgets":
				$data = $this->bmabAdmin->getWidgets();
				break;

			case "bmabEditWidget":
				$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				if ( $id !== null ) {
					$data = $this->bmabAdmin->getWidget( $id );
				}
				break;

			case "bmabViewPayments":
				$data = $this->bmabAdmin->getPayments();
				break;
		}
		echo json_encode( $data );
		wp_die(); // this is required to terminate immediately and return a proper response
	}
}