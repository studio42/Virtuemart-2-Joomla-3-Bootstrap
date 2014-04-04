<?php
/**
*
* List/add/edit/remove Userfields
*
* @package	VirtueMart
* @subpackage Userfields
* @author Oscar van Eijk
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 6386 2012-08-29 11:29:26Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');
jimport('joomla.version');

/**
 * HTML View class for maintaining the list of order types
 *
 * @package	VirtueMart
 * @subpackage Userfields
 * @author Oscar van Eijk
 */
class VirtuemartViewUserfields extends VmView {

	function display($tpl = null) {

		VmConfig::loadJLang('com_virtuemart_shoppers',TRUE);
		// Load the helper(s)
		$this->loadHelper('html');

		$layoutName = JRequest::getWord('layout');
		$model = VmModel::getModel();

		// The list of fields which can't be toggled
		//$lists['coreFields']= array( 'name','username', 'email', 'password', 'password2' );
		$lists['coreFields'] = $model->getCoreFields();

		$this->addStandardDefaultViewLists($model,'ordering','ASC');
		$this->userfieldsList = $model->getUserfieldsList();
		$this->pagination = $model->getPagination();
		$this->lists['coreFields'] = $lists['coreFields'];
		parent::display('results');
		echo $this->AjaxScripts();
	}

	/**
	 * Create an array with userfield types and the visible text in the format expected by the Joomla select class
	 *
	 * @param string $value If not null, the type of which the text should be returned
	 * @return mixed array or string
	 */
	function _getTypes ($value = null)
	{
		$types = array(
			 array('type' => 'text'             , 'text' => JText::_('COM_VIRTUEMART_FIELDS_TEXTFIELD'))
			,array('type' => 'checkbox'         , 'text' => JText::_('COM_VIRTUEMART_FIELDS_CHECKBOX_SINGLE'))
			,array('type' => 'multicheckbox'    , 'text' => JText::_('COM_VIRTUEMART_FIELDS_CHECKBOX_MULTIPLE'))
			,array('type' => 'date'             , 'text' => JText::_('COM_VIRTUEMART_FIELDS_DATE'))
			,array('type' => 'age_verification' , 'text' => JText::_('COM_VIRTUEMART_FIELDS_AGEVERIFICATION'))
			,array('type' => 'select'           , 'text' => JText::_('COM_VIRTUEMART_FIELDS_DROPDOWN_SINGLE'))
			,array('type' => 'multiselect'      , 'text' => JText::_('COM_VIRTUEMART_FIELDS_DROPDOWN_MULTIPLE'))
			,array('type' => 'emailaddress'     , 'text' => JText::_('COM_VIRTUEMART_FIELDS_EMAIL'))
// 			,array('type' => 'euvatid'          , 'text' => JText::_('COM_VIRTUEMART_FIELDS_EUVATID'))
			,array('type' => 'editorta'         , 'text' => JText::_('COM_VIRTUEMART_FIELDS_EDITORAREA'))
			,array('type' => 'textarea'         , 'text' => JText::_('COM_VIRTUEMART_FIELDS_TEXTAREA'))
			,array('type' => 'radio'            , 'text' => JText::_('COM_VIRTUEMART_FIELDS_RADIOBUTTON'))
			,array('type' => 'webaddress'       , 'text' => JText::_('COM_VIRTUEMART_FIELDS_WEBADDRESS'))
			,array('type' => 'delimiter'        , 'text' => JText::_('COM_VIRTUEMART_FIELDS_DELIMITER'))

		);
		$this->renderInstalledUserfieldPlugins($types);


// 		vmdebug('my $dispatcher ',$dispatcher);
// 		if($data['userverifyfailed']==1){
// 			return false;
// 		}

		//This should be done via plugins !
/*		if (file_exists(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_securityimages'.DS.'client.php')) {
			$types[] = array('type' => 'captcha', 'text' => JText::_('COM_VIRTUEMART_FIELDS_CAPTCHA'));
		}
		if (file_exists(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_securityimages'.DS.'class'.DS.'SecurityImagesHelper.php')) {
			$types[] = array('type' => 'captcha', 'text' => JText::_('COM_VIRTUEMART_FIELDS_CAPTCHA'));
		}
		if (file_exists(JPATH_ROOT.DS.'components'.DS.'com_yanc'.DS.'yanc.php')) {
			$types[] = array('type' => 'yanc_subscription', 'text' => JText::_('COM_VIRTUEMART_FIELDS_NEWSLETTER').' (YaNC)');
		}
		if (file_exists(JPATH_ROOT.DS.'components'.DS.'com_anjel'.DS.'anjel.php')) {
			$types[] = array('type' => 'anjel_subscription', 'text' => JText::_('COM_VIRTUEMART_FIELDS_NEWSLETTER').' (ANJEL)');
		}
		if (file_exists(JPATH_ROOT.DS.'components'.DS.'com_letterman'.DS.'letterman.php')) {
			$types[] = array('type' => 'letterman_subscription', 'text' => JText::_('COM_VIRTUEMART_FIELDS_NEWSLETTER').' (Letterman)');
		}
		if (file_exists(JPATH_ROOT.DS.'components'.DS.'com_ccnewsletter'.DS.'ccnewsletter.php')) {
			$types[] = array('type' => 'ccnewsletter_subscription', 'text' => JText::_('COM_VIRTUEMART_FIELDS_NEWSLETTER').' (ccNewsletter)');
		}
		$types[] = array('type' => 'delimiter', 'text' => JText::_('COM_VIRTUEMART_FIELDS_DELIMITER'));
*/
		if ($value === null) {
			return $types;
		} else {
			foreach ($types as $type) {
				if ($type['type'] == $value) {
					return $type['text'];
				}
				return $value;
			}
		}
	}
	function renderUserfieldPlugin($element, $params)
	{
		$db = JFactory::getDBO();

		$table = '#__extensions';
		$jelement = 'element';
		$q = 'SELECT `params`,`element` FROM `' . $table . '` WHERE `' . $jelement . '` = "'.$element.'"';
		$db ->setQuery($q);
		$this->plugin = $db ->loadObject();
		
		// TOTO JPARAMETER $this->loadHelper('parameterparser');
		$parameters = new vmParameters($params,  $this->plugin->element , 'plugin' ,'vmuserfield');
		$lang = JFactory::getLanguage();
		$filename = 'plg_vmuserfield_' .  $this->plugin->element;
		$lang->load($filename, JPATH_ADMINISTRATOR);
		return $parameters->render();


	}

	function renderInstalledUserfieldPlugins(&$plugins){

		$table = '#__extensions';
		$ext_id = 'extension_id';
		$enable = 'enabled';

		$db = JFactory::getDBO();
 		$q = 'SELECT * FROM `'.$table.'` WHERE `folder` = "vmuserfield" AND `'.$enable.'`="1" ';
		$db->setQuery($q);
		$userfieldplugins = $db->loadAssocList($ext_id);
		if(empty($userfieldplugins)){
			return;
		}

		foreach($userfieldplugins as $userfieldplugin){
		  // $plugins[] = array('type' => $userfieldplugin[$ext_id], 'text' => $userfieldplugin['name']);
            $plugins[] = array('type' => 'plugin'.$userfieldplugin['element'], 'text' => $userfieldplugin['name']);
		}

		return;
	}
}

//No Closing Tag
