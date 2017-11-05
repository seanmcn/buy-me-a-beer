<?php

namespace bmab;

/**
 * Class WidgetRepository
 * @package bmab
 */
class WidgetRepository extends BaseRepository {
	protected $table;

	public function __construct( App $app ) {
		parent::__construct( $app );
		$this->table = $app->db->prefix . $app->config->tables['widgets'];
	}

	public function get( $id ) {
		$widget = $this->app->db->get_row( "SELECT * FROM $this->table WHERE id=$id" );

		return $widget;
	}

	public function getAll() {
		$widgets = $this->app->db->get_results( "SELECT * FROM $this->table" );

		return $widgets;
	}

	public function update( $id, $title, $description, $image ) {
		$widget = new WidgetModel();
		$widget
			->setId( $id )
			->setTitle( $title )
			->setDescription( $description )
			->setImage( $image );

		$this->save( $widget );
	}

	public function create( $title, $description, $image ) {
		$widget = new WidgetModel();
		$widget->setTitle( $title )
		       ->setDescription( $description )
		       ->setImage( $image );

		$this->save( $widget );
	}

	public function delete( $id ) {
		$this->app->db->delete( $this->table, array( 'id' => $id ), array( '%d' ) );
	}

	private function save( WidgetModel $widget ) {
		if ( ! $this->isDefaultWidgetSet() ) {
			$widget->setIsDefault( true );
		}

		$data   = array(
			'title'       => $widget->getTitle(),
			'description' => $widget->getDescription(),
			'image'       => $widget->getImage(),
			'is_default'  => $widget->isDefault()
		);
		$format = array( '%s', '%s', '%s', '%d' );

		if ( $widget->getId() ) {
			$this->app->db->update( $this->table, $data, array( 'id' => $widget->getId() ), $format, array( '%d' ) );
		} else {
			$this->app->db->insert( $this->table, $data, $format );
		};
	}

	public function setAsDefaultWidget( $widgetId ) {
		$data = array(
			'is_default' => true
		);
		$this->app->db->update( $this->table, $data, array( 'id' => $widgetId ), array( '%d' ), array( '%d' ) );
	}

	public function isDefaultWidgetSet() {
		global $wpdb, $bmabConfig;
		$table = $wpdb->prefix . $bmabConfig->tables['widgets'];

		$wpdb->query( "SELECT * FROM $table WHERE default_option='1'" );

		return $wpdb->num_rows == 0;
	}

	public function getDefaultWidget() {
		$widget = $this->app->db->get_row( "SELECT * FROM $this->table WHERE is_default" );

		return $widget;
	}
}