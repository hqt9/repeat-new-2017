<?php

class AAOO_Guestwishlist_Model_Session extends Mage_Core_Model_Session_Abstract {
  public function __construct() {
    $_namespace = 'guestwishlist';
    $_namespace .= '_' . (Mage::app()->getStore()->getWebsite()->getCode());

    $this->init($_namespace);
    Mage::dispatchEvent('guestwishlist_session_init', ['guestwishlist_session' => $this]);
  }
}