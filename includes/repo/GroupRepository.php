<?php

namespace bmab;

/**
 * Class GroupRepository
 * @package bmab
 */
class GroupRepository extends BaseRepository {

	protected $table;

	public function __construct( App $app ) {
		parent::__construct( $app );
		$this->table = $app->db->prefix . $app->config->tables['groups'];
	}

	public function get( $id ) {
		$item = $this->app->db->get_row( "SELECT * FROM $this->table WHERE id=$id" );

		return $item;
	}

	public function getAll() {
		$items = $this->app->db->get_results( "SELECT * FROM $this->table" );

		return $items;
	}

	public function update( $id, $name ) {
		$group = new GroupModel();
		$group->setId( $id )
		      ->setName( $name );
		$this->save( $group );
	}

	public function create( $name ) {
		$group = new GroupModel();
		$group->setName( $name );
		$this->save( $group );
	}

	public function delete( $id ) {
		$this->app->db->delete( $this->table, array( 'id' => $id ), array( '%d' ) );
	}

	private function save( GroupModel $group ) {
		$data   = array(
			'name' => $group->getName(),
		);
		$format = array( '%s', '%d', );

		if ( $group->getId() ) {
			$this->app->db->update( $this->table, $data, array( 'id' => $group->getId() ), $format, array( '%d' ) );
		} else {
			$this->app->db->insert( $this->table, $data, $format );
		};
	}
}