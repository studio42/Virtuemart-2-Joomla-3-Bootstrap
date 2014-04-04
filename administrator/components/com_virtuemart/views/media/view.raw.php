<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 6068 2012-06-06 14:59:42Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');

/**
 * HTML View class for the VirtueMart Component
 *
 * @package		VirtueMart
 * @author
 */
class VirtuemartViewMedia extends VmView {

	function display($tpl = null) {

		$this->loadHelper('html');
		$this->loadHelper('permissions');
		//@todo should be depended by loggedVendor
		$this->vendorId=1;
		$titleMsg ='';
		$model = VmModel::getModel();
		$this->perms = Permissions::getInstance();
		
		if (!$this->product_id = JRequest::getInt('virtuemart_product_id',0)) {
			$this->cat_id = JRequest::getInt('virtuemart_category_id',0); 
		} else $this->cat_id = 0 ;

		$this->addStandardDefaultViewLists($model,null,null,'searchMedia');
		$this->files = $model->getFiles(false,false,$this->product_id,$this->cat_id);
		$this->pagination = $model->getPagination();

		parent::display('results');
		$script = "jQuery('.fb-modal-toggle,.modalbox').fancybox()";
		echo $this->AjaxScripts($script );
	}

}
// pure php no closing tag