<?php
/**
*
* Manufacturer Category View
*
* @package	VirtueMart
* @subpackage Manufacturer Category
* @author Patrick Kohl
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 6006 2012-05-07 09:28:42Z electrocity $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');

/**
 * HTML View class for maintaining the list of manufacturer categories
 *
 * @package	VirtueMart
 * @subpackage Manufacturer Categories
 * @author Patrick Kohl
 */
class VirtuemartViewManufacturercategories extends VmView {

	function display($tpl = null) {

		// Load the helper(s)
		$this->loadHelper('html');

		// get necessary model
		$model = VmModel::getModel();
       	$this->addStandardDefaultViewCommands();
       	$this->addStandardDefaultViewLists($model);

		$this->manufacturerCategories = $model->getManufacturerCategories();
		$this->pagination = $model->getPagination();
		parent::display('results');
		echo $this->AjaxScripts();
	}

}
// pure php no closing tag
