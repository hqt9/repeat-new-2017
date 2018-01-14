<?php

class Ecominfinity_Storelocator_Helper_Data extends Mage_Core_Helper_Abstract {
  public function getStoreJson($_is_frontend_end = true) {
    $_stores = Mage::getModel('storelocator/storelocator')->getCollection()->getItems();
    $_result = array();
    foreach ($_stores as $_store) {
      $_id = $_store->getData('entity_id');

      $_data = $_store->getData();
      if ($_is_frontend_end && $_data['status'] == '0') { continue; }
      $_data['opening_hours'] = json_decode($_data['opening_hours']);
      $_data['country_name'] = $this->getCountryNameByCode($_data['country']);
      $_data['main_image'] = Mage::getBaseUrl('media') . $_data['main_image'];
      $_data['continent'] = $this->__($_data['continent']);
      if ($_is_frontend_end) {
        $_data['content'] = Mage::helper('cms')->getBlockTemplateProcessor()->filter($_data['content']);
      }

      $_result[$_id] = $_data;
    }
    return json_encode($_result);
  }

  public function getCountryNameByCode($_code) {
    return Mage::getModel('directory/country')->loadByCode($_code)->getName();
  }

  public function getCountryJson() {
    $_countries = Mage::getModel('directory/country')->getCollection()->getItems();
    $_result = array();
    foreach ($_countries as $_country) {
      $_code = $_country->getData('country_id');
      $_country = Mage::getModel('directory/country')->loadByCode($_code);
      $_result[$_code] = $_country->getName();
    }
    return json_encode($_result);
  }

  public function getSyncUrl($_type) {
    switch ($_type) {
      case 'create':
        return Mage::helper('adminhtml')->getUrl('adminhtml/storelocator/create');
      case 'update':
        return Mage::helper('adminhtml')->getUrl('adminhtml/storelocator/update');
      case 'delete':
        return Mage::helper('adminhtml')->getUrl('adminhtml/storelocator/delete');
    }
  }

  public function getUrlJson() {
    $_urls = array(
      'stores' => array(
        'create' => $this->getSyncUrl('create'),
        'update' => $this->getSyncUrl('update'),
        'delete' => $this->getSyncUrl('delete')
      )
    );

    return json_encode($_urls);
  }

