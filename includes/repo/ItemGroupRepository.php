<?php

namespace bmab;

/**
 * Class ItemGroupRepository
 * @package bmab
 */
class ItemGroupRepository extends BaseRepository {

	protected $table;

	public function __construct( App $app ) {
		parent::__construct( $app );
		$this->table = $app->db->prefix . $app->config->tables['item_groups'];
	}

	public function save( $itemId, $groups ) {

	}

}