<?php
/**
*
* Controller for the front end Manufacturerviews
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
* @version $Id: manufacturer.php 2420 2010-06-01 21:12:57Z oscar $
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
class VirtueMartControllerVendor extends JController
{

	/**
	* Send the ask question email.
	* @author Kohl Patrick, Christopher Roussel
	*/
	public function mailAskquestion () {

		JRequest::checkToken() or jexit( 'Invalid Token' );

		if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php');
		$this->addModelPath(JPATH_VM_ADMINISTRATOR.DS.'models');
		$model = VmModel::getModel('vendor');
		$mainframe = JFactory::getApplication();
		$vars = array();
		$min = VmConfig::get('asks_minimum_comment_length', 50)+1;
		$max = VmConfig::get('asks_maximum_comment_length', 2000)-1 ;
		$commentSize = mb_strlen( JRequest::getString('comment') );
		$validMail = filter_var(JRequest::getVar('email'), FILTER_VALIDATE_EMAIL);

		$virtuemart_vendor_id = JRequest::getInt('virtuemart_vendor_id',1);

		if(!class_exists('VirtueMartModelVendor')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'vendor.php');
		$userId = VirtueMartModelVendor::getUserIdByVendorId($virtuemart_vendor_id);

		//$vendorUser = JFactory::getUser($userId);

		if ( $commentSize<$min || $commentSize>$max || !$validMail ) {
			$this->setRedirect(JRoute::_ ( 'index.php?option=com_virtuemart&view=vendor&task=contact&virtuemart_vendor_id=' . $virtuemart_vendor_id ),JText::_('COM_VIRTUEMART_COMMENT_NOT_VALID_JS'));
			return ;
		}

		$user = JFactory::getUser();

		$fromMail = JRequest::getVar('email');	//is sanitized then
		$fromName = JRequest::getVar('name','');//is sanitized then
		$fromMail = str_replace(array('\'','"',',','%','*','/','\\','?','^','`','{','}','|','~'),array(''),$fromMail);
		$fromName = str_replace(array('\'','"',',','%','*','/','\\','?','^','`','{','}','|','~'),array(''),$fromName);
		if (!empty($user->id)) {
			if(empty($fromMail)){
				$fromMail = $user->email;
			}
			if(empty($fromName)){
				$fromName = $user->name;
			}
		}

		$vars['user'] = array('name' => $fromName, 'email' => $fromMail);

		$VendorEmail = $model->getVendorEmail($virtuemart_vendor_id);
		$vars['vendor'] = array('vendor_store_name' => $fromName );

		if (shopFunctionsF::renderMail('vendor', $VendorEmail, $vars,'vendor')) {
			$string = 'COM_VIRTUEMART_MAIL_SEND_SUCCESSFULLY';
		}
		else {
			$string = 'COM_VIRTUEMART_MAIL_NOT_SEND_SUCCESSFULLY';
		}
		$mainframe->enqueueMessage(JText::_($string));

		// Display it all
		$view = $this->getView('vendor', 'html');

		$view->setLayout('mail_confirmed');
		$view->display();
	}

}

// No closing tag
