<?php

class Ecominfinity_Repeatgroup_Helper_Data extends Mage_Core_Helper_Abstract {
  public function indexHreflang() {
    $_is_secure = Mage::app()->getStore()->isCurrentlySecure();
    if ($_is_secure) {
      $_configuration = [
        1 => ['url' => 'https://www.repeatcashmere.com/en/', 'locale' => 'en'],
        2 => ['url' => 'https://www.repeatcashmere.com/de/', 'locale' => 'de-de'],
        3 => ['url' => 'https://www.repeatcashmere.com/nl/', 'locale' => 'nl-nl'],
        4 => ['url' => 'https://www.repeatcashmere.com/fr/', 'locale' => 'fr-fr'],
        5 => ['url' => 'https://www.repeatcashmere.com/uk/', 'locale' => 'en-gb'],
        6 => ['url' => 'https://www.repeatcashmere.com/us/', 'locale' => 'en-us'],
        7 => ['url' => 'https://www.repeatcashmere.com/ch_de/', 'locale' => 'de-ch'],
        8 => ['url' => 'https://www.repeatcashmere.com/ch_fr/', 'locale' => 'fr-ch'],
        9 => ['url' => 'https://www.repeatcashmere.com/hk_en/', 'locale' => 'en-hk'],
        11 => ['url' => 'https://www.repeatcashmere.com/hk_tc/', 'locale' => 'zh-hk'],
        13 => ['url' => 'https://www.repeatcashmere.com/hk_sc/', 'locale' => 'zh-cn'],
      ];
    } else {
      $_configuration = [
        1 => ['url' => 'http://www.repeatcashmere.com/en/', 'locale' => 'en'],
        2 => ['url' => 'http://www.repeatcashmere.com/de/', 'locale' => 'de-de'],
        3 => ['url' => 'http://www.repeatcashmere.com/nl/', 'locale' => 'nl-nl'],
        4 => ['url' => 'http://www.repeatcashmere.com/fr/', 'locale' => 'fr-fr'],
        5 => ['url' => 'http://www.repeatcashmere.com/uk/', 'locale' => 'en-gb'],
        6 => ['url' => 'http://www.repeatcashmere.com/us/', 'locale' => 'en-us'],
        7 => ['url' => 'http://www.repeatcashmere.com/ch_de/', 'locale' => 'de-ch'],
        8 => ['url' => 'http://www.repeatcashmere.com/ch_fr/', 'locale' => 'fr-ch'],
        9 => ['url' => 'http://www.repeatcashmere.com/hk_en/', 'locale' => 'en-hk'],
        11 => ['url' => 'http://www.repeatcashmere.com/hk_tc/', 'locale' => 'zh-hk'],
        13 => ['url' => 'http://www.repeatcashmere.com/hk_sc/', 'locale' => 'zh-cn'],
      ];
    }

    $_links = array();
    $_store_id = Mage::app()->getStore()->getId();
    $_stores = [1,2,3,4,5,6,7,8,9,11,13];

    foreach ($_stores as $i) {
      $_links[] =array(
        'url' => $_configuration[$i]['url'],
        'locale' => $_configuration[$i]['locale'],
      );
    }

    return $_links;
  }

  public function redirectHreflang() {
    $_is_secure = Mage::app()->getStore()->isCurrentlySecure();
    $_base_url = ($_is_secure) ? 'https://www.repeatcashmere.com/' : 'http://www.repeatcashmere.com/';
    $_configuration = [
      1 => ['code' => 'en', 'locale' => 'en'],
      2 => ['code' => 'de', 'locale' => 'de-de'],
      3 => ['code' => 'nl', 'locale' => 'nl-nl'],
      4 => ['code' => 'fr', 'locale' => 'fr-fr'],
      5 => ['code' => 'uk', 'locale' => 'en-gb'],
      6 => ['code' => 'us', 'locale' => 'en-us'],
      7 => ['code' => 'ch_de', 'locale' => 'de-ch'],
      8 => ['code' => 'ch_fr', 'locale' => 'fr-ch'],
      9 => ['code' => 'hk_en', 'locale' => 'en-hk'],
      11 => ['code' => 'hk_tc', 'locale' => 'zh-hk'],
      13 => ['code' => 'hk_sc', 'locale' => 'zh-cn'],
    ];

    $_links = [];
    $_store_id = Mage::app()->getStore()->getId();
    $_stores = [1,2,3,4,5,6,7,8,9,11,13];

    $_url_string = Mage::helper('core/url')->getCurrentUrl();
    $_url = Mage::getSingleton('core/url')->parseUrl($_url_string);
    $_path = $_url->getPath();
    $_path = explode('/', $_path);
    array_shift($_path);
    array_shift($_path);
    $_path = trim(implode('/', $_path), '/');

    $_en_rewrite_path = NULL;

    foreach ($_stores as $i) {
      $_rewrite = Mage::getModel('enterprise_urlrewrite/redirect')->loadByRequestPath($_path, $i);

      if ($_rewrite->getId() == NULL || strpos($_rewrite->getData('target_path'), 'view') != false) {
        $_rewrite_path = $_path;
      } else {
        $_rewrite_path = $_rewrite->getData('target_path');
      }

      if ($i == 1) { $_en_rewrite_path = $_rewrite_path; }
      if ($i == 5 || $i == 6) { $_rewrite_path = $_en_rewrite_path; }

      if (substr($_rewrite_path, -5) !== '.html' && substr($_rewrite_path, -1) !== '/') {
        $_rewrite_path .= '/';
      }

      $_links[] =array(
        'url' => $_base_url . $_configuration[$i]['code'] . '/' . $_rewrite_path,
        'locale' => $_configuration[$i]['locale'],
      );
    }

    return $_links;
  }

