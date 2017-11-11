<?php

namespace bmab;

/**
 * Class ItemRepository
 * @package bmab
 */
class ItemRepository extends BaseRepository {

	protected $table;

	public function __construct( App $app ) {
		parent::__construct( $app );
		$this->table = $app->db->prefix . $app->config->tables['items'];
	}

	public function get( $id ) {
		$item = $this->app->db->get_row( "SELECT * FROM $this->table WHERE id=$id" );

		return $item;
	}

	public function getAll() {
		$items = $this->app->db->get_results( "SELECT * FROM $this->table" );

		return $items;
	}

	public function getAllFormatted() {
		$items = $this->getAll();
		foreach ( $items as $key => $value ) {
			$items[ $key ]->price = $this->app->formatAsCurrency( $value->price );
		}

		return $items;
	}

	public function update( $id, $name, $price ) {
		$item = new ItemModel();
		$item->setId( $id )
		     ->setName( $name )
		     ->setPrice( $price );
		$this->save( $item );
	}

	public function create( $name, $price ) {
		$item = new ItemModel();
		$item->setName( $name )
		     ->setPrice( $price );
		$itemId = $this->save( $item );

		return $itemId;
	}

	public function delete( $id ) {
		$this->app->db->delete( $this->table, array( 'id' => $id ), array( '%d' ) );
	}

	private function save( ItemModel $item ) {
		$data   = array(
			'name'  => $item->getName(),
			'price' => $item->getPrice(),
		);
		$format = array( '%s', '%d', );

		if ( $item->getId() ) {
			$this->app->db->update( $this->table, $data, array( 'id' => $item->getId() ), $format, array( '%d' ) );

			return $item->getId();
		} else {
			$this->app->db->insert( $this->table, $data, $format );

			return $this->app->db->insert_id;
		}
	}
}