<?php
/**
 *
 * UpdatesMigration View
 *
 * @package	VirtueMart
 * @subpackage UpdatesMigration
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 6043 2012-05-21 21:40:56Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmview.php');

/**
 * HTML View class for maintaining the Installation. Updating of the files and imports of the database should be done here
 *
 * @package	VirtueMart
 * @subpackage UpdatesMigration
 * @author Max Milbers
 */
class VirtuemartViewUpdatesMigration extends VmView {

	function display($tpl = null) {


		$latestVersion = JRequest::getVar('latestverison', '');

		JToolBarHelper::title(JTEXT::_('COM_VIRTUEMART_UPDATE_MIGRATION'), 'head vm_config_48');

// 		$this->loadHelper('connection');
		$this->loadHelper('image');
		$this->loadHelper('html');

		$this->assignRef('checkbutton_style', $checkbutton_style);
		$this->assignRef('downloadbutton_style', $downloadbutton_style);
		$this->assignRef('latestVersion', $latestVersion);

		$analyse =$this->analyseTables();
		$this->assignRef('analyse', $analyse);

		parent::display($tpl);
	}

	function analyseTables() {
/*		$db = JFactory::getDBO();
		$config = JFactory::getConfig();

		$prefix = $config->getValue('config.dbprefix').'virtuemart_%';
		$db->setQuery('SHOW TABLES LIKE "'.$prefix.'"');
		if (!$tables = $db->loadResultArray()) {
			vmError ($db->getErrorMsg());
			return false;
		}
		$html ='<pre>';
		$app = JFactory::getApplication();
// 		foreach ($tables as $table) {

// 			$db->setQuery('SELECT * FROM '.$table.' PROCEDURE ANALYSE(); ');
			$db->setQuery('SELECT * FROM #__virtuemart_countries PROCEDURE ANALYSE(); ');

			if($db->query()){
				vmdebug('Analyse',$db->loadObjectList());
			} else {
				$app->enqueueMessage('Error drop virtuemart table ' . $db->getErrorMsg());
			}
// 		}
		return $html.'</pre>';*/
	}
}
// pure php no closing tag
