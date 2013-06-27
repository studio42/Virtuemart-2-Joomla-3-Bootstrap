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
if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmview.php');
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

		$option = JRequest::getCmd( 'option');
		$mainframe = JFactory::getApplication() ;

		// Load the helper(s)


		$this->loadHelper('html');

		$layoutName = JRequest::getWord('layout', 'default');
		$model = VmModel::getModel();

		// The list of fields which can't be toggled
		//$lists['coreFields']= array( 'name','username', 'email', 'password', 'password2' );
		$lists['coreFields'] = $model->getCoreFields();

		if ($layoutName == 'edit') {
			$editor = JFactory::getEditor();

			$userField = $model->getUserfield();
                        $this->SetViewTitle('USERFIELD',$userField->name );
                                $this->assignRef('viewName',$viewName);
			$userFieldPlugin = '';
			if ($userField->virtuemart_userfield_id < 1) { // Insert new userfield


				$this->assignRef('ordering', JText::_('COM_VIRTUEMART_NEW_ITEMS_PLACE'));
				$userFieldValues = array();
				$attribs = '';
				$lists['type'] = JHTML::_('select.genericlist', $this->_getTypes(), 'type', $attribs, 'type', 'text', $userField->type);
			} else { // Update existing userfield
				// Ordering dropdown
				$qry = 'SELECT ordering AS value, name AS text'
					. ' FROM #__virtuemart_userfields'
					. ' ORDER BY ordering';
				$ordering = JHTML::_('list.specificordering',  $userField, $userField->virtuemart_userfield_id, $qry);
				$this->assignRef('ordering', $ordering);

				$userFieldValues = $model->getUserfieldValues();

				$lists['type'] = $this->_getTypes($userField->type)
					. '<input id="type" type="hidden" name="type" value="'.$userField->type.'" />';
				if (strpos($userField->type, 'plugin') !==false) 
					$userFieldPlugin = self::renderUserfieldPlugin(substr($userField->type, 6),$userField);
			}
			$this->assignRef('userFieldPlugin',	$userFieldPlugin);
			JToolBarHelper::divider();
			JToolBarHelper::save();
			JToolBarHelper::apply();
			JToolBarHelper::cancel();

			$notoggle = (in_array($userField->name, $lists['coreFields']) ? 'class="readonly"' : '');

			// Vendor selection
			if(Vmconfig::get('multix','none')!=='none'){
				$lists['vendors']= ShopFunctions::renderVendorList($userField->virtuemart_vendor_id);
			}
// 			$vendor_model = VmModel::getModel('vendor');
// 			$vendor_list = $vendor_model->getVendors();
// 			$lists['vendors'] = JHTML::_('select.genericlist', $vendor_list, 'virtuemart_vendor_id', '', 'virtuemart_vendor_id', 'vendor_name', $userField->virtuemart_vendor_id);

			// Shopper groups for EU VAT Id
			$shoppergroup_model = VmModel::getModel('shoppergroup');
			$shoppergroup_list = $shoppergroup_model->getShopperGroups(true);
			array_unshift($shoppergroup_list,'0');
			$lists['shoppergroups'] = JHTML::_('select.genericlist', $shoppergroup_list, 'virtuemart_shoppergroup_id', '', 'virtuemart_shoppergroup_id', 'shopper_group_name', $model->_params->get('virtuemart_shoppergroup_id'));

			// Minimum age select
			$ages = array();
			for ($i = 13; $i <= 25; $i++) {
				$ages[] = array('key' => $i, 'value' => $i.' '.JText::_('COM_VIRTUEMART_YEAR_S'));
			}
			$lists['minimum_age'] = JHTML::_('select.genericlist', $ages, 'minimum_age', '', 'key', 'value', $model->_params->get('minimum_age', 18));

			// Web address types
			$webaddress_types = array(
				 array('key' => 0, 'value' => JText::_('COM_VIRTUEMART_USERFIELDS_URL_ONLY'))
				,array('key' => 2, 'value' => JText::_('COM_VIRTUEMART_USERFIELDS_HYPERTEXT_URL'))
			);
			$lists['webaddresstypes'] = JHTML::_('select.genericlist', $webaddress_types, 'webaddresstype', '', 'key', 'value', $model->_params->get('webaddresstype'));

			// Userfield values
			if (($n = count($userFieldValues)) < 1) {
				$lists['userfield_values'] =
					 '<tr>'
					.'<td><input type="text" value="" name="vValues[0]" /></td>'
					.'<td><input type="text" size="50" value="" name="vNames[0]" /></td>'
					.'</tr>';
				$i = 1;
			} else {
				$lists['userfield_values'] = '';
				$lang =JFactory::getLanguage();
				for ($i = 0; $i < $n; $i++) {
					$translate= $lang->hasKey($userFieldValues[$i]->fieldtitle) ? " (".JText::_($userFieldValues[$i]->fieldtitle).")" : "";
					$lists['userfield_values'] .=
						 '<tr>'
						 .'<td><input type="text" value="'.$userFieldValues[$i]->fieldvalue.'" name="vValues['.$i.']" /></td>'
						.'<td><input type="text" size="50" value="'.$userFieldValues[$i]->fieldtitle.'" name="vNames['.$i.']"   />'.$translate.'<input type="button" class="button deleteRow" value=" - " /></td>'
						.'</tr>';
				}
			}
			$this->assignRef('valueCount', --$i);

// 			vmdebug('$userField->shipment',$userField);
			// Toggles
			$lists['required']     =  VmHTML::row('booleanlist','COM_VIRTUEMART_FIELDMANAGER_REQUIRED','required',$userField->required,$notoggle);
			$lists['published']    =  VmHTML::row('booleanlist','COM_VIRTUEMART_PUBLISH','published',$userField->published,$notoggle);
			$lists['registration'] =  VmHTML::row('booleanlist','COM_VIRTUEMART_FIELDMANAGER_SHOW_ON_REGISTRATION','registration',$userField->registration,$notoggle);
			$lists['shipment']     =  VmHTML::row('booleanlist','COM_VIRTUEMART_FIELDMANAGER_SHOW_ON_SHIPPING','shipment',$userField->shipment,$notoggle);
			$lists['account']      =  VmHTML::row('booleanlist','COM_VIRTUEMART_FIELDMANAGER_SHOW_ON_ACCOUNT','account',$userField->account,$notoggle);
			$lists['readonly']     =  VmHTML::row('booleanlist','COM_VIRTUEMART_USERFIELDS_READONLY','readonly',$userField->readonly,$notoggle);


			$this->assignRef('lists', $lists);
			$this->assignRef('userField', $userField);
			$this->assignRef('userFieldValues', $userFieldValues);
			$this->assignRef('editor', $editor);
		} else {
			JToolBarHelper::title( JText::_('COM_VIRTUEMART_MANAGE_USER_FIELDS'));
			JToolBarHelper::addNewX();
			JToolBarHelper::editListX();
			JToolBarHelper::divider();
			JToolBarHelper::custom('toggle.required.1', 'publish','','COM_VIRTUEMART_FIELDMANAGER_REQUIRE');
			JToolBarHelper::custom('toggle.required.0', 'unpublish','','COM_VIRTUEMART_FIELDMANAGER_UNREQUIRE');
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
			JToolBarHelper::divider();
			$barText = JText::_('COM_VIRTUEMART_FIELDMANAGER_SHOW_HIDE');

			$bar= JToolBar::getInstance( 'toolbar' );
			$bar->appendButton( 'Separator', '"><span class="bartext">'.$barText.'</span><hr style="display: none;' );
//$bar->appendButton( 'publish', 'upload', $alt, '', 550, 400 );
			JToolBarHelper::custom('toggle.registration.1', 'publish','','COM_VIRTUEMART_FIELDMANAGER_SHOW_REGISTRATION');
			JToolBarHelper::custom('toggle.registration.0', 'unpublish','','COM_VIRTUEMART_FIELDMANAGER_HIDE_REGISTRATION');
			JToolBarHelper::custom('toggle.shipment.1', 'publish','','COM_VIRTUEMART_FIELDMANAGER_SHOW_SHIPPING');
			JToolBarHelper::custom('toggle.shipment.0', 'unpublish','','COM_VIRTUEMART_FIELDMANAGER_HIDE_SHIPPING');
			JToolBarHelper::custom('toggle.account.1', 'publish','','COM_VIRTUEMART_FIELDMANAGER_SHOW_ACCOUNT');
			JToolBarHelper::custom('toggle.account.0', 'unpublish','','COM_VIRTUEMART_FIELDMANAGER_HIDE_ACCOUNT');
			JToolBarHelper::divider();
			JToolBarHelper::deleteList();

			$this->addStandardDefaultViewLists($model,'ordering','ASC');

			$userfieldsList = $model->getUserfieldsList();
			$this->assignRef('userfieldsList', $userfieldsList);

			$pagination = $model->getPagination();
			$this->assignRef('pagination', $pagination);

			// search filter
			$search = $mainframe->getUserStateFromRequest( $option.'search', 'search', '', 'string');
			$search = JString::strtolower( $search );
			$this->lists['search']= $search;
		}
		$this->lists['coreFields'] = $lists['coreFields'];
		parent::display($tpl);
	}

	/**
	 * Additional grid function for custom toggles
	 *
	 * @return string HTML code to write the toggle button
	 */
	function toggle( $field, $i, $toggle, $untoggleable = false, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{

		$img 	= $field ? $imgY : $imgX;
		if ($toggle == 'published') { // Stay compatible with grid.published
			$task 	= $field ? 'unpublish' : 'publish';
			$alt 	= $field ? JText::_('COM_VIRTUEMART_PUBLISHED') : JText::_('COM_VIRTUEMART_UNPUBLISHED');
			$action = $field ? JText::_('COM_VIRTUEMART_UNPUBLISH_ITEM') : JText::_('COM_VIRTUEMART_PUBLISH_ITEM');
		} else {
			$task 	= $field ? $toggle.'.0' : $toggle.'.1';
			$alt 	= $field ? JText::_('COM_VIRTUEMART_ENABLED') : JText::_('COM_VIRTUEMART_DISABLED');
			$action = $field ? JText::_('COM_VIRTUEMART_DISABLE_ITEM') : JText::_('COM_VIRTUEMART_ENABLE_ITEM');
		}

		if (JVM_VERSION>1) {
			$img = 'admin/' . $img;
		}
		  if ($untoggleable) {
			$attribs='style="opacity: 0.6;"';
		} else {
			$attribs='';
		}
		$retImgSrc =  JHTML::_('image.administrator', $img, '/images/', null, null, $alt, $attribs);

		if ($untoggleable) {
			return ($retImgSrc);
		} else {
			return ('<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task .'\')" title="'. $action .'">'
				.$retImgSrc. '</a>');

		}
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

		if (JVM_VERSION===1) {
			$table = '#__plugins';
			$jelement = 'element';
		} else {
			$table = '#__extensions';
			$jelement = 'element';
		}
		$q = 'SELECT `params`,`element` FROM `' . $table . '` WHERE `' . $jelement . '` = "'.$element.'"';
		$db ->setQuery($q);
		$this->plugin = $db ->loadObject();
		
		$this->loadHelper('parameterparser');
		$parameters = new vmParameters($params,  $this->plugin->element , 'plugin' ,'vmuserfield');
		$lang = JFactory::getLanguage();
		$filename = 'plg_vmuserfield_' .  $this->plugin->element;
		$lang->load($filename, JPATH_ADMINISTRATOR);
		return $parameters->render();


	}

	function renderInstalledUserfieldPlugins(&$plugins){

		if ( JVM_VERSION===1) {
			$table = '#__plugins';
			$ext_id = 'id';
			$enable = 'published';
		} else {
			$table = '#__extensions';
			$ext_id = 'extension_id';
			$enable = 'enabled';
		}

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
