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
if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmview.php');

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

		$this->SetViewTitle();


		$model = VmModel::getModel();

//		$stateId = JRequest::getVar('virtuemart_state_id');
//		$model->setId($stateId);
		$state = $model->getSingleState();

		$countryId = JRequest::getInt('virtuemart_country_id', 0);
		if(empty($countryId)) $countryId = $state->virtuemart_country_id;
		$this->assignRef('virtuemart_country_id',	$countryId);

        $isNew = (count($state) < 1);

		if(empty($countryId) && $isNew){
			JError::raiseWarning(412,'Country id is 0');
			return false;
		}

		$country = VmModel::getModel('country');
		$country->setId($countryId);
		$this->assignRef('country_name', $country->getData()->country_name);


		$layoutName = JRequest::getWord('layout', 'default');
		if ($layoutName == 'edit') {


			$this->assignRef('state', $state);

			$zoneModel = VmModel::getModel('Worldzones');
			$wzsList = $zoneModel->getWorldZonesSelectList();
			$this->assignRef('worldZones', $wzsList);

			$this->addStandardEditViewCommands();

		} else {

			$this->addStandardDefaultViewCommands();
			$this->addStandardDefaultViewLists($model);

			$states = $model->getStates($countryId);
			$this->assignRef('states',	$states);

			$pagination = $model->getPagination();
			$this->assignRef('pagination', $pagination);

		}

		parent::display($tpl);
	}

}
// pure php no closing tag
