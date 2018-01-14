<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('storelocator')} ADD `status` tinyint(4) NOT NULL AFTER `entity_id`;
ALTER TABLE {$this->getTable('storelocator')} ADD `url_key` VARCHAR(255) NOT NULL AFTER `status`;

");

$installer->endSetup(); 