  public function productHreflang($_product_id) {
    $_configuration = [
      1 => ['code' => 'en', 'locale' => 'en'],
      2 => ['code' => 'de', 'locale' => 'de-de'],
      3 => ['code' => 'nl', 'locale' => 'nl-nl'],
      4 => ['code' => 'fr', 'locale' => 'fr-fr'],
      5 => ['code' => 'uk', 'locale' => 'en-gb'],
      6 => ['code' => 'us', 'locale' => 'en-us'],
      7 => ['code' => 'ch_de', 'locale' => 'de-ch'],
      8 => ['code' => 'ch_fr', 'locale' => 'fr-ch'],
      9 => ['code' => 'hk_en', 'locale' => 'en-hk'],
      11 => ['code' => 'hk_tc', 'locale' => 'zh-hk'],
      13 => ['code' => 'hk_sc', 'locale' => 'zh-cn'],
    ];

    $_links = array();
    $_store_id = Mage::app()->getStore()->getId();
    $_stores = [1,2,3,4,5,6,7,8,9,11,13];

    foreach ($_stores as $i) {
      $_product = Mage::getModel('catalog/product')->setStoreId($i)->load($_product_id);
      $_links[] = array(
        'url' => 'http://www.repeatcashmere.com/' . $_configuration[$i]['code'] . '/' . $_product->getData('url_key') . '.html',
        'locale' => $_configuration[$i]['locale'],
      );
    }

    return $_links;
  }

  protected function _get_session() {
    return Mage::getSingleton('repeatgroup/session');
  }

  protected function _get_session_data($_key) {
    return $this->_get_session()->getData($_key);
  }

  public function getMcacheStatus() {
    return Mage::getStoreConfig('mcache/general/is_running', 0);
  }

  public function setMcacheStatus($_status) {
    $_config = new Mage_Core_Model_Config();
    $_config->saveConfig('mcache/general/is_running', $_status, 'default');
  }

  public function is_user_dismissed() {
    $_is_dismissed = $this->_get_session_data('is_dismissed');
    return $_is_dismissed === 'true';
  }

  public function get_tracking_url($_order) {
    $_store_code = Mage::app()->getStore()->getCode();
  $_url = '';
    switch ($_store_code) {
      case 'en':
        $_url = '//www.dpd.com/tracking';
    $_locale = 'en_D2';
    break;
      case 'de':
        $_url = '//www.dpd.com/tracking_de';
    $_locale = 'de_DE';
    break;
      case 'nl':
        $_url = '//www.dpd.com/tracking_nl';
    $_locale = 'nl_NL';
    break;
    case 'fr':
    $_url = '//www.dpd.com/tracking_fr';
    $_locale = 'fr_BE';
      default:
        return '#';
    }

  if (isset($_order)) {
    $_shipments = Mage::getResourceModel('sales/order_shipment_collection')
          ->setOrderFilter($_order)->load();
    foreach ($_shipments as $_shipment) {
      foreach ($_shipment->getAllTracks() as $_track) {
        $_tracking_number = $_track->getNumber();

        return sprintf('%s?query=%s&locale=%s', '//tracking.dpd.de/parcelstatus', $_tracking_number, $_locale);
      }
    }
  }
  return $_url;
  }

  public function getMyAccountUrl() {
    return array(
      'label' => $this->__('My Account'),
      'title' => $this->__('My Account'),
      'link' => Mage::getUrl('customer/account/')
    );
  }

  public function getAddressBookUrl() {
    return array(
      'label' => $this->__('My Address'),
      'title' => $this->__('My Address'),
    );
  }

  public function getGiftCardUrl() {
    return array(
      'label' => $this->__('Gift Card'),
      'title' => $this->__('Gift Card'),
      'link' => ''
    );
  }

  public function getAccountInformationUrl() {
    return array(
      'label' => $this->__('Account Information'),
      'title' => $this->__('Account Information'),
      'link' => ''
    );
  }

  public function getHomeUrl() {
    return array(
      'label' => $this->__('Home'),
      'title' => $this->__('Home'),
      'link' => Mage::getUrl('/')
    );
  }

  public function getMyOrdersUrl() {
  return array(
    'label' => $this->__('My Orders'),
    'title' => $this->__('My Orders')
  );
  }

 public function getMyOrdersUrlWithLink() {
  return array(
    'label' => $this->__('My Orders'),
    'title' => $this->__('My Orders'),
    'link' => Mage::getUrl('sales/history'),
  );
  }

  public function getOrderViewUrl() {
  return array(
    'label' => $this->__('Order View'),
    'title' => $this->__('Order View')
  );
  }

  public function getAttributeValueByLabel($_attribute, $_label) {
    $_product = Mage::getModel('catalog/product');
    $_attr = $_product->getResource()->getAttribute($_attribute);
    return $_attr->getSource()->getOptionId($_label);
  }
}
