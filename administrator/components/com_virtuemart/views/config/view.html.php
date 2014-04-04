<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Config
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 6299 2012-07-25 22:53:11Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');
jimport('joomla.html.pane');
jimport('joomla.version');

/**
 * HTML View class for the configuration maintenance
 *
 * @package		VirtueMart
 * @subpackage 	Config
 * @author 		RickG
 */
class VirtuemartViewConfig extends VmView {

	function display($tpl = null) {

		$this->loadHelper('image');
		$this->loadHelper('html');

		$model = VmModel::getModel();
		$usermodel = VmModel::getModel('user');

		JToolBarHelper::title( JText::_('COM_VIRTUEMART_CONFIG') , 'head vm_config_48');

		$this->addStandardEditViewCommands();

		$config = VmConfig::loadConfig();
		if(!empty($config->_params)){
			unset ($config->_params['pdf_invoice']); // parameter remove and replaced by inv_os
		}
		$this->config = $config;

		$this->userparams = JComponentHelper::getParams('com_users');
		$this->jTemplateList = ShopFunctions::renderTemplateList(JText::_('COM_VIRTUEMART_ADMIN_CFG_JOOMLA_TEMPLATE_DEFAULT'));
		$this->vmLayoutList = $model->getLayoutList('virtuemart');
		$this->categoryLayoutList = $model->getLayoutList('category');
		$this->productLayoutList = $model->getLayoutList('productdetails');
		$this->noimagelist = $model->getNoImageList();
		$this->orderStatusModel=VmModel::getModel('orderstatus');
		$this->currConverterList = $model->getCurrencyConverterList();
		$this->moduleList = $model->getModuleList();
		$this->activeLanguages = $model->getActiveLanguages( VmConfig::get('active_languages') );
		$this->orderByFields = $model->getProductFilterFields('browse_orderby_fields');
		$this->searchFields = $model->getProductFilterFields( 'browse_search_fields');
		
		$this->aclGroups = $usermodel->getAclGroupIndentedTree();
		
		if(is_Dir(VmConfig::get('vmtemplate').DS.'images'.DS.'availability'.DS)){
			$imagePath = VmConfig::get('vmtemplate').'/images/availability/';
		} else {
			$imagePath = '/components/com_virtuemart/assets/images/availability/';
		}
		$this->imagePath = $imagePath;

		shopFunctions::checkSafePath();
		$this -> checkVmUserVendor();

		parent::display($tpl);
	}

	private function checkVmUserVendor(){

		$db = JFactory::getDBO();
		$multix = Vmconfig::get('multix','none');

		$q = 'select * from #__virtuemart_vmusers where user_is_vendor = 1';// and virtuemart_vendor_id '.$vendorWhere.' limit 1';
		$db->setQuery($q);
		$r = $db->loadAssocList();

		if (empty($r)){
			vmWarn('Your Virtuemart installation contains an error: No user as marked as vendor. Please fix this in your phpMyAdmin and set #__virtuemart_vmusers.user_is_vendor = 1 and #__virtuemart_vmusers.virtuemart_vendor_id = 1 to one of your administrator users. Please update all users to be associated with virtuemart_vendor_id 1.');
		} else {
			if($multix=='none' and count($r)!=1){
				vmWarn('You are using single vendor mode, but it seems more than one user is set as vendor');
			}
			foreach($r as $entry){
				if(empty($entry['virtuemart_vendor_id'])){
					vmWarn('The user with virtuemart_user_id = '.$entry['virtuemart_user_id'].' is set as vendor, but has no referencing vendorId.');
				}
			}
		}
	}

}
// pure php no closing tag
