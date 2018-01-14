<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('qrcode')};
CREATE TABLE {$this->getTable('qrcode')} (
  `entity_id` int(11) unsigned NOT NULL auto_increment,
  `code` varchar(255) NOT NULL default '',
  `enabled` int(1),
  `counter` int(11) unsigned NOT NULL,
  `url` varchar(255),
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup(); 