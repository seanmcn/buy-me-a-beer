<?php

namespace bmab;

class PaymentModel {
	//Todo: document
	protected $id;
	protected $externalId;
	protected $amount;
	protected $email;
	protected $firstName;
	protected $lastName;
	protected $address;
	protected $paymentMethod;
	protected $widgetId;
	protected $url;
	protected $time;

	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param mixed $id
	 *
	 * @return PaymentModel
	 */
	public function setId( $id ) {
		$this->id = $id;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getExternalId() {
		return $this->externalId;
	}

	/**
	 * @param mixed $externalId
	 *
	 * @return PaymentModel
	 */
	public function setExternalId( $externalId ) {
		$this->externalId = $externalId;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAmount() {
		return $this->amount;
	}

	/**
	 * @param mixed $amount
	 *
	 * @return PaymentModel
	 */
	public function setAmount( $amount ) {
		$this->amount = $amount;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param mixed $email
	 *
	 * @return PaymentModel
	 */
	public function setEmail( $email ) {
		$this->email = $email;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * @param mixed $firstName
	 *
	 * @return PaymentModel
	 */
	public function setFirstName( $firstName ) {
		$this->firstName = $firstName;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLastName() {
		return $this->lastName;
	}

	/**
	 * @param mixed $lastName
	 *
	 * @return PaymentModel
	 */
	public function setLastName( $lastName ) {
		$this->lastName = $lastName;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAddress() {
		return $this->address;
	}

	/**
	 * @param mixed $address
	 *
	 * @return PaymentModel
	 */
	public function setAddress( $address ) {
		$this->address = $address;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPaymentMethod() {
		return $this->paymentMethod;
	}

	/**
	 * @param mixed $paymentMethod
	 *
	 * @return PaymentModel
	 */
	public function setPaymentMethod( $paymentMethod ) {
		$this->paymentMethod = $paymentMethod;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getWidgetId() {
		return $this->widgetId;
	}

	/**
	 * @param mixed $widgetId
	 *
	 * @return PaymentModel
	 */
	public function setWidgetId( $widgetId ) {
		$this->widgetId = $widgetId;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param mixed $url
	 *
	 * @return PaymentModel
	 */
	public function setUrl( $url ) {
		$this->url = $url;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTime() {
		return $this->time;
	}

	/**
	 * @return PaymentModel
	 */
	public function setTime() {
		$this->time = current_time( 'mysql' );

		return $this;
	}

}