  public function getContinentByCountryCode($_code) {
    $_mapping = array('AD' => 'Europe', 'AE' => 'Asia', 'AF' => 'Asia', 'AG' => 'North America', 'AI' => 'North America', 'AL' => 'Europe', 'AM' => 'Asia', 'AN' => 'North America', 'AO' => 'Africa', 'AP' => 'Asia', 'AQ' => 'Antarctica', 'AR' => 'South America', 'AS' => 'Oceania', 'AT' => 'Europe', 'AU' => 'Oceania', 'AW' => 'North America', 'AX' => 'Europe', 'AZ' => 'Asia', 'BA' => 'Europe', 'BB' => 'North America', 'BD' => 'Asia', 'BE' => 'Europe', 'BF' => 'Africa', 'BG' => 'Europe', 'BH' => 'Asia', 'BI' => 'Africa', 'BJ' => 'Africa', 'BL' => 'North America', 'BM' => 'North America', 'BN' => 'Asia', 'BO' => 'South America', 'BR' => 'South America', 'BS' => 'North America', 'BT' => 'Asia', 'BV' => 'Antarctica', 'BW' => 'Africa', 'BY' => 'Europe', 'BZ' => 'North America', 'CA' => 'North America', 'CC' => 'Asia', 'CD' => 'Africa', 'CF' => 'Africa', 'CG' => 'Africa', 'CH' => 'Europe', 'CI' => 'Africa', 'CK' => 'Oceania', 'CL' => 'South America', 'CM' => 'Africa', 'CN' => 'Asia', 'CO' => 'South America', 'CR' => 'North America', 'CU' => 'North America', 'CV' => 'Africa', 'CX' => 'Asia', 'CY' => 'Asia', 'CZ' => 'Europe', 'DE' => 'Europe', 'DJ' => 'Africa', 'DK' => 'Europe', 'DM' => 'North America', 'DO' => 'North America', 'DZ' => 'Africa', 'EC' => 'South America', 'EE' => 'Europe', 'EG' => 'Africa', 'EH' => 'Africa', 'ER' => 'Africa', 'ES' => 'Europe', 'ET' => 'Africa', 'EU' => 'Europe', 'FI' => 'Europe', 'FJ' => 'Oceania', 'FK' => 'South America', 'FM' => 'Oceania', 'FO' => 'Europe', 'FR' => 'Europe', 'FX' => 'Europe', 'GA' => 'Africa', 'GB' => 'Europe', 'GD' => 'North America', 'GE' => 'Asia', 'GF' => 'South America', 'GG' => 'Europe', 'GH' => 'Africa', 'GI' => 'Europe', 'GL' => 'North America', 'GM' => 'Africa', 'GN' => 'Africa', 'GP' => 'North America', 'GQ' => 'Africa', 'GR' => 'Europe', 'GS' => 'Antarctica', 'GT' => 'North America', 'GU' => 'Oceania', 'GW' => 'Africa', 'GY' => 'South America', 'HK' => 'Asia', 'HM' => 'Antarctica', 'HN' => 'North America', 'HR' => 'Europe', 'HT' => 'North America', 'HU' => 'Europe', 'ID' => 'Asia', 'IE' => 'Europe', 'IL' => 'Asia', 'IM' => 'Europe', 'IN' => 'Asia', 'IO' => 'Asia', 'IQ' => 'Asia', 'IR' => 'Asia', 'IS' => 'Europe', 'IT' => 'Europe', 'JE' => 'Europe', 'JM' => 'North America', 'JO' => 'Asia', 'JP' => 'Asia', 'KE' => 'Africa', 'KG' => 'Asia', 'KH' => 'Asia', 'KI' => 'Oceania', 'KM' => 'Africa', 'KN' => 'North America', 'KP' => 'Asia', 'KR' => 'Asia', 'KW' => 'Asia', 'KY' => 'North America', 'KZ' => 'Asia', 'LA' => 'Asia', 'LB' => 'Asia', 'LC' => 'North America', 'LI' => 'Europe', 'LK' => 'Asia', 'LR' => 'Africa', 'LS' => 'Africa', 'LT' => 'Europe', 'LU' => 'Europe', 'LV' => 'Europe', 'LY' => 'Africa', 'MA' => 'Africa', 'MC' => 'Europe', 'MD' => 'Europe', 'ME' => 'Europe', 'MF' => 'North America', 'MG' => 'Africa', 'MH' => 'Oceania', 'MK' => 'Europe', 'ML' => 'Africa', 'MM' => 'Asia', 'MN' => 'Asia', 'MO' => 'Asia', 'MP' => 'Oceania', 'MQ' => 'North America', 'MR' => 'Africa', 'MS' => 'North America', 'MT' => 'Europe', 'MU' => 'Africa', 'MV' => 'Asia', 'MW' => 'Africa', 'MX' => 'North America', 'MY' => 'Asia', 'MZ' => 'Africa', 'NA' => 'Africa', 'NC' => 'Oceania', 'NE' => 'Africa', 'NF' => 'Oceania', 'NG' => 'Africa', 'NI' => 'North America', 'NL' => 'Europe', 'NO' => 'Europe', 'NP' => 'Asia', 'NR' => 'Oceania', 'NU' => 'Oceania', 'NZ' => 'Oceania', 'OM' => 'Asia', 'PA' => 'North America', 'PE' => 'South America', 'PF' => 'Oceania', 'PG' => 'Oceania', 'PH' => 'Asia', 'PK' => 'Asia', 'PL' => 'Europe', 'PM' => 'North America', 'PN' => 'Oceania', 'PR' => 'North America', 'PS' => 'Asia', 'PT' => 'Europe', 'PW' => 'Oceania', 'PY' => 'South America', 'QA' => 'Asia', 'RE' => 'Africa', 'RO' => 'Europe', 'RS' => 'Europe', 'RU' => 'Europe', 'RW' => 'Africa', 'SA' => 'Asia', 'SB' => 'Oceania', 'SC' => 'Africa', 'SD' => 'Africa', 'SE' => 'Europe', 'SG' => 'Asia', 'SH' => 'Africa', 'SI' => 'Europe', 'SJ' => 'Europe', 'SK' => 'Europe', 'SL' => 'Africa', 'SM' => 'Europe', 'SN' => 'Africa', 'SO' => 'Africa', 'SR' => 'South America', 'ST' => 'Africa', 'SV' => 'North America', 'SY' => 'Asia', 'SZ' => 'Africa', 'TC' => 'North America', 'TD' => 'Africa', 'TF' => 'Antarctica', 'TG' => 'Africa', 'TH' => 'Asia', 'TJ' => 'Asia', 'TK' => 'Oceania', 'TL' => 'Asia', 'TM' => 'Asia', 'TN' => 'Africa', 'TO' => 'Oceania', 'TR' => 'Europe', 'TT' => 'North America', 'TV' => 'Oceania', 'TW' => 'Asia', 'TZ' => 'Africa', 'UA' => 'Europe', 'UG' => 'Africa', 'UM' => 'Oceania', 'US' => 'North America', 'UY' => 'South America', 'UZ' => 'Asia', 'VA' => 'Europe', 'VC' => 'North America', 'VE' => 'South America', 'VG' => 'North America', 'VI' => 'North America', 'VN' => 'Asia', 'VU' => 'Oceania', 'WF' => 'Oceania', 'WS' => 'Oceania', 'YE' => 'Asia', 'YT' => 'Africa', 'ZA' => 'Africa', 'ZM' => 'Africa', 'ZW' => 'Africa');
    if (isset($_mapping[$_code])) {
      return $_mapping[$_code];
    }
  }
}