<?php
/**
*
* Category controller
*
* @package	VirtueMart
* @subpackage Category
* @author RickG, jseros
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: category.php 6071 2012-06-06 15:33:04Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');

/**
 * Category Controller
 *
 * @package    VirtueMart
 * @subpackage Category
 * @author jseros, Max Milbers
 */
class VirtuemartControllerCategory extends VmController {

	/**
	 * We want to allow html so we need to overwrite some request data
	 *
	 * @author Max Milbers
	 */
	function save($data = 0){

		$data = JRequest::get('post');

		$data['category_name'] = JRequest::getVar('category_name','','post','STRING',JREQUEST_ALLOWHTML);
		$data['category_description'] = JRequest::getVar('category_description','','post','STRING',JREQUEST_ALLOWHTML);
		$this->cleanCache('com_virtuemart');
		parent::save($data);
	}


	/**
	* Save the category order
	*
	* @author jseros
	*/
	public function orderUp()
	{
		// Check token
		JSession::checkToken() or jexit( 'Invalid Token' );

		//capturing virtuemart_category_id
		$id = 0;
		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		} else {
			$this->setRedirect( null, JText::_('COM_VIRTUEMART_NO_ITEMS_SELECTED') );
			return false;
		}

		//getting the model
		$model = VmModel::getModel('category');

		if ($model->orderCategory($id, -1)) {
			$msg = JText::_('COM_VIRTUEMART_ITEM_MOVED_UP');
		} else {
			$msg = $model->getError();
		}

		$this->setRedirect( null, $msg );
	}


	/**
	* Save the category order
	*
	* @author jseros
	*/
	public function orderDown()
	{
		// Check token
		JSession::checkToken() or jexit( 'Invalid Token' );

		//capturing virtuemart_category_id
		$id = 0;
		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (isset($cid[0]) && $cid[0]) {
			$id = $cid[0];
		} else {
			$this->setRedirect( null, JText::_('COM_VIRTUEMART_NO_ITEMS_SELECTED') );
			return false;
		}

		//getting the model
		$model = VmModel::getModel('category');

		if ($model->orderCategory($id, 1)) {
			$msg = JText::_('COM_VIRTUEMART_ITEM_MOVED_DOWN');
		} else {
			$msg = $model->getError();
		}

		$this->setRedirect( null, $msg );
	}


	/**
	* Save the categories order
	*/
	public function saveorder()
	{
		if ($ordered = parent::saveorder()) {
			$this->cleanCache('_virtuemart');
		}
	}
	/**
	 * Handle the toggle task
	 *
	 * @author Max Milbers , Patrick Kohl
	 */

	public function toggle($field,$val=null){
		$this->cleanCache('_virtuemart');
		parent::toggle($field,$val);
		
	}

}
