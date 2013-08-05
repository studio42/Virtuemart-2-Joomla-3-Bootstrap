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
* @version $Id: view.html.php 3006 2011-04-08 13:16:08Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmview.php');

/**
 * HTML View class for the VirtueMart Component
 *
 * @package		VirtueMart
 * @author
 */
class VirtuemartViewCustom extends VmView {

	function display($tpl = null) {

		// Load the helper(s)
		$this->loadHelper('html');
		$this->loadHelper('vmcustomplugin');
		$model = VmModel::getModel();
		$this->loadHelper('permissions');
		$this->customfields = VmModel::getModel('customfields');
		// TODO Make an Icon for custom
		$layoutName = JRequest::getWord('layout', 'default');
		if ($layoutName == 'edit') {

			$this->addStandardEditViewCommands();
			$customPlugin = '';
			// TOTO JPARAMETER $this->loadHelper('parameterparser');
			$custom = $model->getCustom();
			
// 			vmdebug('VirtuemartViewCustom',$custom);
			JPluginHelper::importPlugin('vmcustom');
			$dispatcher = JDispatcher::getInstance();
			$retValue = $dispatcher->trigger('plgVmOnDisplayEdit',array($custom->virtuemart_custom_id,&$customPlugin));

			$this->SetViewTitle('PRODUCT_CUSTOM_FIELD', $custom->custom_title);

			$selected=0;
			if(!empty($custom->custom_jplugin_id)) {
				$selected = $custom->custom_jplugin_id;
			}
			// $this->pluginList = $this->renderInstalledCustomPlugins($selected);
			$this->customPlugin = $customPlugin;
			$this->custom = $custom ;

        }
        else {
			$this->SetViewTitle('PRODUCT_CUSTOM_FIELD');
			JToolBarHelper::custom('createClone', 'copy', 'copy',  JText::_('COM_VIRTUEMART_CLONE'), true);
			JToolBarHelper::custom('toggle.admin_only.1', 'publish','', JText::_('COM_VIRTUEMART_TOGGLE_ADMIN'), true);
			JToolBarHelper::custom('toggle.admin_only.0', 'unpublish','', JText::_('COM_VIRTUEMART_TOGGLE_ADMIN'), true);
			JToolBarHelper::custom('toggle.is_hidden.1', 'publish','', JText::_('COM_VIRTUEMART_TOGGLE_HIDDEN'), true);
			JToolBarHelper::custom('toggle.is_hidden.0', 'unpublish','', JText::_('COM_VIRTUEMART_TOGGLE_HIDDEN'), true);

			$this->addStandardDefaultViewCommands();
			$this->addStandardDefaultViewLists($model, 0, 'DESC', 'keyword');
			$this->customfieldTypes = $this->customfields->getField_types();
			$this->installedCustoms = $this->renderInstalledCustomPlugins(null, true);
			
			$this->customs = $model->getCustoms(JRequest::getInt('custom_parent_id'),JRequest::getWord('keyword'));
			$this->pagination = $model->getPagination();

		}

		parent::display($tpl);
	}

	function renderInstalledCustomPlugins($selected,$resultOnly= null )
	{
		$db = JFactory::getDBO();
		$q = 'SELECT * FROM `#__extensions` WHERE `folder` = "vmcustom" AND `enabled`="1" ';
		$db->setQuery($q);

		$results = $db->loadAssocList('extension_id');
        $this->lang =JFactory::getLanguage();
		foreach ($results as &$result) {
        $filename = 'plg_vmcustom_' .strtolower ( $result['element']).'.sys';
        $this->lang->load($filename, JPATH_ADMINISTRATOR);
		//print_r($lang);
		}
		// set all types for "NEW" custom field modal 
		if ($resultOnly) {
			return $results;
		}
		return VmHTML::select( 'custom_jplugin_id', $results, $selected,"",'extension_id', 'name');

		//return JHtml::_('select.genericlist', $result, 'custom_jplugin_id', null, $ext_id, 'name', $selected);
	}

}
// pure php no closing tag