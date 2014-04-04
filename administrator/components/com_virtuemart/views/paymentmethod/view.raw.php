<?php
/**
 *
 * Calc View
 *
 * @package	VirtueMart
 * @subpackage Payment Method
 * @author Max Milbers
 * @author valérie isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 5601 2012-03-04 18:22:24Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');

/**
 * Description
 *
 * @package		VirtueMart
 * @author valérie isaksen
 */
if (!class_exists('VirtueMartModelCurrency'))
require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'currency.php');

class VirtuemartViewPaymentMethod extends VmView {

	function display($tpl = null) {

		// Load the helper(s)
		$this->loadHelper('permissions');
		$this->loadHelper('html');

		if (!class_exists ('vmPlugin')) {
			require(JPATH_VM_PLUGINS . DS . 'vmplugin.php');
		}
		$this->perms = Permissions::getInstance();

		$model = VmModel::getModel();

		//@todo should be depended by loggedVendor
		//		$vendorId=1;
		//		$this->assignRef('vendorId', $vendorId);
		// TODO logo
		$vendorModel = VmModel::getModel('vendor');
		$vendorModel->setId(1);
		$vendor = $vendorModel->getVendor();
		$currencyModel = VmModel::getModel('currency');
		$currency = $currencyModel->getCurrency($vendor->vendor_currency);

		$this->vendor_currency = $currency->currency_symbol;
		$this->addStandardDefaultViewCommands();
		$this->addStandardDefaultViewLists($model);
		$this->payments = $model->getPayments();
		$this->pagination = $model->getPagination();
		// know payment list
		$this->installedPayments = $this->PaymentPlgList();

		parent::display('results');
		echo $this->AjaxScripts();
	}


	/**
	 * Builds a list to choose the Payment type
	 *
	 * @copyright 	Copyright (c) 2009 VirtueMart Team. All rights reserved.
	 * @author 		Max Milbers
	 * @param 	$selected 	the selected values, may be single data or array
	 * @return 	$list 		list of the Entrypoints
	 * @deprecated
	 */

	function renderPaymentTypesList($selected){

		$list = array(
		'0' => array('payment_type' => 'C', 'payment_type_name' => JText::_('COM_VIRTUEMART_PAYMENT_FORM_CREDIT')),
		'1' => array('payment_type' => 'Y', 'payment_type_name' => JText::_('COM_VIRTUEMART_PAYMENT_FORM_USE_PP')),
		'2' => array('payment_type' => 'B', 'payment_type_name' => JText::_('COM_VIRTUEMART_PAYMENT_FORM_BANK_DEBIT')),
		'3' => array('payment_type' => 'N', 'payment_type_name' => JText::_('COM_VIRTUEMART_PAYMENT_FORM_AO')),
		'4' => array('payment_type' => 'P', 'payment_type_name' => JText::_('COM_VIRTUEMART_PAYMENT_FORM_FORMBASED'))
		);

		$listHTML = JHTML::_('Select.genericlist', $list, 'payment_type', '', 'payment_type', 'payment_type_name', $selected );
		return $listHTML;
	}
	/*
	 *
	* @deprecated
	*/
	function renderPaymentRadioList($selected){

		$list = array(
		'0' => array('payment_type' => 'C', 'payment_type_name' => JText::_('COM_VIRTUEMART_PAYMENT_FORM_CREDIT')),
		'1' => array('payment_type' => 'Y', 'payment_type_name' => JText::_('COM_VIRTUEMART_PAYMENT_FORM_USE_PP')),
		'2' => array('payment_type' => 'B', 'payment_type_name' => JText::_('COM_VIRTUEMART_PAYMENT_FORM_BANK_DEBIT')),
		'3' => array('payment_type' => 'N', 'payment_type_name' => JText::_('COM_VIRTUEMART_PAYMENT_FORM_AO')),
		'4' => array('payment_type' => 'P', 'payment_type_name' => JText::_('COM_VIRTUEMART_PAYMENT_FORM_FORMBASED'))
		);

		$listHTML='';
		foreach($list as $item){
			if($item['payment_type']==$selected) $checked='checked="checked"'; else $checked='';
			if($item['payment_type']=='Y' || $item['payment_type']=='C') $id = 'pam_type_CC_on'; else $id='pam_type_CC_off';
			$listHTML .= '<input id="'.$id.'" type="radio" name="payment_type" value="'.$item['payment_type'].'" '.$checked.'>'.$item['payment_type_name'].' <br />';
		}

		return $listHTML;
	}

	function InstalledPaymentPlgSelectList($selected,$enabled=1){

		$db = JFactory::getDBO();
		$q = 'SELECT * FROM `#__extensions` WHERE `folder` = "vmpayment" AND `enabled`="1" ';
		$db->setQuery($q);
		$result = $db->loadAssocList('extension_id');
		if(empty($result)){
			$app = JFactory::getApplication();
			$app -> enqueueMessage(JText::_('COM_VIRTUEMART_NO_PAYMENT_PLUGINS_INSTALLED'));
		}
		$listHTML='<select id="payment_jplugin_id" name="payment_jplugin_id">';
		// if(!class_exists('JParameter')) require(JPATH_VM_LIBRARIES.DS.'joomla'.DS.'html'.DS.'parameter.php' );
		foreach($result as $paym){
			$params = new JRegistry();
			$params->loadString($paym['params']);
			// $params = new JParameter($paym['params']);
			if($paym['extension_id']==$selected) $checked='selected="selected"'; else $checked='';
			// Get plugin info
			$pType = $params->get('pType');
			if($pType=='Y' || $pType=='C') $id = 'pam_type_CC_on'; else $id='pam_type_CC_off';
			$listHTML .= '<option id="'.$id.'" '.$checked.' value="'.$paym['extension_id'].'">'.JText::_($paym['name']).'</option>';

		}
		$listHTML .= '</select>';

		return $listHTML;
	}
	// list all payement(enabled or disabeld
	function PaymentPlgList(){
		$db = JFactory::getDBO();
		$q = 'SELECT * FROM `#__extensions` WHERE `folder` = "vmpayment"';// AND `enabled`="1" ';
		$db->setQuery($q);
		return $db->loadObjectList('extension_id');
	}
}
// pure php not tag
