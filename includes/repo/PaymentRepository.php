<?php

namespace bmab;

/**
 * Class PaymentRepository
 * @package bmab
 */
class PaymentRepository extends BaseRepository {
	/**
	 * @var string
	 */
	protected $table;

	/**
	 * PaymentRepository constructor.
	 *
	 * @param App $app
	 */
	public function __construct( App $app ) {
		parent::__construct( $app );
		$this->table = $app->db->prefix . $app->config->tables['payments'];
	}

	/**
	 * @return array|null|object
	 */
	public function getAll() {
		$widgetTable = $this->app->db->prefix . $this->app->config->tables['widgets'];
		$payments    = $this->app->db->get_results( "SELECT * FROM $this->table LEFT JOIN $widgetTable ON $this->table.widget_id=$widgetTable.id" );

		return $payments;
	}

	/**
	 * @param $externalId
	 * @param $email
	 * @param $firstName
	 * @param $lastName
	 * @param $address
	 * @param $method
	 * @param $amount
	 * @param $widget_id
	 * @param $url
	 */
	public function create( $externalId, $email, $firstName, $lastName, $address, $method, $amount, $widget_id, $url ) {
		$payment = new PaymentModel();
		$payment->setExternalId( $externalId )
		        ->setEmail( $email )
		        ->setFirstName( $firstName )
		        ->setLastName( $lastName )
		        ->setAddress( $address )
		        ->setPaymentMethod( $method )
		        ->setTime()
		        ->setAmount( $amount )
		        ->setWidgetId( $widget_id )
		        ->setUrl( $url );

		$this->save( $payment );
	}

	/**
	 * @param PaymentModel $payment
	 */
	private function save( PaymentModel $payment ) {

		global $wpdb, $bmabConfig;
		$table = $wpdb->prefix . $bmabConfig->tables['payments'];
		// Todo: sort this out with sprintf
		$wpdb->insert(
			$table,
			array(
				'paypal_id'      => $payment->getExternalId(),
				'email'          => $payment->getEmail(),
				'first_name'     => $payment->getFirstName(),
				'last_name'      => $payment->getLastName(),
				'address'        => json_encode( $payment->getAddress() ),
				'payment_method' => $payment->getPaymentMethod(),
				'time'           => $payment->getTime(),
				'amount'         => $payment->getAmount(),
				'widget_id'      => $payment->getWidgetId(),
				'url'            => $payment->getUrl()
			)
		);
	}
}