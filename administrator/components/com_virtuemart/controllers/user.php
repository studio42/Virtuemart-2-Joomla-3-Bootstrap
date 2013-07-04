<?php
/**
*
* User controller
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
* @version $Id: user.php 6071 2012-06-06 15:33:04Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');


/**
 * Controller class for the user
 *
 * @package    	VirtueMart
 * @subpackage 	User
 * @author     	Oscar van Eijk
 * @author 		Max Milbers
 */
class VirtuemartControllerUser extends VmController {

	/**
	 * Method to display the view
	 *
	 * @access public
	 * @author
	 */
	function __construct(){
		VmConfig::loadJLang('com_virtuemart_shoppers',TRUE);
		parent::__construct('virtuemart_user_id');
	}

	/**
	 * Handle the edit task
	 */
	function edit($view=0){

		//We set here the virtuemart_user_id, when no virtuemart_user_id is set to 0, for adding a new user
		//In every other case the virtuemart_user_id is sent.
		$cid = JRequest::getVar('virtuemart_user_id');
		if(!isset($cid)) JRequest::setVar('virtuemart_user_id', (int)0);

		parent::edit('edit');
	}

	function addST(){

		$this->edit();
	}

	function editshop(){

		$user = JFactory::getUser();
		//the virtuemart_user_id var gets overriden in the edit function, when not set. So we must set it here
		JRequest::setVar('virtuemart_user_id', (int)$user->id);
		$this->edit();

	}
	function cancel(){

		$lastTask = JRequest::getWord('last_task');
		if ($lastTask == 'edit_shop') $this->setRedirect('index.php?option=com_virtuemart');
		else $this->setRedirect('index.php?option=com_virtuemart&view=user');
	}

	/**
	 * Handle the save task
	 * Checks already in the controller the rights todo so and sets the data by filtering the post
	 *
	 * @author Max Milbers
	 */
	function save($data = 0){

		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$view = $this->getView('user', $viewType);

		$_currentUser = JFactory::getUser();
// TODO sortout which check is correctt.....
//		if (!$_currentUser->authorize('administration', 'manage', 'components', 'com_users')) {
		if (!$_currentUser->authorize('com_users', 'manage')) {
			$msg = JText::_(_NOT_AUTH);
		} else {
			$model = VmModel::getModel('user');

			$data = JRequest::get('post');

			// Store multiple selectlist entries as a ; separated string
			if (key_exists('vendor_accepted_currencies', $data) && is_array($data['vendor_accepted_currencies'])) {
			    $data['vendor_accepted_currencies'] = implode(',', $data['vendor_accepted_currencies']);
			}
			// TODO disallow vendor_store_name as HTML ?
			$data['vendor_store_name'] = JRequest::getVar('vendor_store_name','','post','STRING',JREQUEST_ALLOWHTML);
			$data['vendor_store_desc'] = JRequest::getVar('vendor_store_desc','','post','STRING',JREQUEST_ALLOWHTML);
			$data['vendor_terms_of_service'] = JRequest::getVar('vendor_terms_of_service','','post','STRING',JREQUEST_ALLOWHTML);
			$data['vendor_legal_info'] = JRequest::getVar('vendor_legal_info','','post','STRING',JREQUEST_ALLOWHTML);
			$data['vendor_letter_css'] = JRequest::getVar('vendor_letter_css','','post','STRING',JREQUEST_ALLOWHTML);
			$data['vendor_letter_header_html'] = JRequest::getVar('vendor_letter_header_html','','post','STRING',JREQUEST_ALLOWHTML);
			$data['vendor_letter_footer_html'] = JRequest::getVar('vendor_letter_footer_html','','post','STRING',JREQUEST_ALLOWHTML);

			$ret=$model->store($data);
			if(!$ret){
				$msg = '';
			} else {
				$msg = $ret['message'];
			}

		}
		$cmd = JRequest::getCmd('task');
		$lastTask = JRequest::getWord('last_task');
		if($cmd == 'apply'){
			if ($lastTask == 'editshop') $redirection = 'index.php?option=com_virtuemart&view=user&task=editshop';
			else $redirection = 'index.php?option=com_virtuemart&view=user&task=edit&virtuemart_user_id[]='.$ret['newId'];
		} else {
			if ($lastTask == 'editshop') $redirection = 'index.php?option=com_virtuemart';
			else $redirection = 'index.php?option=com_virtuemart&view=user';
		}
// 		$this->setRedirect($redirection, $ret['message']);
		$this->setRedirect($redirection);
	}


}

//No Closing tag
