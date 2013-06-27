<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author  Patrick Kohl
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
jimport( 'joomla.application.component.view');

/**
 * Json View class for the VirtueMart Component
 *
 * @package		VirtueMart
 * @author  Patrick Kohl
 */
class VirtuemartViewUserfields extends JView {

	function display($tpl = null) {
		$db = JFactory::getDBO();
		if ( $field = JRequest::getVar('field') ) {
			if (strpos($field, 'plugin') !==false) {
				if (JVM_VERSION===1) {
					$table = '#__plugins';
					//$ext_id = 'id';
				} else {
					$table = '#__extensions';
					//$ext_id = 'extension_id';
				}
				$field = substr($field, 6);
				$q = 'SELECT `params`,`element` FROM `' . $table . '` WHERE `element` = "'.$field.'"';
				$db ->setQuery($q);
				$this->plugin = $db ->loadObject();
				$this->loadHelper('parameterparser');
				$parameters = new vmParameters($this->plugin ,  $this->plugin->element , 'plugin' ,'vmuserfield');
				$lang = JFactory::getLanguage();
				$filename = 'plg_vmuserfield_' .  $this->plugin->element;
				if(VmConfig::get('enableEnglish', 1)){
		            $lang->load($filename, JPATH_ADMINISTRATOR, 'en-GB', true);
				}
				$lang->load($filename, JPATH_ADMINISTRATOR, $lang->getDefault(), true);
				$lang->load($filename, JPATH_ADMINISTRATOR, null, true);

				echo $parameters->render();
				//echo '<input type="hidden" value="'.$this->plugin->element.'" name="custom_value">';
				jExit();
			}
		}
		jExit();
	}

}
// pure php no closing tag
