<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER IGNORE TABLE `{$this->getTable( 'junaidbhura_jbmarketplace_vendors' )}` ADD `store_returnpolicy` text;

");


$installer->endSetup();