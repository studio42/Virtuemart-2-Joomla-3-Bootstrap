<?php
/**
*
* Country View
*
* @package	VirtueMart
* @subpackage Country
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 6307 2012-08-07 07:39:45Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');

/**
 * HTML View class for maintaining the list of countries
 *
 * @package	VirtueMart
 * @subpackage Country
 * @author RickG
 */
class VirtuemartViewCountry extends VmView {

    function display($tpl = null) {

		// Load the helper(s)
		if (!class_exists( 'VmConfig' )) require(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'config.php');
		VmConfig::loadConfig();
		if(VmConfig::get('enableEnglish', 1)){
		    $jlang =JFactory::getLanguage();
		    $jlang->load('com_virtuemart_countries', JPATH_ADMINISTRATOR, 'en-GB', true);
		    $jlang->load('com_virtuemart_countries', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		    $jlang->load('com_virtuemart_countries', JPATH_ADMINISTRATOR, null, true);
		}
		$this->loadHelper('html');


		$model = VmModel::getModel('country');
		$zoneModel = VmModel::getModel('worldzones');

		$this->SetViewTitle();


		$layoutName = JRequest::getWord('layout', 'default');
		if ($layoutName == 'edit') {

			$this->country = $model->getData();
			$this->worldZones = $zoneModel->getWorldZonesSelectList();
			$this->addStandardEditViewCommands();

		}
		else {

			$this->addStandardDefaultViewCommands(true,false);

			//First the view lists, it sets the state of the model
			$this->addStandardDefaultViewLists($model,0,'ASC','filter_country');

			$filter_country = JRequest::getWord('filter_country', false);
			$this->countries = $model->getCountries(false, false, $this->lists['filter_country']);

			$this->pagination = $model->getPagination();

		}

		parent::display($tpl);
    }

}
// pure php no closing tag
