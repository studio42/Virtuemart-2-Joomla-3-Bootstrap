<?php
/**
 *
 * Controller for the front end User maintenance
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
 * @version $Id: user.php 6355 2012-08-20 09:23:27Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

/**
 * VirtueMart Component Controller
 *
 * @package		VirtueMart
 */
class VirtueMartControllerUser extends JController
{

	public function __construct()
	{
		parent::__construct();
		$this->useSSL = VmConfig::get('useSSL',0);
		$this->useXHTML = true;

	}


	function edit(){

	}

	function editAddressST(){

		$view = $this->getView('user', 'html');

		$view->setLayout('edit_address');

		$ftask ='saveAddressST';
		$view->assignRef('fTask', $ftask);
		// Display it all
		$view->display();

	}

	/**
	 * This is for use in the cart, it calls a standard template for editing user adresses. It sets the task following into the form
	 * of the template to saveCartUser, the task saveCartUser just sets the right redirect in the js save(). This is done just to have the
	 * controll flow in the controller and not in the layout. The layout is everytime calling a standard joomla task.
	 *
	 * @author Max Milbers
	 */

	function editAddressCart(){

		$view = $this->getView('user', 'html');

		$view->setLayout('edit_address');

		$ftask ='savecartuser';
		$view->assignRef('fTask', $ftask);

		// Display it all
		$view->display();

	}

	/**
	 * This is for use in the checkout process, it is the same like editAddressCart, but it sets the save task
	 * to saveCheckoutUser, the task saveCheckoutUser just sets the right redirect. This is done just to have the
	 * controll flow in the controller and not in the layout. The layout is everytime calling a standard joomla task.
	 *
	 * @author Max Milbers
	 */
	function editAddressCheckout(){

		$view = $this->getView('user', 'html');

		$view->setLayout('edit_address');

		$ftask ='savecheckoutuser';
		$view->assignRef('fTask', $ftask);

		// Display it all
		$view->display();
	}

