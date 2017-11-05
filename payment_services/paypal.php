<?php

namespace bmab;

/**
 * Class BuyMeABeerPaypal
 */
class BuyMeABeerPaypal {

	protected $app;
	protected $paypalApi;
	protected $paypalAccount;
	protected $paypalClientId;
	protected $paypalSecret;
	protected $currency;
	protected $paypalConsentEndpoint;

	/** @var ItemRepository widget_repo */
	protected $itemRepo;

	/** @var  PaymentRepository $paymentRepo */
	protected $paymentRepo;

	public function __construct( App $app ) {
		$this->app         = $app;
		$this->currency    = $app->currency;
		$this->itemRepo    = $app->repos['items'];
		$this->paymentRepo = $app->repos['payments'];
		/** @var SettingRepository $settingRepo */
		$settingRepo                 = $app->repos['settings'];
		$paypalMode                  = $settingRepo->get( SettingRepository::PAYPAL_MODE, 'sandbox' );
		$this->paypalAccount         = $settingRepo->get( SettingRepository::PAYPAL_EMAIL, null );
		$this->paypalClientId        = $settingRepo->get( SettingRepository::PAYPAL_CLIENT, null );
		$this->paypalSecret          = $settingRepo->get( SettingRepository::PAYPAL_SECRET, null );
		$this->paypalApi             = $paypalMode === 'live' ? "api.paypal.com" : "api.sandbox.paypal.com";
		$this->paypalConsentEndpoint = "/webapps/auth/protocol/openidconnect/v1/authorize";
	}

	public function createPayment( $widgetId, $itemId, $location ) {
		$item = $this->itemRepo->get( $itemId );

		$blogName                                 = get_bloginfo( 'name' );
		$priceName                                = $item->name;
		$price                                    = $item->price;
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
				'currency' => $this->currency,
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
			'widget_id' => $widgetId,
			'url'       => $location
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
			$params['widget_id']         = $data['widget_id'];


			$this->paymentRepo->create( $params['paymentId'], $params['payerEmail'], $params['payerFirstName'],
				$params['payerLastName'], $params['payerShippingAddress'], $params['payerPaymentMethod'],
				$params['payerPaymentTotal'], $params['widget_id'], $params['url'] );

			$bmabSuccessPage = get_option( 'bmabSuccessPage', false );
			if ( $bmabSuccessPage ) {
				$post = get_post( $bmabSuccessPage );
				if ( $post ) {
					wp_redirect( $post->guid );
					exit;
				}
			}
			wp_redirect( home_url() );
			exit;
		} else {
			$bmabErrorPage = get_option( 'bmabErrorPage', false );
			if ( $bmabErrorPage ) {
				$post = get_post( $bmabErrorPage );
				if ( $post ) {
					wp_redirect( $post->guid );
					exit;
				}
			}
			wp_redirect( home_url() );
			exit;
		}
	}

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