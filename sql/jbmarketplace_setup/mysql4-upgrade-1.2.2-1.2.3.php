<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `{$this->getTable( 'junaidbhura_jbmarketplace_messages' )}` (
	`jbmarketplace_message_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(100) NOT NULL default '',
	`content` text NOT NULL default '',
	`create_dtime` datetime NOT NULL,
	PRIMARY KEY (`jbmarketplace_message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SQLTEXT;

$installer->run($sql);

$installer->endSetup();