<?php

namespace bmab;

/**
 * Class BaseRepository
 * @package bmab
 */
class BaseRepository {
	protected $app;

	public function __construct( App $app ) {
		$this->app = $app;
	}
}