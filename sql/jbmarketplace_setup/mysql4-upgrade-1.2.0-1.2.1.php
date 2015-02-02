<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `{$this->getTable( 'junaidbhura_jbmarketplace_vendors' )}` (
	`jbmarketplace_vendor_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`vendor_id` bigint(20) unsigned NOT NULL,
	`warehouse_id` int(10) unsigned NOT NULL,
	`user_name` varchar(20) NOT NULL default '',
	`first_name` varchar(100) NOT NULL default '',
	`last_name` varchar(100) NOT NULL default '',
	`email` varchar(100) NOT NULL default '',
	`commission` varchar(40) NOT NULL default '',
	`store_name` varchar(100) NOT NULL default '',
	`store_url` varchar(100) NOT NULL default '',
	`store_description` text NOT NULL default '',
	`store_logo` varchar(100) NOT NULL default '',
	`store_image` varchar(100) NOT NULL default '',
	`store_twitter` varchar(100) NOT NULL default '',
	`store_facebook` varchar(100) NOT NULL default '',
	`store_pinterest` varchar(100) NOT NULL default '',
	`store_googleplus` varchar(100) NOT NULL default '',
	PRIMARY KEY (`jbmarketplace_vendor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SQLTEXT;

$installer->run($sql);

$installer->endSetup();