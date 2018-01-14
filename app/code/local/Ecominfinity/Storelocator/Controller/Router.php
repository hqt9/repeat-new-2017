<?php

class Ecominfinity_Storelocator_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard {
  public function getModuleByFrontName($frontName) {
    $_alias = ['storelocator', 'store_locator', 'shopsuche', 'trouver-un-magasin.html'];
    return (in_array($frontName, $_alias)) ? ['Ecominfinity_Storelocator'] : false;
  }

  public function match(Zend_Controller_Request_Http $request) {
        //checking before even try to find out that current module
        //should use this router
        if (!$this->_beforeModuleMatch()) {
            return false;
        }

        $this->fetchDefault();

        $front = $this->getFront();
        $path = trim($request->getPathInfo(), '/');

        if ($path) {
            $p = explode('/', $path);
        } else {
            $p = explode('/', $this->_getDefaultPath());
        }

        // get module name
        if ($request->getModuleName()) {
            $module = $request->getModuleName();
        } else {
            if (!empty($p[0])) {
                $module = $p[0];
            } else {
                $module = $this->getFront()->getDefault('module');
                $request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, '');
            }
        }
        if (!$module) {
            if (Mage::app()->getStore()->isAdmin()) {
                $module = 'admin';
            } else {
                return false;
            }
        }

        $modules = $this->getModuleByFrontName($module);

        if ($modules === false) {
            return false;
        }

        // checks after we found out that this router should be used for current module
        if (!$this->_afterModuleMatch()) {
            return false;
        }

        // check whether the store exist
        if (count($p) > 1) {
            $_query = strtolower($p[1]);

            $_stores = Mage::getModel('storelocator/storelocator')->getCollection()->getItems();

            $_flag = false;
            foreach ($_stores as $_store) {
                if ($_store->getData('status') != '1') {
                    continue;
                }
                
                if ($_query == $_store->getData('url_key') ||
                    $_query == strtolower($_store->getData('country')) ||
                    $_query == strtolower($_store->getData('city'))) {
                    $_flag = true;
                    break;
                }
            }

            if ($_flag === false) {
                Mage::app()->getResponse()
                    ->setRedirect(Mage::getUrl($p[0]), 301)->sendResponse();
                die();
            }
        }

        $request->setRouteName('storelocator');
        $controller = 'index';
        $action = 'index';
        $this->_checkShouldBeSecure($request, '/'.$module.'/'.$controller.'/'.$action);
        $controllerClassName = $this->_validateControllerClassName('Ecominfinity_Storelocator', $controller);
        $controllerInstance = Mage::getControllerInstance($controllerClassName, $request, $front->getResponse());
        $request->setModuleName('storelocator');
        $request->setControllerName($controller);
        $request->setActionName($action);
        $request->setControllerModule('Ecominfinity_Storelocator');

        // dispatch action
        $request->setDispatched(true);
        $controllerInstance->dispatch($action);

        return true;
    }
}