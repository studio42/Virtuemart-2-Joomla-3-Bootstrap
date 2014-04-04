<?php
/**
*
* Inventory controller
*
* @package	VirtueMart
* @subpackage
* @author RolandD
* @ Re author Patrick Kohl
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: inventory.php 5399 2012-02-08 19:29:45Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');


/**
 * Inventory Controller
 *
 * @package    VirtueMart
 * @author RolandD
 */
class VirtuemartControllerInventory extends VmController {

	//task:updatestock
	public function updatestock(){
		JSession::checkToken() or jexit( 'Invalid Token save' );
		$data = array();
		if ( $data['virtuemart_product_id'] = (int)JRequest::getVar('virtuemart_product_id', 0) ){
			$data['product_in_stock'] = (int)JRequest::getVar('product_in_stock', 0);
			$model = VmModel::getModel($this->_cname);
			$model->updateStock($data);
			$errors = $model->getErrors();
			if (empty($errors)) {
				$msg = JText::_('COM_VIRTUEMART_PRODUCT_IN_STOCK').' '. $data['product_in_stock'];
			}
			foreach($errors as $error){
				$msg = ($error).'<br />';
			}
		} else $msg = 'no product ID !';

		$this->setRedirect(null,$msg);
	}
}
// pure php no closing tag
