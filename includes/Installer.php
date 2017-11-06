<?php

namespace bmab;

/**
 * Class BuyMeABeer
 */

class Installer {

	/** @var WidgetRepository $widgetRepo */
	protected $widgetRepo;

	/** @var ItemRepository $itemRepo */
	protected $itemRepo;

	/** @var App */
	protected $app;


	/**
	 * Installer constructor.
	 *
	 * @param App $app
	 */
	public function __construct( App $app ) {
		$this->app        = $app;
		$this->widgetRepo = $app->repos['widgets'];
		$this->itemRepo   = $app->repos['items'];
	}

	/**
	 *
	 */
	public function activatePlugin() {
		// check user can activate plugin
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		check_admin_referer( "activate-plugin_{$plugin}" );

		$this->installation();
	}

	/**
	 *
	 */
	public function installation() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$charset = $this->app->db->get_charset_collate();

		// Group Table
		$table = $this->app->db->prefix . $this->app->config->tables['groups'];
		$sql   = "CREATE TABLE $table (
			id int NOT NULL AUTO_INCREMENT,
			name VARCHAR(300),
			UNIQUE KEY id(id)
		) $charset;";

		// Item Table
		$table = $this->app->db->prefix . $this->app->config->tables['items'];
		$sql   .= "CREATE TABLE $table (
			id INT NOT NULL AUTO_INCREMENT,
			name VARCHAR(300),
			price INT NOT NULL,
			UNIQUE KEY id(id)
		) $charset;";

		// Item Groups Table
		$table = $this->app->db->prefix . $this->app->config->tables['item_groups'];
		$sql   .= "CREATE TABLE $table (
			item_id INT NOT NULL,
			group_id INT NOT NULL,
			PRIMARY KEY(item_id, group_id)
		) $charset;";

		// Payments Table
		$table = $this->app->db->prefix . $this->app->config->tables['payments'];
		$sql   .= "CREATE TABLE $table (
			id INT NOT NULL AUTO_INCREMENT,
			paypal_id varchar(300),
			amount int(100),
			email varchar(300),
			first_name varchar(300),
			last_name varchar(300),
			address text,
			payment_method varchar(300),
			widget_id int(100),
			url text,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			UNIQUE KEY id (id)
		) $charset;";

		// Widget Table
		$table = $this->app->db->prefix . $this->app->config->tables['widgets'];
		$sql   .= "CREATE TABLE $table (
			id INT NOT NULL AUTO_INCREMENT,
			name VARCHAR(300),
			title VARCHAR(300),
			description text,
			image VARCHAR(300),
			is_default BOOLEAN,
			UNIQUE KEY id(id)
		) $charset;";

		// Widget Group Table
		$table = $this->app->db->prefix . $this->app->config->tables['widget_groups'];
		$sql   .= "CREATE TABLE $table (
			widget_id INT NOT NULL,
			group_id INT NOT NULL,
			PRIMARY KEY(widget_id, group_id)
		) $charset;";

		// Run queries and store schema version.
		dbDelta( $sql );
		add_option( 'bmabDatabaseVersion', $this->app->version );

		// Seed the database
		$this->itemRepo->create( "1 Beer", 3 );
		$this->itemRepo->create( "6 Beers", 15 );
		$this->itemRepo->create( "12 Beers", 25 );
		$this->itemRepo->create( '36 Beers', 45 );
		$this->itemRepo->create( "Keg of Beer", 125 );

		// Todo add group 'Default'
		$this->widgetRepo->create( 'Did I help you out?', 'If so how about buying me some beer?', '' );
	}


}