<?php
require_once( plugin_dir_path( __DIR__ ) . "includes/config.php" );

/**
 * Class BuyMeABeerPaypal
 */
class BuyMeABeerPaypal {

	/**
	 * BuyMeABeerPaypal constructor.
	 */
	public function __construct() {
		$paypalMode                  = get_option( 'bmabPaypalMode', 'sandbox' );
		$this->paypalApi             = $paypalMode === 'live' ? "api.paypal.com" : "api.sandbox.paypal.com";
		$this->paypalAccount         = get_option( 'bmabPaypalEmail', null );
		$this->paypalClientId        = get_option( 'bmabPaypalClientId', null );
		$this->paypalSecret          = get_option( 'bmabPaypalSecret', null );
		$this->bmabCurrency          = get_option( 'bmabCurrency', 'USD' );
		$this->paypalConsentEndpoint = "/webapps/auth/protocol/openidconnect/v1/authorize";
	}


	/**
	 * @param $descriptionId
	 * @param $selectedPQ
	 * @param $location
	 *
	 * @return string
	 */
	public function createPayment( $descriptionId, $selectedPQ, $location ) {
		global $wpdb;

		$pqTable = $wpdb->prefix . PRICEQUANITY_TABLE;
		$pq      = $wpdb->get_row( "SELECT * FROM $pqTable WHERE id=$selectedPQ" );

		$blogName  = get_bloginfo( 'name' );
		$priceName = $pq->name;
		$price     = $pq->price;

		$accessToken                              = $this->getToken();
		$curlHeaders                              = array(
			'Content-Type: application/json',
			'Authorization: Bearer ' . $accessToken
		);
		$paymentObject                            = array();
		$paymentObject['intent']                  = 'sale';
		$paymentObject['payer']['payment_method'] = 'paypal';
		$paymentObject['transactions'][]          = array(
			'amount'      => array(
				'total'    => $price,
				'currency' => $this->bmabCurrency,
				'details'  => array(
					'subtotal' => $price,
					'tax'      => '0.00',
					'shipping' => '0.00',
				)
			),
			'description' => "$priceName for $blogName",
		);

		// Implode data we want Paypal to send back to us to store with payment
		$sendBack               = array(
			'descriptionId' => $descriptionId,
			'url'           => $location
		);
		$_SESSION['bmabPaypal'] = $sendBack;

		$paymentObject['redirect_urls'] = array(
			'return_url' => plugins_url( 'public/payment_responders/paypal/success.php?custom=' . $sendBack, __DIR__ ),
			'cancel_url' => plugins_url( 'public/payment_responders/paypal/cancel.php', __DIR__ ),
		);
		$paymentJsonObject              = json_encode( $paymentObject );

		$auth           = $this->paypalClientId . ':' . $this->paypalSecret;
		$curlResult     = $this->curlPost( $curlHeaders, $auth, '/v1/payments/payment', $paymentJsonObject );
		$paypalApproval = $curlResult['links'][1]['href'];

		return $paypalApproval . "&useraction=commit";
	}


	/**
	 * @param $paymentId
	 * @param $payerId
	 * @param $data
	 */
	public function executePayment( $paymentId, $payerId, $data ) {
		$accessToken = $this->getToken();
		$curlHeaders = array(
			'Content-Type: application/json',
			'Authorization: Bearer ' . $accessToken
		);

		$curlResult = $this->curlPost( $curlHeaders, null, '/v1/payments/payment/' . $paymentId . '/execute/',
			json_encode( array( 'payer_id' => $payerId ) ) );
		if ( array_key_exists( 'state', $curlResult ) && $curlResult['state'] == 'approved' ) {
			$params['paymentId']            = $curlResult['id'];
			$params['payerEmail']           = $curlResult['payer']['payer_info']['email'];
			$params['payerFirstName']       = $curlResult['payer']['payer_info']['first_name'];
			$params['payerLastName']        = $curlResult['payer']['payer_info']['last_name'];
			$params['payerShippingAddress'] = $curlResult['payer']['payer_info']['shipping_address']; //array;
			$params['payerPaymentMethod']   = $curlResult['payer']['payment_method'];

			// Work out the total paid
			$total = 0;
			foreach ( $curlResult['transactions'] as $transaction ) {
				$total = $total + $transaction['amount']['total'];
			}
			$params['payerPaymentTotal'] = $total;
			$params['url']               = $data['url'];
			$params['descriptionId']     = $data['descriptionId'];

			$this->savePayment( $params );
			//Todo Sean: Thank you page ->
			wp_redirect( home_url() . '/bmab-success' );
		} else {
			//Todo Sean : Payment Failed page ->
			wp_redirect( home_url() . '/bmab-failure' );
		}
	}

	/**
	 * @param $params
	 */
	public function savePayment( $params ) {
		global $wpdb;
		$paymentsTable = $wpdb->prefix . PAYMENTS_TABLE;
		$wpdb->insert(
			$paymentsTable,
			array(
				'paypal_id'      => $params['paymentId'],
				'email'          => $params['payerEmail'],
				'first_name'     => $params['payerFirstName'],
				'last_name'      => $params['payerLastName'],
				'address'        => json_encode( $params['payerShippingAddress'] ),
				'payment_method' => $params['payerPaymentMethod'],
				'time'           => current_time( 'mysql' ),
				'amount'         => $params['payerPaymentTotal'],
				'description_id' => $params['descriptionId'],
				'url'            => $params['url']
			)
		);

		return;
	}

	/**
	 * @return mixed
	 */
	public function getToken() {
		$curlHeaders = array(
			'Accept: application/json',
			'Content-Type: x-www-form-urlencoded'
		);
		$curlAuth    = $this->paypalClientId . ':' . $this->paypalSecret;
		$curlResult  = $this->curlPost( $curlHeaders, $curlAuth, '/v1/oauth2/token', 'grant_type=client_credentials' );
		if ( $curlResult ) {
			$accessToken = $curlResult['access_token'];
		} else {
			error_log( 'Buy Me A Beer: Failed to get Paypal oAuth token' );
			wp_redirect( home_url() );
			exit;
		}

		return $accessToken;
	}

	/**
	 * @param $headers
	 * @param null $auth
	 * @param $endPoint
	 * @param $postFields
	 *
	 * @return array|bool|mixed|object
	 */
	public function curlPost( $headers, $auth = null, $endPoint, $postFields ) {
		$curl        = curl_init();
		$curlOptions = array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL            => 'https://' . $this->paypalApi . $endPoint,
			CURLOPT_HTTPHEADER     => $headers,
			CURLOPT_POST           => 1,
			CURLOPT_POSTFIELDS     => $postFields,
			CURLOPT_SSL_VERIFYPEER => false //odo Sean: Remove this hack.
		);
		if ( $auth ) {
			$curlOptions[ CURLOPT_USERPWD ] = $auth;
		}
		curl_setopt_array( $curl, $curlOptions );
		$result = curl_exec( $curl );
		if ( curl_errno( $curl ) ) {
			error_log( 'Error: ' . curl_error( $curl ) );

			return false;
		}
		curl_close( $curl );

		return json_decode( $result, true );
	}
}