<?php 

$installer = $this;
$installer->startSetup();
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('ei_coupon_mail')};
CREATE TABLE {$this->getTable('ei_coupon_mail')} (
  `entity_id` int(11) unsigned NOT NULL auto_increment,
  `email` varchar(255) NOT NULL default '',
  `coupon` varchar(255) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();