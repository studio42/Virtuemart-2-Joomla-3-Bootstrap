<?php
/**
 * Administrator menu helper class
 *
 * This class was derived from the show_image_in_imgtag.php and imageTools.class.php files in VM.  It provides some
 * image functions that are used throughout the VirtueMart shop.
 *
 * @package	VirtueMart
 * @subpackage Helpers
 * @author Eugen Stranz
 * @copyright Copyright (c) 2004-2008 Soeren Eberhardt-Biermann, 2009 VirtueMart Team. All rights reserved.
 */

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ();

class AdminUIHelper {

	public static $vmAdminAreaStarted = false;
	public static $backEnd = true;

   /**
     * Start the administrator area table
     *
     * The entire administrator area with contained in a table which include the admin ribbon menu
     * in the left column and the content in the right column.  This function sets up the table and
     * displayes the admin menu in the left column.
     */
    static function startAdminArea($backEnd=true) {
		if (JRequest::getWord ( 'format') =='pdf') return;
		if (JRequest::getWord ( 'tmpl') =='component') {
			self::$backEnd=false;
			include('front/edit.html.php');
		}
    	if(self::$vmAdminAreaStarted) return;
    	self::$vmAdminAreaStarted = true;
		$front = JURI::root(true).'/components/com_virtuemart/assets/';
		$admin = JURI::root(true).'/administrator/components/com_virtuemart/assets/';
		$document = JFactory::getDocument();

		//loading defaut admin CSS
		// $document->addStyleSheet($admin.'css/admin_ui.css');
		// $document->addStyleSheet($admin.'css/admin_menu.css');
		if (JVM_VERSION===2 || self::$backEnd===false) {
			$document->addStyleSheet($front.'css/ui/bootstrap.css');
			// JHtml::_('bootstrap.loadCss') ;
			JHtml::_('bootstrap.tooltip') ;
			if (JVM_VERSION===2) $document->addScript($admin.'js/j25fixes.js');
			$document->setMetadata('viewport', 'width=device-width, initial-scale=1.0'); 
			JHtml::_('behavior.framework');
		}
		$document->addStyleSheet($admin.'css/admin.styles.css');
		$document->addStyleSheet($admin.'css/toolbar_images.css');
		$document->addStyleSheet($admin.'css/menu_images.css');
		$document->addStyleSheet($admin.'css/fileinput.css');
		// $document->addStyleSheet($front.'css/chosen.css');
		// $document->addStyleSheet($front.'css/vtip.css');
		$document->addStyleSheet($front.'css/jquery.fancybox-1.3.4.css');
		//$document->addStyleSheet($admin.'css/jqtransform.css');

		//loading defaut script
		// JHtml::_('bootstrap.tooltip');
		$document->addScript($front.'js/fancybox/jquery.mousewheel-3.0.4.pack.js');
		$document->addScript($front.'js/fancybox/jquery.easing-1.3.pack.js');
		$document->addScript($front.'js/fancybox/jquery.fancybox-1.3.4.pack.js');
		$document->addScript($admin.'js/jquery.coookie.js');
		// $document->addScript($front.'js/chosen.jquery.min.js');
		JHtml::_('formbehavior.chosen', 'select');
		$document->addScript($admin.'js/vm2admin.js');
		$document->addScript($admin.'js/fileinput.js');
		//$document->addScript($admin.'js/jquery.jqtransform.js');
		if (JText::_('COM_VIRTUEMART_JS_STRINGS') == 'COM_VIRTUEMART_JS_STRINGS') $vm2string = "editImage: 'edit image',select_all_text: 'select all options',select_some_options_text: 'select some options'" ;
		else $vm2string = JText::_('COM_VIRTUEMART_JS_STRINGS') ;
		//prevent joomlaJtext bug.
		JText::script('JGLOBAL_SELECT_SOME_OPTIONS');
		// old type image var tip_image='".JURI::root(true)."/components/com_virtuemart/assets/js/images/vtip_arrow.png';
		// fix for jsonRequest in front admin
		$baseUrlCurrent =  self::$backEnd===false ? '/' : '/administrator/';

		$document->addScriptDeclaration ( "
//<![CDATA[
		var vmBaseUrl = '". JURI::root(true).$baseUrlCurrent. "';
		var vm2string ={".$vm2string."} ;
		 jQuery( function($) {

			$('dl#system-message').hide().slideDown(400);
			$('.virtuemart-admin-area .toggler').vm2admin('toggle');
			$('#admin-ui-menu').vm2admin('accordeon');
			if ( $('#admin-ui-tabs').length  ) {
				$('#admin-ui-tabs').vm2admin('tabs',virtuemartcookie).find('select').chosen({enable_select_all: true,select_all_text : vm2string.select_all_text,select_some_options_text:vm2string.select_some_options_text}); 
			}

			// TIPS IS now from bootstrap  $('#content-box [title]').vm2admin('tips',tip_image);
			jQuery('.hasTip').tooltip({});

			$('.fb-modal-toggle,.modalbox').fancybox();
			// $('.modal').modal();
			$('.reset-value').click( function(e){
				e.preventDefault();
				none = '';
				jQuery(this).parent().find('.ui-autocomplete-input').val(none);
				
			});

		});
//]]>
		");
		?>


		<?php
		// Include ALU System
		if (self::$backEnd) {
		require_once JPATH_VM_ADMINISTRATOR.DS.'liveupdate'.DS.'liveupdate.php';
		?>
		<div class="vm2admin">
		<div class="row-fluid">
		<div class="span12">
			<div class="btn btn-large btn-inverse" id="sidebar-toggle"><i class="icon-chevron-left"></i></div>
			<div class="span3 j-sidebar-container" id="j-sidebar-container">
				<div class="well well-small"><a href="index.php?option=com_virtuemart&view=virtuemart" ><div class="menu-vmlogo"></div></a></div>
				<?php AdminUIHelper::showAdminMenu();
				if ($admin = JFactory::getUser()->authorise('core.admin') ) {
					?>
					<div class="menu-notice hidden-phone">
						<?php echo LiveUpdate::getIcon(array(),'notice'); ?>
						<?php echo VmConfig::getInstalledVersion(); ?>
					</div>
					<?php
				} ?>

			</div>
		<?php } 
		$view = jRequest::getWord('view','virtuemart');
		if (!self::$backEnd)  $span = $view === 'virtuemart' ? 'span8' : '';
		elseif ($view == 'virtuemart') $span = 'span5';
		else $span='span9';
		?>
			<div id="j-main-container" class="<?php echo $span ?>">
		<?php
	}

	/**
	 * Close out the adminstrator area table.
	 * @author RickG, Max Milbers, Patrick Kohl
	 * @boolean $ajaxResults, in case of raw/ajax result exist, include the file
	 * Note the script must be set after joomla javascript declaration to overide it.
	 */
	static function endAdminArea($ajaxResults = false) {
		if (!self::$backEnd) {
			echo '</div></div></div></div>';
			vmJsApi::js ('jquery.ui.autocomplete.html');
			if ($ajaxResults) include('ajax/results.html.php');
			include('front/langs.html.php');
			return;
		}
		self::$vmAdminAreaStarted = false;
		if (VmConfig::get('debug') == '1') {
		//TODO maybe add debuggin again here
//		include(JPATH_VM_ADMINISTRATOR.'debug.php');
		}
		?>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		</div>

		<?php 
		// add ajax results script
		if ($ajaxResults) include('ajax/results.html.php');

	}

	/**
	 * Admin UI Tabs
	 * Gives A Tab Based Navigation Back And Loads The Templates With A Nice Design
	 * @param $load_template = a key => value array. key = template name, value = Language File contraction
	 * @params $cookieName = choose a cookiename or leave empty if you don't want cookie tabs in this place
	 * @example 'shop' => 'COM_VIRTUEMART_ADMIN_CFG_SHOPTAB'
	 */
	static public function buildTabs($view, $load_template = array(),$cookieName='') {
		$cookieName = JRequest::getWord('view','virtuemart').$cookieName;
		$document = JFactory::getDocument ();
		$document->addScriptDeclaration ( '
		var virtuemartcookie="'.$cookieName.'";
		');
		$defaultTab = key($load_template);
		$html = JHtml::_('bootstrap.startTabSet', $cookieName, array('active' => 'page-'.$defaultTab)); 
		// $html = JHtml::_('bootstrap.startTabSet', $cookieName); 
		foreach ( $load_template as $tab_content => $tab_title ) {
			$html .= JHtml::_('bootstrap.addTab', $cookieName , 'page-'.$tab_content, JText::_($tab_title, true)); 
			// $html .= '<div class="tabs" title="' . JText::_ ( $tab_title ) . '">';
			$html .= $view->loadTemplate ( $tab_content );
			$html .= JHtml::_('bootstrap.endTab');
		}
		$html .= JHtml::_('bootstrap.endTabSet'); 
		echo $html;
	}

	/**
	 * Admin UI Tabs Imitation
	 * Gives A Tab Based Navigation Back And Loads The Templates With A Nice Design
	 * @param $return = return the start tag or the closing tag - choose 'start' or 'end'
	 * @params $language = pass the language string
	 */
	static function imitateTabs($return,$language = '') {
		if ($return == 'start') {
			$document = JFactory::getDocument ();
			$document->addScriptDeclaration ( '
			var virtuemartcookie="vm-tab";
			');
			$html = JHtml::_('bootstrap.startTabSet', 'solo', array('active' => 'page-solo')); 
			$html .= JHtml::_('bootstrap.addTab', 'solo' , 'page-solo', JText::_($language, true)); 
			echo $html;
		}
		if ($return == 'end') {
			$html = JHtml::_('bootstrap.endTab');
			$html .= JHtml::_('bootstrap.endTabSet'); 
			echo $html;
		}
	}

	/**
	 * Build an array containing all the menu items.
	 *
	 * @param int $moduleId Id of the module to filter on
	 */
	static function _getAdminMenu($moduleId = 0) {
		$db = JFactory::getDBO ();
		$menuArr = array ();
		$admin = JFactory::getUser()->authorise('core.admin');
		$manage = JFactory::getUser()->authorise('core.manage');
		$filter [] = "jmmod.published='1'";
		$filter [] = "item.published='1'";
		$filter [] = "jmmod.is_admin='1'";
		if (!$admin) {
			$filter [] = "item.view!='config'";
			$filter [] = "item.view!='updatesmigration'";
		}
		// var_dump($admin,$filter);
		// if (!$manage) {
			// $filter [] = "item.view!='config'";
		// }
		if (! empty ( $moduleId )) {
			$filter [] = 'vmmod.module_id=' . ( int ) $moduleId;
		}

		$query = 'SELECT `jmmod`.`module_id`, `module_name`, `module_perms`, `id`, `name`, `link`, `depends`, `icon_class`, `view`, `task`
						FROM `#__virtuemart_modules` AS jmmod
						LEFT JOIN `#__virtuemart_adminmenuentries` AS item ON `jmmod`.`module_id`=`item`.`module_id`
						WHERE  ' . implode ( ' AND ', $filter ) . '
						ORDER BY `jmmod`.`ordering`, `item`.`ordering` ';

		$db->setQuery ( $query );
		$result = $db->loadAssocList ();
		//		echo '<pre>'.print_r($query,1).'</pre>';
		for($i = 0, $n = count ( $result ); $i < $n; $i ++) {
			$row = $result [$i];
			$menuArr [$row['module_id']] ['title'] = 'COM_VIRTUEMART_' . strtoupper ( $row['module_name'] ) . '_MOD';
			$menuArr [$row['module_id']] ['items'] [] = $row ;
		}
		return $menuArr;
	}

	/**
	 * Display the administrative ribbon menu.
	 * @todo The link should be done better
	 */
	static function showAdminMenu() {
		$document = JFactory::getDocument ();
		$moduleId = JRequest::getInt ( 'module_id', 0 );
		$collapse_in= "";//" in";
		$menuItems = AdminUIHelper::_getAdminMenu ( $moduleId );
		$modCount = 1; ?>
<!--		<div>Virtuemart</div>-->
		<div class="accordion sidebar-nav" id="vmmenu">
		<?php
		foreach ( $menuItems as $item ) { 
			$vmd = 'vmd'.$modCount ;
			?>
			<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#vmmenu" href="#<?php echo $vmd ?>">
						<i class="icon-chevron-down pull-right"></i><?php echo JText::_ ( $item ['title'] )?>
					</a>
				</div>
				<div id="<?php echo $vmd ?>" class="accordion-body collapse<?php echo $collapse_in ?>">
				<div class="accordion-inner">
					<ul class="nav nav-list">
					<?php
					foreach ( $item ['items'] as $link ) {
						$target='';
						if ($link ['name'] == '-') {
							// it was emtpy before
						} else {
							if (strncmp ( $link ['link'], 'http', 4 ) === 0) {
								$url = $link ['link'];
								$target='TARGET="_blank"';
							} else {
								$url = ($link ['link'] === '') ? 'index.php?option=com_virtuemart' :$link ['link'] ;
								$url .= $link ['view'] ? "&view=" . $link ['view'] : '';
								$url .= $link ['task'] ? "&task=" . $link ['task'] : '';
								// $url .= $link['extra'] ? $link['extra'] : '';
							}
							?>
						<li>
							<a href="<?php echo $url; ?>" <?php echo $target; ?>><span class="<?php echo $link ['icon_class'] ?> pull-right"></span><?php echo JText::_ ( $link ['name'] )?></a>
						</li>
						<?php
						}
					}
					?>
					</ul>
				</div>
				</div>
			</div>
			<?php
			$collapse_in ='';
			$modCount ++;
		} ?>
		</div>
		<?php
	}

}

?>