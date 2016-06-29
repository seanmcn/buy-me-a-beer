<?php
define( 'DESCRIPTIONS_TABLE', 'buymeabeer_descriptions' );
define( 'PAYMENTS_TABLE', 'buymeabeer_payments' );
define( 'PRICEQUANITY_TABLE', "buymeabeer_pqs" );

global $currencyMappings;
$currencyMappings = array(
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

//define('CURRENCY_MAPPINGS', json_encode($currencyMappings));