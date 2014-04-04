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
		$countryId = JRequest::getInt('virtuemart_country_id', 0);
		$stateId = JRequest::getInt('virtuemart_state_id', 0);
		$this->virtuemart_country_id = $countryId;

		$layoutName = JRequest::getWord('layout');
		if ($layoutName == 'edit') {
			if(empty($countryId) && empty($stateId)){
				JError::raiseWarning(412,'Country id is 0');
				return false;
			}
			$this->state =  $model->getSingleState();
			$this->SetViewTitle('',$this->state->state_name);
			$this->virtuemart_country_id = $this->state->virtuemart_country_id;

			$zoneModel = VmModel::getModel('Worldzones');
			$this->worldZones = $zoneModel->getWorldZonesSelectList();
			$this->addStandardEditViewCommands();
		} else {
			$this->addStandardDefaultViewCommands();
			$this->addStandardDefaultViewLists($model);
			$this->states = $model->getStates($countryId,false,$this->lists['search']);
			$this->pagination = $model->getPagination();

		}
		$country = VmModel::getModel('country');
		$country->setId($this->virtuemart_country_id);
		$this->country_name = $country->getData()->country_name;
		if ($layoutName !== 'edit') $this->SetViewTitle('',$this->country_name);

		parent::display($tpl);
	}

}
// pure php no closing tag
