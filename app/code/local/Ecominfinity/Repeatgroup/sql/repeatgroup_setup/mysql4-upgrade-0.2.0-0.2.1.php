<?php 

$installer = $this;
$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('ei_stock_qty')};
CREATE TABLE {$this->getTable('ei_stock_qty')} (
  `entity_id` int(11) unsigned NOT NULL auto_increment,
  `sku` varchar(255) NOT NULL default '',
  `qty` int(11) NOT NULL default 0,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();