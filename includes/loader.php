<?php

namespace bmab;
/**
 * Class BuyMeABeerLoader
 */
class BuyMeABeerLoader {

	/**
	 * @var array
	 */
	protected $actions;
	/**
	 * @var array
	 */
	protected $filters;

	/**
	 * @var array
	 */
	protected $shortCodes;

	/**
	 * BuyMeABeerLoader constructor.
	 */
	public function __construct() {
		$this->actions    = array();
		$this->filters    = array();
		$this->shortCodes = array();
	}

	/**
	 * @param $hook
	 * @param $component
	 * @param $callback
	 */
	public function addAction( $hook, $component, $callback ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback );
	}

	/**
	 * @param $hook
	 * @param $component
	 * @param $callback
	 */
	public function addFilter( $hook, $component, $callback ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback );
	}


	/**
	 * @param $hook
	 * @param $component
	 * @param $callback
	 */
	public function addShortCode( $hook, $component, $callback ) {
		$this->shortCodes = $this->add( $this->shortCodes, $hook, $component, $callback );
	}

	/**
	 * @param $hooks
	 * @param $hook
	 * @param $component
	 * @param $callback
	 *
	 * @return array
	 */
	private function add( $hooks, $hook, $component, $callback ) {
		$hooks[] = array(
			'hook'      => $hook,
			'component' => $component,
			'callback'  => $callback
		);

		return $hooks;
	}

	/**
	 *
	 */
	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
		}

		foreach ( $this->shortCodes as $hook ) {
			add_shortcode( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
		}
	}
}