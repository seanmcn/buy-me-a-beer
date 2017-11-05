<?php

namespace bmab;

class ItemModel {

	/**
	 * @var int
	 */
	protected $id;
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var int
	 */
	protected $price;

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 *
	 * @return ItemModel
	 */
	public function setId( $id ) {
		$this->id = $id;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 *
	 * @return ItemModel
	 */
	public function setName( $name ) {
		$this->name = $name;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @param int $price
	 *
	 * @return ItemModel
	 */
	public function setPrice( $price ) {
		$this->price = $price;

		return $this;
	}

}