	/**
	 * This function is called from the layout edit_adress and just sets the right redirect back to the cart
	 * We use here the saveData(true) function, because within the cart shouldnt be done any registration.
	 *
	 * @author Max Milbers
	 */
	function saveCheckoutUser(){

		$msg = $this->saveData(true);

		//We may add here the option for silent registration.
		$this->setRedirect( JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout',$this->useXHTML,$this->useSSL), $msg );
	}

	function registerCheckoutUser(){
		$msg = $this->saveData(true,true);
		$this->setRedirect(JRoute::_( 'index.php?option=com_virtuemart&view=cart&task=checkout',$this->useXHTML,$this->useSSL ),$msg);
	}

	/**
	 * This function is called from the layout edit_adress and just sets the right redirect back to the cart.
	 * We use here the saveData(true) function, because within the cart shouldnt be done any registration.
	 *
	 * @author Max Milbers
	 */
	function saveCartUser(){

		$msg = $this->saveData(true);
		$this->setRedirect(JRoute::_( 'index.php?option=com_virtuemart&view=cart' ),$msg);
	}

	function registerCartuser(){
		$msg = $this->saveData(true, true);
		$this->setRedirect(JRoute::_('index.php?option=com_virtuemart&view=cart') , $msg);
	}


	/**
	 * This is the save function for the normal user edit.php layout.
	 * We use here directly the userModel store function, because this view is for registering also
	 * it redirects to the standard user view.
	 *
	 * @author Max Milbers
	 */
	function saveUser(){

		$msg = $this->saveData(false,true);
		$layout = JRequest::getWord('layout','edit');
		$this->setRedirect( JRoute::_('index.php?option=com_virtuemart&view=user&layout='.$layout), $msg );
	}

	function saveAddressST(){

		$msg = $this->saveData(false,true,true);
		$layout = 'edit';// JRequest::getWord('layout','edit');
		$this->setRedirect( JRoute::_('index.php?option=com_virtuemart&view=user&layout='.$layout), $msg );

	}

	/**
	 * Save the user info. The saveData function don't use the userModel store function for anonymous shoppers, because it would register them.
	 * We make this function private, so we can do the tests in the tasks.
	 *
	 * @author Max Milbers
	 * @author Valérie Isaksen
	 *
	 * @param boolean Defaults to false, the param is for the userModel->store function, which needs it to determine how to handle the data.
	 * @return String it gives back the messages.
	 */
	private function saveData($cart=false,$register=false, $onlyAddress=false) {
		$mainframe = JFactory::getApplication();
		$currentUser = JFactory::getUser();
		$msg = '';

		$data = JRequest::get('post');

		$data['address_type'] = JRequest::getWord('addrtype','BT');
		if($currentUser->guest!=1 || $register){
			$this->addModelPath( JPATH_VM_ADMINISTRATOR.DS.'models' );
			$userModel = VmModel::getModel('user');

			if(!$cart){
				// Store multiple selectlist entries as a ; separated string
				if (key_exists('vendor_accepted_currencies', $data) && is_array($data['vendor_accepted_currencies'])) {
					$data['vendor_accepted_currencies'] = implode(',', $data['vendor_accepted_currencies']);
				}

				$data['vendor_store_name'] = JRequest::getVar('vendor_store_name','','post','STRING',JREQUEST_ALLOWHTML);
				$data['vendor_store_desc'] = JRequest::getVar('vendor_store_desc','','post','STRING',JREQUEST_ALLOWHTML);
				$data['vendor_terms_of_service'] = JRequest::getVar('vendor_terms_of_service','','post','STRING',JREQUEST_ALLOWHTML);
			}

			//It should always be stored
			if($onlyAddress){
				$ret = $userModel->storeAddress($data);
			} else {
				$ret = $userModel->store($data);
			}
			if($currentUser->guest==1){
				$msg = (is_array($ret)) ? $ret['message'] : $ret;
				$usersConfig = JComponentHelper::getParams( 'com_users' );
				$useractivation = $usersConfig->get( 'useractivation' );
				if (is_array($ret) and $ret['success'] and !$useractivation) {
					// Username and password must be passed in an array
					$credentials = array('username' => $ret['user']->username,
			  					'password' => $ret['user']->password_clear
					);
					$return = $mainframe->login($credentials);
				}
			}

		}

		$this->saveToCart($data);
		return $msg;
	}

	/**
	 * This function just gets the post data and put the data if there is any to the cart
	 *
	 * @author Max Milbers
	 */
	private function saveToCart($data){

		if(!class_exists('VirtueMartCart')) require(JPATH_VM_SITE.DS.'helpers'.DS.'cart.php');
		$cart = VirtueMartCart::getCart();
		$cart->saveAddressInCart($data, $data['address_type']);

	}

	/**
	 * Editing a user address was cancelled when called from the cart; return to the cart
	 *
	 * @author Oscar van Eijk
	 */
	function cancelCartUser(){

		$this->setRedirect( JRoute::_('index.php?option=com_virtuemart&view=cart')  );
	}

	/**
	 * Editing a user address was cancelled during chaeckout; return to the cart
	 *
	 * @author Oscar van Eijk
	 */
	function cancelCheckoutUser(){
		$this->setRedirect( JRoute::_('index.php?option=com_virtuemart&view=cart&task=checkout',$this->useXHTML,$this->useSSL) );
	}

	/**
	 * Action cancelled; return to the previous view
	 *
	 * @author Oscar van Eijk
	 */
	function cancel()
	{
		$return = JURI::base();
		$this->setRedirect( $return );
	}


	function removeAddressST(){
		$db = JFactory::getDBO();
		$currentUser = JFactory::getUser();
		$virtuemart_userinfo_id = JRequest::getVar('virtuemart_userinfo_id');

		//Lets do it dirty for now
		//$userModel = VmModel::getModel('user');
		$msg = '';
		if ( isset($virtuemart_userinfo_id) && $currentUser->id != 0 ) {
			//$userModel -> deleteAddressST();
			$q = 'DELETE FROM #__virtuemart_userinfos  WHERE virtuemart_user_id="'. $currentUser->id .'" AND virtuemart_userinfo_id="'. $virtuemart_userinfo_id .'"';
			$db->setQuery($q);
			$db->query();

			$msg = vmInfo('Address has been successfully deleted.');
		}
		$layout = JRequest::getWord('layout','edit');
		$this->setRedirect( JRoute::_('index.php?option=com_virtuemart&view=user&layout='.$layout), $msg );
	}
}
// No closing tag
