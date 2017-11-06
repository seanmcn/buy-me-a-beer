<?php

namespace bmab;

class Config {
	public $tables = [
		'groups'        => 'bmab_groups',
		'items'         => 'bmab_items',
		'item_groups'   => 'bmab_item_groups',
		'payments'      => 'bmab_payments',
		'widgets'       => 'bmab_widgets',
		'widget_groups' => 'bmab_widget_groups'
	];

	public $currencyMappings = array(
		"AUD" => array(
			"pre"  => "&#36;",
			"post" => " AUD"
		),
		"CAD" => array(
			"pre"  => "&#36;",
			"post" => " CAD"
		),
		"EUR" => array(
			"pre"  => "&euro;",
			"post" => " EUR"
		),
		"HKD" => array(
			"pre"  => "&#36;",
			"post" => " HKD"
		),
		"NZD" => array(
			"pre"  => "&#36;",
			"post" => " NZD"
		),
		"NOK" => array(
			"pre"  => "",
			"post" => " NOK"
		),
		"GBP" => array(
			"pre"  => "&#163;",
			"post" => " GBP"
		),
		"SEK" => array(
			"pre"  => "",
			"post" => " SEK"
		),
		"CHF" => array(
			"pre"  => "",
			"post" => " CHF"
		),
		"USD" => array(
			"pre"  => "&#36;",
			"post" => " USD"
		)
	);
}