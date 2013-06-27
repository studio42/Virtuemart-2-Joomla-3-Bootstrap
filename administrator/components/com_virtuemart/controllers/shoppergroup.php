<?php
/**
*
* Shopper Group controller
*
* @package	VirtueMart
* @subpackage ShopperGroup
* @author Markus �hler
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: shoppergroup.php 5419 2012-02-10 19:27:53Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');


/**
 * Shopper Group Controller
 *
 * @package    VirtueMart
 * @subpackage ShopperGroup
 * @author Markus �hler
 */
class VirtuemartControllerShopperGroup extends VmController
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function __construct() {
		parent::__construct('virtuemart_shoppergroup_id');
		$this->registerTask( 'default','makeDefault' );
	}

	function makeDefault() {
		$mainframe = Jfactory::getApplication();

		/* Load the view object */
		$view = $this->getView('shoppergroup', 'html');

		$model = VmModel::getModel('shoppergroup');
		$msgtype = '';
		$cids = JRequest::getVar('virtuemart_shoppergroup_id',array());
		if ($model->makeDefault((int)$cids[0])) $msg = JText::_('COM_VIRTUEMART_SET_TO_DEFAULT_SUCCESSFULLY');
		else {
			$msg = '';
// 			$msg = JText::_('COM_VIRTUEMART_SET_TO_DEFAULT_ERROR');
			$msgtype = 'error';
		}
		$mainframe->redirect('index.php?option=com_virtuemart&view=shoppergroup', $msg, $msgtype);
	}
}
// pure php no closing tag
