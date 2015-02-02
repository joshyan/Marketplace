<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `{$this->getTable( 'junaidbhura_jbmarketplace_orders' )}` (
	`jbmarketplace_order_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`order_id` bigint(20) unsigned NOT NULL,
	`increment_id` varchar(40) NOT NULL default '',
	`vendor_id` bigint(20) unsigned NOT NULL,
	`products` text NOT NULL default '',
	`products_count` int(10) unsigned NOT NULL,
	`customer_firstname` varchar(40) NOT NULL default '',
	`customer_lastname` varchar(40) NOT NULL default '',
	`status` varchar(2) NOT NULL default '',
	`gross_sales` float(40),
	`sales` float(40),
	`income` float(40),
	`shipping_fee` float(40),
	`shipping_method` varchar(100) NOT NULL default '',
	`tax` float(40),
	`discount` float(40),
	`tracking_code` varchar(40) NOT NULL default '',
	`shipping_carrier` varchar(40) NOT NULL default '',
	`message` text NOT NULL default '',
	`order_dtime` datetime NOT NULL,
	PRIMARY KEY (`jbmarketplace_order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SQLTEXT;

$installer->run($sql);

$installer->endSetup();