<?php
class Ecominfinity_Repeatgroup_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/repeatgroup?id=15 
    	 *  or
    	 * http://site.com/repeatgroup/id/15 	
    	 */
    	/* 
		$repeatgroup_id = $this->getRequest()->getParam('id');

  		if($repeatgroup_id != null && $repeatgroup_id != '')	{
			$repeatgroup = Mage::getModel('repeatgroup/repeatgroup')->load($repeatgroup_id)->getData();
		} else {
			$repeatgroup = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($repeatgroup == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$repeatgroupTable = $resource->getTableName('repeatgroup');
			
			$select = $read->select()
			   ->from($repeatgroupTable,array('repeatgroup_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$repeatgroup = $read->fetchRow($select);
		}
		Mage::register('repeatgroup', $repeatgroup);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}