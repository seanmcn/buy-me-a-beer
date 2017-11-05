<?php

namespace bmab;

/**
 * Class WidgetModel
 */
class WidgetModel {
	/**
	 * @var int
	 */
	protected $id;
	/**
	 * @var string
	 */
	protected $name;
	/**
	 * @var string
	 */
	protected $title;
	/**
	 * @var string
	 */
	protected $description;
	/**
	 * @var string
	 */
	protected $image;
	/**
	 * @var bool
	 */
	protected $is_default;


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 *
	 * @return WidgetModel
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
	 * @return WidgetModel
	 */
	public function setName( $name ) {
		$this->name = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $title
	 *
	 * @return WidgetModel
	 */
	public function setTitle( $title ) {
		$this->title = $title;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param string $description
	 *
	 * @return WidgetModel
	 */
	public function setDescription( $description ) {
		$this->description = $description;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * @param string $image
	 *
	 * @return WidgetModel
	 */
	public function setImage( $image ) {
		$this->image = $image;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isDefault() {
		return $this->is_default;
	}

	/**
	 * @param bool $is_default
	 *
	 * @return WidgetModel
	 */
	public function setIsDefault( $is_default ) {
		$this->is_default = $is_default;

		return $this;
	}
}