<?php

namespace bmab;

class GroupModel {

	/**
	 * @var int
	 */
	protected $id;
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 *
	 * @return GroupModel
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
	 * @return GroupModel
	 */
	public function setName( $name ) {
		$this->name = $name;

		return $this;
	}

}