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
class VirtuemartViewCustom extends JViewLegacy {

	/* json object */
	private $json = null;

	function display($tpl = null) {
			$db = JFactory::getDBO();
		if ( $virtuemart_media_id = JRequest::getInt('virtuemart_media_id') ) {
			//$db = JFactory::getDBO();
			$query='SELECT `file_url`,`file_title` FROM `#__virtuemart_medias` where `virtuemart_media_id`='.$virtuemart_media_id;
			$db->setQuery( $query );
			$json = $db->loadObject();
			if (isset($json->file_url)) {
				$json->file_url = JURI::root().$json->file_url;
				$json->msg =  'OK';
				echo json_encode($json);
			} else {
				$json->msg =  '<b>'.JText::_('COM_VIRTUEMART_NO_IMAGE_SET').'</b>';
				echo json_encode($json);
			}
		}
		elseif ( $custom_jplugin_id = JRequest::getInt('custom_jplugin_id') ) {
			$q = 'SELECT `params`,`element` FROM `#__extensions` WHERE `extension_id` = "'.$custom_jplugin_id.'"';
			$db ->setQuery($q);
			$this->plugin = $db ->loadObject();
			JPluginHelper::importPlugin('vmcustom');
			$dispatcher = JDispatcher::getInstance();
			//    			$varsToPushParam = $dispatcher->trigger('plgVmDeclarePluginParams',array('custom',$this->plugin->custom_element,$this->plugin->custom_jplugin_id));
			$retValue = $dispatcher->trigger('plgVmDeclarePluginParamsCustom',array('custom',$this->plugin->element,$custom_jplugin_id ,&$this->plugin));

			// TOTO JPARAMETER $this->loadHelper('parameterparser');
			$path	= JPath::clean( JPATH_PLUGINS.'/vmcustom/' . $this->plugin->element . '/' . $this->plugin->element . '.xml');
			if (file_exists($path))
			{

				$this->custom->form = JForm::getInstance('plgForm', $path, array(),true, '//config');
				// $this->plugin->xml = simplexml_load_file($path);
				$this->custom->form->bind($this->plugin);
			}
			// $parameters = new vmParameters($this->plugin->params,  $this->plugin->element , 'plugin' ,'vmcustom');
			$lang = JFactory::getLanguage();
			$filename = 'plg_vmcustom_' .  $this->plugin->element;
			$lang->load($filename, JPATH_ADMINISTRATOR);
			$this->setLayout('edit');
			echo $this->loadTemplate ( 'options');
			// echo $parameters->render();
			echo '<input type="hidden" value="'.$this->plugin->element.'" name="custom_value">';
		}
		// jExit();
	}

}
// pure php no closing tag
