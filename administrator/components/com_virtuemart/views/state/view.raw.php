<?php
/**
*
* State View
*
* @package	VirtueMart
* @subpackage State
* @author RickG, Max Milbers
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
 * HTML View class for maintaining the list of states
 *
 * @package	VirtueMart
 * @subpackage State
 * @author Max Milbers
 */
class VirtuemartViewState extends VmView {

	function display($tpl = null) {

		// Load the helper(s)
		$this->loadHelper('html');
		$model = VmModel::getModel();

		// init some values
		$this->virtuemart_country_id = JRequest::getInt('virtuemart_country_id', 0);
		$this->addStandardDefaultViewCommands();
		$this->addStandardDefaultViewLists($model);
		
		$this->states = $model->getStates(this->virtuemart_country_id,false,this->lists['search']);
		$this->pagination = $model->getPagination();

		parent::display('results');
		echo $this->AjaxScripts();
	}

}
// pure php no closing tag
