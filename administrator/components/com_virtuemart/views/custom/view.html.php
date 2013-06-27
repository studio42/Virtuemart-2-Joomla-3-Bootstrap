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
		// TODO Make an Icon for custom
		$this->SetViewTitle('PRODUCT_CUSTOM_FIELD');



		$layoutName = JRequest::getWord('layout', 'default');
		if ($layoutName == 'edit') {
			$this->addStandardEditViewCommands();
			$customPlugin = '';
			$this->loadHelper('parameterparser');
			$custom = $model->getCustom();
			$customfields = VmModel::getModel('customfields');
// 			vmdebug('VirtuemartViewCustom',$custom);
			JPluginHelper::importPlugin('vmcustom');
			$dispatcher = JDispatcher::getInstance();
			$retValue = $dispatcher->trigger('plgVmOnDisplayEdit',array($custom->virtuemart_custom_id,&$customPlugin));

			$this->SetViewTitle('PRODUCT_CUSTOM_FIELD', $custom->custom_title);

			$selected=0;
			if(!empty($custom->custom_jplugin_id)) {
				$selected = $custom->custom_jplugin_id;
			}
			$pluginList = self::renderInstalledCustomPlugins($selected);
			$this->assignRef('customPlugin',	$customPlugin);

			$this->assignRef('pluginList',$pluginList);
			$this->assignRef('custom',	$custom);
			$this->assignRef('customfields',	$customfields);

        }
        else {

			JToolBarHelper::custom('createClone', 'copy', 'copy',  JText::_('COM_VIRTUEMART_CLONE'), true);
			JToolBarHelper::custom('toggle.admin_only.1', 'publish','', JText::_('COM_VIRTUEMART_TOGGLE_ADMIN'), true);
			JToolBarHelper::custom('toggle.admin_only.0', 'unpublish','', JText::_('COM_VIRTUEMART_TOGGLE_ADMIN'), true);
			JToolBarHelper::custom('toggle.is_hidden.1', 'publish','', JText::_('COM_VIRTUEMART_TOGGLE_HIDDEN'), true);
			JToolBarHelper::custom('toggle.is_hidden.0', 'unpublish','', JText::_('COM_VIRTUEMART_TOGGLE_HIDDEN'), true);

			$this->addStandardDefaultViewCommands();
			$this->addStandardDefaultViewLists($model);

			$customs = $model->getCustoms(JRequest::getInt('custom_parent_id'),JRequest::getWord('keyword'));
			$this->assignRef('customs',	$customs);

			$pagination = $model->getPagination();
			$this->assignRef('pagination', $pagination);


		}

		parent::display($tpl);
	}

	function renderInstalledCustomPlugins($selected)
	{
		$db = JFactory::getDBO();

		if (JVM_VERSION===1) {
			$table = '#__plugins';
			$enable = 'published';
			$ext_id = 'id';
		}
		else {
			$table = '#__extensions';
			$enable = 'enabled';
			$ext_id = 'extension_id';
		}
		$q = 'SELECT * FROM `'.$table.'` WHERE `folder` = "vmcustom" AND `'.$enable.'`="1" ';
		$db->setQuery($q);

		$results = $db->loadAssocList($ext_id);
        $lang =JFactory::getLanguage();
		foreach ($results as &$result) {
        $filename = 'plg_' .strtolower ( $result['name']).'.sys';

        $lang->load($filename, JPATH_ADMINISTRATOR);
		//print_r($lang);
		}
		return VmHTML::select( 'custom_jplugin_id', $results, $selected,"",$ext_id, 'name');

		//return JHtml::_('select.genericlist', $result, 'custom_jplugin_id', null, $ext_id, 'name', $selected);
	}

}
// pure php no closing tag