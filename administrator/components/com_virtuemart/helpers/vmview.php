<?php
/**
 * abstract controller class containing get,store,delete,publish and pagination
 *
 *
 * This class provides the functions for the calculations
 *
 * @package	VirtueMart
 * @subpackage Helpers
 * @author Max Milbers
 * @copyright Copyright (c) 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
// Load the view framework
jimport( 'joomla.application.component.view');
// Load default helpers
if (!class_exists('ShopFunctions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'shopfunctions.php');
if (!class_exists('AdminUIHelper')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'adminui.php');
if (!class_exists('JToolBarHelper')) require(JPATH_ADMINISTRATOR.DS.'includes'.DS.'toolbar.php');
class VmView extends JView{

	/**
	 * Sets automatically the shortcut for the language and the redirect path
	 *
	 * @author Max Milbers
	 */
	// public function __construct() {
		// parent::construct();
	// }
	var $lists = array();


	/*
	 * set all commands and options for BE default.php views
	* return $list filter_order and
	*/
	function addStandardDefaultViewCommands($showNew=true, $showDelete=true) {

		JToolBarHelper::divider();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::editListX();
		if ($showNew) {
			JToolBarHelper::addNewX();
		}
		if ($showDelete) {
			JToolBarHelper::deleteList();
		}
	}

	/*
	 * set pagination and filters
	* return Array() $list( filter_order and dir )
	*/

	function addStandardDefaultViewLists($model, $default_order = 0, $default_dir = 'DESC',$name = 'search') {

		//This function must be used after the listing
// 		$pagination = $model->getPagination();
// 		$this->assignRef('pagination', $pagination);

		/* set list filters */
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view', JRequest::getCmd('controller','virtuemart'));

		$app = JFactory::getApplication();
		$lists[$name] = $app->getUserStateFromRequest($option . '.' . $view . '.'.$name, $name, '', 'string');

		$lists['filter_order'] = $this->getValidFilterOrder($app,$model,$view,$default_order);

// 		if($default_dir===0){
			$toTest = $app->getUserStateFromRequest( 'com_virtuemart.'.$view.'.filter_order_Dir', 'filter_order_Dir', $default_dir, 'cmd' );

		$lists['filter_order_Dir'] = $model->checkFilterDir($toTest);

		$this->assignRef('lists', $lists);

	}

	function getValidFilterOrder($app,$model,$view,$default_order){

		if($default_order===0){
			$default_order = $model->getDefaultOrdering();
		}

		$toTest = $app->getUserStateFromRequest( 'com_virtuemart.'.$view.'.filter_order', 'filter_order', $default_order, 'cmd' );

// 		vmdebug('getValidFilterOrder '.$toTest.' '.$default_order, $model->_validOrderingFieldName);
		return $model->checkFilterOrder($toTest);
	}


	/*
	 * Add simple search to form
	* @param $searchLabel text to display before searchbox
	* @param $name 		 lists and id name
	* ??JText::_('COM_VIRTUEMART_NAME')
	*/

	function displayDefaultViewSearch($searchLabel='COM_VIRTUEMART_NAME',$name ='search') {
		return JText::_('COM_VIRTUEMART_FILTER') . ' ' . JText::_($searchLabel) . ':
		<input type="text" name="' . $name . '" id="' . $name . '" value="' .$this->lists[$name] . '" class="text_area" />
		<button onclick="this.form.submit();">' . JText::_('COM_VIRTUEMART_GO') . '</button>
		<button onclick="document.getElementById(\'' . $name . '\').value=\'\';this.form.submit();">' . JText::_('COM_VIRTUEMART_RESET') . '</button>';
	}

	function addStandardEditViewCommands($id = 0,$object = null) {
		if (JRequest::getCmd('tmpl') =='component' ) {
			if (!class_exists('JToolBarHelper')) require(JPATH_ADMINISTRATOR.DS.'includes'.DS.'toolbar.php');
		} else {
// 		JRequest::setVar('hidemainmenu', true);
		JToolBarHelper::divider();
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		}
		// javascript for cookies setting in case of press "APPLY"
		$document = JFactory::getDocument();

		if (JVM_VERSION===1) {
			$j = "
//<![CDATA[
	function submitbutton(pressbutton) {

		jQuery( '#media-dialog' ).remove();
		var options = { path: '/', expires: 2}
		if (pressbutton == 'apply') {
			var idx = jQuery('#tabs li.current').index();
			jQuery.cookie('vmapply', idx, options);
		} else {
			jQuery.cookie('vmapply', '0', options);
		}
		 submitform(pressbutton);
	};
//]]>
	" ;
		}
		else $j = "
//<![CDATA[
	Joomla.submitbutton=function(a){
		var options = { path: '/', expires: 2}
		if (a == 'apply') {
			var idx = jQuery('#tabs li.current').index();
			jQuery.cookie('vmapply', idx, options);
		} else {
			jQuery.cookie('vmapply', '0', options);
		}
		jQuery( '#media-dialog' ).remove();
		Joomla.submitform(a);
	};
//]]>
	" ;
		$document->addScriptDeclaration ( $j);

		// LANGUAGE setting

		$editView = JRequest::getWord('view',JRequest::getWord('controller','' ) );

		$params = JComponentHelper::getParams('com_languages');
		//$config =JFactory::getConfig();$config->getValue('language');
		$selectedLangue = $params->get('site', 'en-GB');

		$lang = strtolower(strtr($selectedLangue,'-','_'));
		// only add if ID and view not null
		if ($editView and $id and (count(vmconfig::get('active_languages'))>1) ) {

			if ($editView =='user') $editView ='vendor';
			//$params = JComponentHelper::getParams('com_languages');
			jimport('joomla.language.helper');
			$lang = JRequest::getVar('vmlang', $lang);
			$languages = JLanguageHelper::createLanguageList($selectedLangue, constant('JPATH_SITE'), true);
			$activeVmLangs = (vmconfig::get('active_languages') );

			foreach ($languages as $k => &$joomlaLang) {
				if (!in_array($joomlaLang['value'], $activeVmLangs) )  unset($languages[$k] );
			}
			$langList = JHTML::_('select.genericlist',  $languages, 'vmlang', 'class="inputbox"', 'value', 'text', $selectedLangue , 'vmlang');
			$this->assignRef('langList',$langList);
			$this->assignRef('lang',$lang);



			$token = JUtility::getToken();
			$j = '
			jQuery(function($) {
				var oldflag = "";
				$("select#vmlang").chosen().change(function() {
					langCode = $(this).find("option:selected").val();
					flagClass = "flag-"+langCode.substr(0,2) ;
					$.getJSON( "index.php?option=com_virtuemart&view=translate&task=paste&format=json&lg="+langCode+"&id='.$id.'&editView='.$editView.'&'.$token.'=1" ,
						function(data) {
							var items = [];

							if (data.fields !== "error" ) {
								if (data.structure == "empty") alert(data.msg);
								$.each(data.fields , function(key, val) {
									cible = jQuery("#"+key);
									if (oldflag !== "") cible.parent().removeClass(oldflag)
									if (cible.parent().addClass(flagClass).children().hasClass("mce_editable") && data.structure !== "empty" ) tinyMCE.execInstanceCommand(key,"mceSetContent",false,val);
									else if (data.structure !== "empty") cible.val(val);
									});
								oldflag = flagClass ;
							} else alert(data.msg);
						}
					)
				});
			})';
			$document->addScriptDeclaration ( $j);
		} else {
			// $params = JComponentHelper::getParams('com_languages');
			// $lang = $params->get('site', 'en-GB');
			$jlang = JFactory::getLanguage();
			$langs = $jlang->getKnownLanguages();
			$defautName = $langs[$selectedLangue]['name'];
			$flagImg =JURI::root( true ).'/administrator/components/com_virtuemart/assets/images/flag/'.substr($lang,0,2).'.png';
			$langList = '<input name ="vmlang" type="hidden" value="'.$selectedLangue.'" ><img style="vertical-align: middle;" alt="'.$defautName.'" src="'.$flagImg.'"> <b> '.$defautName.'</b>';
			$this->assignRef('langList',$langList);
			$this->assignRef('lang',$lang);
		}

		//I absolutly do not understand for that should be for, note by Max
/*		if ($object) {
		   if(Vmconfig::get('multix','none')!=='none'){
				$this->loadHelper('permissions');
				if(!Permissions::getInstance()->check('admin')) {
					if (!$object->virtuemart_vendor_id) {
						if(!class_exists('VirtueMartModelVendor')) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'vendor.php');
						$object->virtuemart_vendor_id = VirtueMartModelVendor::getLoggedVendor();
					}
					$vendorList = '<input type="hidden" name="virtuemart_vendor_id" value="'.$object->virtuemart_vendor_id.'" />';
				} else 	$vendorList= ShopFunctions::renderVendorList($object->virtuemart_vendor_id,false);
		   } else {
				$vendorList = '<input type="hidden" name="virtuemart_vendor_id" value="1" />';
		   }
		   $this->assignRef('vendorList', $vendorList);
		}*/

	}


	function SetViewTitle($name ='', $msg ='') {
		$view = JRequest::getWord('view', JRequest::getWord('controller'));
		if ($name == '')
		$name = $view;
		if ($msg) {
			$msg = ' <span style="color: #666666; font-size: large;">' . $msg . '</span>';
		}
		//$text = strtoupper('COM_VIRTUEMART_'.$name );
		$viewText = JText::_('COM_VIRTUEMART_' . $name);
		if (!$task = JRequest::getWord('task'))
		$task = 'list';

		$taskName = ' <small><small>[ ' . JText::_('COM_VIRTUEMART_' . $task) . ' ]</small></small>';
		JToolBarHelper::title($viewText . ' ' . $taskName . $msg, 'head vm_' . $view . '_48');
		$this->assignRef('viewName',$viewText); //was $viewName?
	}

	function sort($orderby ,$name=null ){
		if (!$name) $name= 'COM_VIRTUEMART_'.strtoupper ($orderby);
		return JHTML::_('grid.sort' , JText::_($name) , $orderby , $this->lists['filter_order_Dir'] , $this->lists['filter_order']);
	}

	public function addStandardHiddenToForm($controller=null, $task=''){
		if (!$controller)	$controller = JRequest::getCmd('view');
		$option = JRequest::getCmd('option','com_virtuemart' );
		$hidden ='';
		if (array_key_exists('filter_order',$this->lists)) $hidden ='
			<input type="hidden" name="filter_order" value="'.$this->lists['filter_order'].'" />
			<input type="hidden" name="filter_order_Dir" value="'.$this->lists['filter_order_Dir'].'" />';
		return  $hidden.'
		<input type="hidden" name="task" value="'.$task.'" />
		<input type="hidden" name="option" value="'.$option.'" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="'.$controller.'" />
		<input type="hidden" name="view" value="'.$controller.'" />
		'. JHTML::_( 'form.token' );
	}

	function getToolbar() {

		// add required stylesheets from admin template
		$document    = JFactory::getDocument();
		$document->addStyleSheet('administrator/templates/system/css/system.css');
		//now we add the necessary stylesheets from the administrator template
		//in this case i make reference to the bluestork default administrator template in joomla 1.6
		$document->addCustomTag(
			'<link href="administrator/templates/bluestork/css/template.css" rel="stylesheet" type="text/css" />'."\n\n".
			'<!--[if IE 7]>'."\n".
			'<link href="administrator/templates/bluestork/css/ie7.css" rel="stylesheet" type="text/css" />'."\n".
			'<![endif]-->'."\n".
			'<!--[if gte IE 8]>'."\n\n".
			'<link href="administrator/templates/bluestork/css/ie8.css" rel="stylesheet" type="text/css" />'."\n".
			'<![endif]-->'."\n".
			'<link rel="stylesheet" href="administrator/templates/bluestork/css/rounded.css" type="text/css" />'."\n"
			);
		//load the JToolBar library and create a toolbar
		jimport('joomla.html.toolbar');
		JToolBarHelper::divider();
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		$bar = new JToolBar( 'toolbar' );
		//and make whatever calls you require
		$bar->appendButton( 'Standard', 'save', 'Save', 'save', false );
		$bar->appendButton( 'Separator' );
		$bar->appendButton( 'Standard', 'cancel', 'Cancel', 'cancel', false );
		//generate the html and return
		return $bar->render();
	}

	/**
	 * Additional grid function for custom toggles
	 *
	 * @return string HTML code to write the toggle button
	 */
	function toggle( $field, $i, $toggle, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{

		$img 	= $field ? $imgY : $imgX;
		if ($toggle == 'published') {
			// Stay compatible with grid.published
			$task 	= $field ? 'unpublish' : 'publish';
			$alt 	= $field ? JText::_('COM_VIRTUEMART_PUBLISHED') : JText::_('COM_VIRTUEMART_UNPUBLISHED');
			$action = $field ? JText::_('COM_VIRTUEMART_UNPUBLISH_ITEM') : JText::_('COM_VIRTUEMART_PUBLISH_ITEM');
		} else {
			$task 	= $field ? $toggle.'.0' : $toggle.'.1';
			$alt 	= $field ? JText::_('COM_VIRTUEMART_PUBLISHED') : JText::_('COM_VIRTUEMART_DISABLED');
			$action = $field ? JText::_('COM_VIRTUEMART_DISABLE_ITEM') : JText::_('COM_VIRTUEMART_ENABLE_ITEM');
		}

		if (JVM_VERSION>1) {
			return ('<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task .'\')" title="'. $action .'">'
				.JHTML::_('image', 'admin/' .$img, $alt, null, true) .'</a>');
		} else {
			return ('<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $task .'\')" title="'. $action .'">'
				.'<img src="images/'. $img .'" border="0" alt="'. $alt .'" /></a>');
		}

	}

}