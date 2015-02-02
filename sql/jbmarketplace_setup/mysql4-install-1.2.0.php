<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `{$this->getTable( 'junaidbhura_jbmarketplace_products' )}` (
	`jbmarketplace_product_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`product_id` bigint(20) unsigned NOT NULL,
	`user_id` bigint(20) unsigned NOT NULL,
	`jbmarketplace_products_dtime` datetime NOT NULL,
	PRIMARY KEY (`jbmarketplace_product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SQLTEXT;

$installer->run($sql);

$installer->endSetup();