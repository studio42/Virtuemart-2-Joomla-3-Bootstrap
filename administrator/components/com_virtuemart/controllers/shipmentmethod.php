<?php
/**
*
* Shipment  controller
*
* @package	VirtueMart
* @subpackage Shipment
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: shipmentmethod.php 6326 2012-08-08 14:14:28Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');


/**
 * Shipment  Controller
 *
 * @package    VirtueMart
 * @subpackage Shipment
 * @author RickG, Max Milbers
 */
class VirtuemartControllerShipmentmethod extends VmController {

	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * We want to allow html in the descriptions.
	 *
	 * @author Max Milbers
	 */
	function save($data = 0){
		$data = JRequest::get('post');
		// TODO disallow shipment_name as HTML
		$data['shipment_name'] = JRequest::getVar('shipment_name','','post','STRING',JREQUEST_ALLOWHTML);
		$data['shipment_desc'] = JRequest::getVar('shipment_desc','','post','STRING',JREQUEST_ALLOWHTML);

		parent::save($data);

	}
	/**
	 * Clone a shipment
	 *
	 * @author Valérie Isaksen
	 */
	public function CloneShipment() {
		$mainframe = Jfactory::getApplication();

		/* Load the view object */
		$view = $this->getView('shipmentmethod', 'html');

		$model = VmModel::getModel('shipmentmethod');
		$msgtype = '';
		//$cids = JRequest::getInt('virtuemart_product_id',0);
		$cids = JRequest::getVar($this->_cidName, JRequest::getVar('virtuemart_shipment_id',array(),'', 'ARRAY'), '', 'ARRAY');
		//jimport( 'joomla.utilities.arrayhelper' );
		JArrayHelper::toInteger($cids);

		foreach($cids as $cid){
			if ($model->createClone($cid)) $msg = JText::_('COM_VIRTUEMART_SHIPMENT_CLONED_SUCCESSFULLY');
			else {
				$msg = JText::_('COM_VIRTUEMART_SHIPMENT_NOT_CLONED_SUCCESSFULLY');
				$msgtype = 'error';
			}
		}

		$mainframe->redirect('index.php?option=com_virtuemart&view=shipmentmethod', $msg, $msgtype);
	}
}
// pure php no closing tag
