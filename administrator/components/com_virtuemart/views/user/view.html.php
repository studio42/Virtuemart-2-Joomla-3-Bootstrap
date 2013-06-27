<?php
/**
 *
 * List/add/edit/remove Users
 *
 * @package	VirtueMart
 * @subpackage User
 * @author Oscar van Eijk
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 6477 2012-09-24 14:33:54Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmview.php');
jimport('joomla.version');

/**
 * HTML View class for maintaining the list of users
 *
 * @package	VirtueMart
 * @subpackage User
 * @author Oscar van Eijk
 */
class VirtuemartViewUser extends VmView {

	function display($tpl = null) {

		// Load the helper(s)
		$this->loadHelper('html');
		if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
		$perm = Permissions::getInstance();
		$this->assignRef('perm',$perm);

		$model = VmModel::getModel();

		$currentUser = JFactory::getUser();


		$task = JRequest::getWord('task', 'edit');
		if($task == 'editshop'){

			if(Vmconfig::get('multix','none') !=='none'){
				//Maybe we must check here if the user is vendor and if he has an own id and else map to mainvendor.
				$userId = 0;
			} else {
				if(!class_exists('VirtueMartModelVendor')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'vendor.php');
				$userId = VirtueMartModelVendor::getUserIdByVendorId(1);
			}
			$this->SetViewTitle('STORE'  );
		} else if ($task == 'add'){
			$userId  = 0;
		} else {
			$userId = JRequest::getVar('virtuemart_user_id',0);
			if(is_array($userId)){
				$userId = $userId[0];
			}
			$this->SetViewTitle('USER');
		}
		$userId = $model->setId($userId);

		$layoutName = JRequest::getWord('layout', 'default');
		$layoutName = $this->getLayout();

		if ($layoutName == 'edit' || $layoutName == 'edit_shipto') {

			$editor = JFactory::getEditor();

			// Get the required helpers
			$this->loadHelper('shoppergroup');
			$this->loadHelper('image');

			//$userFieldsModel = VmModel::getModel('userfields');

			$userDetails = $model->getUser();

			if($task == 'editshop' && $userDetails->user_is_vendor){
// 				$model->setCurrent();
				if(!empty($userDetails->vendor->vendor_store_name)){
					$this->SetViewTitle('STORE',$userDetails->vendor->vendor_store_name );
				} else {
					$this->SetViewTitle('STORE',JText::_('COM_VIRTUEMART_NEW_VENDOR') );
				}
				$vendorid = $userDetails->virtuemart_vendor_id;
			} else {
				$vendorid = 0 ;
				$this->SetViewTitle('USER',$userDetails->JUser->get('name'));
			}

			$_new = ($userDetails->JUser->get('id') < 1);

			$this->addStandardEditViewCommands($vendorid);

			// User details
			$_contactDetails = $model->getContactDetails();
			$_groupList = $model->getGroupList();

			if (!is_array($_groupList)) {
				$this->lists['gid'] = '<input type="hidden" name="gid" value="'. $userDetails->JUser->get('gid') .'" /><strong>'. JText::_($_groupList) .'</strong>';
			} else {
				$this->lists['gid'] 	= JHTML::_('select.genericlist', $_groupList, 'gid', 'size="10"', 'value', 'text', $userDetails->JUser->get('gid'));
			}

			$this->lists['canBlock'] = ($currentUser->authorize('com_users', 'block user')
			&& ($userDetails->JUser->get('id') != $currentUser->get('id'))); // Can't block myself
			$this->lists['canSetMailopt'] = $currentUser->authorize('workflow', 'email_events');
			$this->lists['block'] = JHTML::_('select.booleanlist', 'block',      'class="inputbox"', $userDetails->JUser->get('block'),     'COM_VIRTUEMART_YES', 'COM_VIRTUEMART_NO');
			$this->lists['sendEmail'] = JHTML::_('select.booleanlist', 'sendEmail',  'class="inputbox"', $userDetails->JUser->get('sendEmail'), 'COM_VIRTUEMART_YES', 'COM_VIRTUEMART_NO');
			$this->lists['params'] = $userDetails->JUser->getParameters(true);

			// Shopper info
			$this->lists['shoppergroups'] = ShopFunctions::renderShopperGroupList($userDetails->shopper_groups,true);
			$this->lists['vendors'] = ShopFunctions::renderVendorList($userDetails->virtuemart_vendor_id);
			$model->setId($userDetails->JUser->get('id'));
			$this->lists['custnumber'] = $model->getCustomerNumberById();

			// Shipment address(es)
			$this->lists['shipTo'] = ShopFunctions::generateStAddressList($this,$model,'addST');

			$new = false;
			if(JRequest::getInt('new','0')===1){
				$new = true;
			}

			$virtuemart_userinfo_id_BT = $model->getBTuserinfo_id($userId);
			$userFieldsArray = $model->getUserInfoInUserFields($layoutName,'BT',$virtuemart_userinfo_id_BT,false);
			$userFieldsBT = $userFieldsArray[$virtuemart_userinfo_id_BT];


			//$this->lists['perms'] = JHTML::_('select.genericlist', Permissions::getUserGroups(), 'perms', '', 'group_name', 'group_name', $userDetails->perms);

			// Load the required scripts
			if (count($userFieldsBT['scripts']) > 0) {
				foreach ($userFieldsBT['scripts'] as $_script => $_path) {
					JHTML::script($_script, $_path);
				}
			}
			// Load the required stylesheets
			if (count($userFieldsBT['links']) > 0) {
				foreach ($userFieldsBT['links'] as $_link => $_path) {
					JHTML::stylesheet($_link, $_path);
				}
			}

			$this->assignRef('userFieldsBT', $userFieldsBT);
			$this->assignRef('userInfoID', $virtuemart_userinfo_id_BT);


			$virtuemart_userinfo_id = JRequest::getString('virtuemart_userinfo_id', '0','');
			$userFieldsArray = $model->getUserInfoInUserFields($layoutName,'ST',$virtuemart_userinfo_id,false);

			if($new ){
				$virtuemart_userinfo_id = 0;
// 				$userFieldsST = $userFieldsArray[$virtuemart_userinfo_id];
			} else {
// 				$userFieldsST = $userFieldsArray[$virtuemart_userinfo_id];
// 				if(empty($virtuemart_userinfo_id)){
// 					$virtuemart_userinfo_id = $model->getBTuserinfo_id();
// 				}
			}
			$userFieldsST = $userFieldsArray[$virtuemart_userinfo_id];

			$this->assignRef('shipToFields', $userFieldsST);
			$this->assignRef('shipToId', $virtuemart_userinfo_id);
			$this->assignRef('new', $new);

			if (!$_new) {
				// Check for existing orders for this user
				$orders = VmModel::getModel('orders');
				$orderList = $orders->getOrdersList($userDetails->JUser->get('id'), true);
			} else {
				$orderList = null;
			}


			if (count($orderList) > 0 || !empty($userDetails->user_is_vendor)) {
				if (!class_exists('CurrencyDisplay')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'currencydisplay.php');
				$currency = CurrencyDisplay::getInstance();
				$this->assignRef('currency',$currency);
			}

// 			vmdebug('user $userDetails ',	$userDetails 	);
			if (!empty($userDetails->user_is_vendor)) {

				$vendorModel = VmModel::getModel('vendor');
				$vendorModel->setId($userDetails->virtuemart_vendor_id);


				$vendorModel->addImages($userDetails->vendor);
				$this->assignRef('vendor', $userDetails->vendor);

				$currencyModel = VmModel::getModel('currency');
				$_currencies = $currencyModel->getCurrencies();
				$this->assignRef('currencies', $_currencies);

			}


			$this->assignRef('userDetails', $userDetails);

			$this->assignRef('orderlist', $orderList);
			$this->assignRef('contactDetails', $_contactDetails);
			$this->assignRef('editor', $editor);

		} else {

			JToolBarHelper::divider();
			JToolBarHelper::custom('toggle.user_is_vendor.1', 'publish','','COM_VIRTUEMART_USER_ISVENDOR');
			JToolBarHelper::custom('toggle.user_is_vendor.0', 'unpublish','','COM_VIRTUEMART_USER_ISNOTVENDOR');
			JToolBarHelper::divider();
			JToolBarHelper::deleteList();
			JToolBarHelper::editListX();

			//This is intentionally, creating new user via BE is buggy and can be done by joomla
			//JToolBarHelper::addNewX();
			$this->addStandardDefaultViewLists($model,'ju.id');

			$userList = $model->getUserList();
			$this->assignRef('userList', $userList);

			$pagination = $model->getPagination();
			$this->assignRef('pagination', $pagination);

			$shoppergroupmodel = VmModel::getModel('shopperGroup');
			$defaultShopperGroup = $shoppergroupmodel->getDefault(0)->shopper_group_name;
			$this->assignRef('defaultShopperGroup', $defaultShopperGroup);
		}

		parent::display($tpl);
	}

	/*
	*	What is this doing here?
	*
	*/

	function renderMailLayout ($doVendor=false) {
		$tpl = ($doVendor) ? 'mail_html_regvendor' : 'mail_html_reguser';
		$this->setLayout($tpl);

		$vendorModel = VmModel::getModel('vendor');
		$vendorId = 1;
		$vendorModel->setId($vendorId);
		$vendor = $vendorModel->getVendor();
		$vendorModel->addImages($vendor);
		$this->assignRef('subject', ($doVendor) ? JText::sprintf('COM_VIRTUEMART_NEW_USER_MESSAGE_VENDOR_SUBJECT', $this->user->get('email')) : JText::sprintf('COM_VIRTUEMART_NEW_USER_MESSAGE_SUBJECT',$vendor->vendor_store_name));
		parent::display();
	}

}

//No Closing Tag
