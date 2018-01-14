<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('storelocator')};
CREATE TABLE {$this->getTable('storelocator')} (
  `entity_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `address` text NOT NULL,
  `zipcode` varchar(255),
  `city` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `continent` varchar(255) NOT NULL,
  `phone` varchar(255),
  `email` varchar(255),
  `is_flag_store` int(1),
  `is_repeat_store` int(1),
  `is_retail_store` int(1),
  `can_collect_in_store` int(1),
  `can_return_in_store` int(1),
  `opening_hours` text,
  `content` text NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup(); 