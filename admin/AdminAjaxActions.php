<?php

namespace bmab;

/**
 * Class AdminAjaxActions
 */
class AdminAjaxActions {


	protected $app;

	/** @var WidgetRepository $widgetRepo */
	protected $widgetRepo;

	/** @var ItemRepository $itemRepo */
	protected $itemRepo;

	/** @var SettingRepository $settingRepo */
	protected $settingRepo;

	/** @var PaymentRepository $paymentRepo */
	protected $paymentRepo;

	/** @var GroupRepository $groupRepo */
	protected $groupRepo;

	/** @var ItemGroupRepository $itemGroupRepo */
	protected $itemGroupRepo;

	function __construct( $app ) {
		$this->app           = $app;
		$this->widgetRepo    = $this->app->repos['widgets'];
		$this->itemRepo      = $this->app->repos['items'];
		$this->settingRepo   = $this->app->repos['settings'];
		$this->paymentRepo   = $this->app->repos['payments'];
		$this->groupRepo     = $this->app->repos['groups'];
		$this->itemGroupRepo = $this->app->repos['itemGroups'];
	}

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

				$this->settingRepo->update( $paypalEmail, $paypalMode, $paypalClientId, $paypalSecret, $currency,
					$displayMode, $successPage, $errorPage );
				$message = [ "message" => "Settings saved", "type" => "success" ];
				echo json_encode( $message );
				break;

			case "addGroup":
				$name = isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : null;

				if ( $name == null ) {
					$error = [ "message" => "Name is required!", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$this->groupRepo->create( $name );
					$message = [ "message" => "Group created", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "editGroup":
				$id   = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				$name = isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : null;

				if ( $id == null || $name == null ) {
					$error = [ "message" => "Name is required!", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$this->groupRepo->update( $id, $name );
					$message = [ "message" => "Group saved!", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "deleteGroup":
				$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				if ( $id == null ) {
					$error = [ "message" => "Error", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$this->groupRepo->delete( $id );
					$message = [ "message" => "Group has been deleted!", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "addItem":
				$name   = isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : null;
				$price  = isset( $_REQUEST['price'] ) ? $_REQUEST['price'] : null;
				$groups = isset( $_REQUEST['groups'] ) ? $_REQUEST['groups'] : null;
				if ( empty( $groups ) ) {
					$error = [ "message" => "At least one group for the item is required", "type" => "error" ];
					echo json_encode( $error );
				} else if ( $name == null || $price == null ) {
					$error = [ "message" => "Name and Price are required!", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$itemId = $this->itemRepo->create( $name, $price );
					$this->itemGroupRepo->save( $itemId, $groups );
					$message = [ "message" => "Item '$name' created", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "editItem":
				$id     = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				$name   = isset( $_REQUEST['name'] ) ? $_REQUEST['name'] : null;
				$price  = isset( $_REQUEST['price'] ) ? $_REQUEST['price'] : null;
				$groups = isset( $_REQUEST['groups'] ) ? $_REQUEST['groups'] : null;

				if ( empty( $groups ) ) {
					$error = [ "message" => "At least one group for the item is required", "type" => "error" ];
					echo json_encode( $error );
				} elseif ( $id == null || $name == null || $price == null ) {
					$error = [ "message" => "Name and Price are required!", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$itemId = $this->itemRepo->update( $id, $name, $price );
					$this->itemGroupRepo->save( $itemId, $groups );
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
					$this->itemRepo->delete( $id );
					$message = [ "message" => "Item has been deleted!", "type" => "success" ];
					echo json_encode( $message );
				}
				break;

			case "addWidget":
				$title       = isset( $_REQUEST['title'] ) ? $_REQUEST['title'] : null;
				$description = isset( $_REQUEST['description'] ) ? $_REQUEST['description'] : null;
				$image       = isset( $_REQUEST['image'] ) ? $_REQUEST['image'] : null;

				if ( $title == null || $description == null ) {
					$error = [ "message" => "Title and Description are required!", "type" => "error" ];
					echo json_encode( $error );
				} else {
					$this->widgetRepo->create( $title, $description, $image );
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
					$this->widgetRepo->update( $id, $title, $description, $image );
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
					$this->widgetRepo->setAsDefaultWidget( $id );
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
					$this->widgetRepo->delete( $id );
					$message = [ "message" => "Widget has been deleted!", "type" => "success" ];
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

			case "bmabViewGroups":
				$data = $this->groupRepo->getAll();
				break;

			case "bmabEditGroup":
				$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				if ( $id !== null ) {
					$data = $this->groupRepo->get( $id );
				}
				break;

			case "bmabViewItems":
				$data = $this->itemRepo->getAll();
				break;

			case "bmabAddItem":
				$data = $this->groupRepo->getAll();
				break;

			case "bmabEditItem":
				$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				if ( $id !== null ) {
					$data           = [];
					$data['item']   = $this->itemRepo->get( $id );
					$data['groups'] = $this->groupRepo->getAll();
				}
				break;

			case "bmabViewWidgets":
				$data = $this->widgetRepo->getAll();
				break;

			case "bmabEditWidget":
				$id = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
				if ( $id !== null ) {
					$data = $this->widgetRepo->get( $id );
				}
				break;

			case "bmabViewPayments":
				$data = $this->paymentRepo->getAll();
				break;
		}
		echo json_encode( $data );
		wp_die(); // this is required to terminate immediately and return a proper response
	}
}