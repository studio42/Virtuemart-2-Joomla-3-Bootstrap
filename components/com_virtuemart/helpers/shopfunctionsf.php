<?php
/**
*
* Contains shop functions for the front-end
*
* @package	VirtueMart
* @subpackage Helpers
*
* @author RolandD
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: shopfunctionsf.php 6502 2012-10-04 13:19:26Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


class shopFunctionsF {

	/**
	 *
	 */

	static public function getLoginForm($cart=FALSE,$order=FALSE,$url = 0){

		if(!class_exists('VirtuemartViewUser')) require(JPATH_VM_SITE . DS . 'views' . DS . 'user' .DS. 'view.html.php');
		$view = new VirtuemartViewUser();
		$view -> setLayout('login');

		$body = '';
		$show=TRUE;

		if($cart){
			$show = VmConfig::get('oncheckout_show_register', 1);
		}
		if($show==1){

			$view->assignRef('show',$show);

			$view->assignRef('order',$order);
			$view->assignRef('from_cart',$cart);
			$view->assignRef('url',$url);
			ob_start();
			$view->display();
			$body = ob_get_contents();
			ob_end_clean();
		}


		return $body;
	}

	/**
	 * @author Max Milbers
	 */
	static public function getLastVisitedCategoryId(){

		$session = JFactory::getSession();
		return $session->get('vmlastvisitedcategoryid', 0, 'vm');

	}

	/**
	 * @author Max Milbers
	 */
	static public function setLastVisitedCategoryId($categoryId){
		$session = JFactory::getSession();
		return $session->set('vmlastvisitedcategoryid', (int) $categoryId, 'vm');

	}

	/**
	 * @author Max Milbers
	 */
	static public function getLastVisitedManuId(){

		$session = JFactory::getSession();
		return $session->get('vmlastvisitedmanuid', 0, 'vm');

	}

	/**
	 * @author Max Milbers
	 */
	static public function setLastVisitedManuId($manuId){
		$session = JFactory::getSession();
		return $session->set('vmlastvisitedmanuid', (int) $manuId, 'vm');

	}

	static public function getAddToCartButton($orderable){

		if($orderable){
			vmJsApi::jPrice();
			$html = '<input type="submit" name="addtocart" class="addtocart-button" value="'.JText::_('COM_VIRTUEMART_CART_ADD_TO') .'" title="'.JText::_('COM_VIRTUEMART_CART_ADD_TO') .'" />';
		} else {
			$html = '<input name="addtocart" class="addtocart-button-disabled" value="'.JText::_('COM_VIRTUEMART_ADDTOCART_CHOOSE_VARIANT') .'" title="'.JText::_('COM_VIRTUEMART_ADDTOCART_CHOOSE_VARIANT') .'" />';
		}

		return $html;
	}

	/**
	 *
	 * @author Max Milbers
	 */
	static public function addProductToRecent($productId){
		$session = JFactory::getSession();
		$products_ids = $session->get('vmlastvisitedproductids', array(), 'vm');
		$key = array_search($productId,$products_ids);
		if($key!==FALSE){
			unset($products_ids[$key]);
		}
		array_unshift($products_ids,$productId);
		$products_ids = array_unique($products_ids);

		$maxSize = VmConfig::get('max_recent_products',3);
		if(count($products_ids)>$maxSize){
			array_splice($products_ids,$maxSize);
		}

		return $session->set('vmlastvisitedproductids', $products_ids, 'vm');
	}

	/**
	 * Gives ids the recently by the shopper visited products
	 *
	 * @author Max Milbers
	 */
	public function getRecentProductIds(){
		$session = JFactory::getSession();
		return $session->get('vmlastvisitedproductids', array(), 'vm');
	}


	/**
	* function to create a hyperlink
	*
	* @author RolandD
	* @param string $link
	* @param string $text
	* @param string $target
	* @param string $title
	* @param array $attributes
	* @return string
	*/
	public function hyperLink( $link, $text, $target='', $title='', $attributes='' ) {
		$options = array();
		if( $target ) {
			$options['target'] = $target;
		}
		if( $title ) {
			$options['title'] = $title;
		}
		if( $attributes ) {
			$options = array_merge($options, $attributes);
		}
		return JHTML::_('link', $link, $text, $options);
	}

	/**
	* A function to create a XHTML compliant and JS-disabled-safe pop-up link
	*
	* @author RolandD
	* @param string $link The HREF attribute
	* @param string $text The link text
	* @param int $popupWidth
	* @param int $popupHeight
	* @param string $target The value of the target attribute
	* @param string $title
	* @param string $windowAttributes
	* @return string
	*/
	public function vmPopupLink( $link, $text, $popupWidth=640, $popupHeight=480, $target='_blank', $title='', $windowAttributes='' ) {
		if( $windowAttributes ) {
			$windowAttributes = ','.$windowAttributes;
		}
		return self::hyperLink( $link, $text, '', $title, array("onclick" => "void window.open('$link', '$target', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=$popupWidth,height=$popupHeight,directories=no,location=no".$windowAttributes."');return false;" ));

	}


	/**
	 * Prepares a view for rendering email, then renders and sends
	 *
	 * @param object $controller
	 * @param string $viewName View which will render the email
	 * @param string $recipient shopper@whatever.com
	 * @param array $vars variables to assign to the view
	 */
	//TODO this is quirk, why it is using here $noVendorMail, but everywhere else it is using $doVendor => this make logic trouble
	static public function renderMail ($viewName, $recipient, $vars=array(),$controllerName = NULL,$noVendorMail = FALSE) {
		if(!class_exists('VirtueMartControllerVirtuemart')) require(JPATH_VM_SITE.DS.'controllers'.DS.'virtuemart.php');
// 		$format = (VmConfig::get('order_html_email',1)) ? 'html' : 'raw';

		$controller = new VirtueMartControllerVirtuemart();
		//Todo, do we need that? refering to http://forum.virtuemart.net/index.php?topic=96318.msg317277#msg317277
		$controller->addViewPath(JPATH_VM_SITE.DS.'views');

		$view = $controller->getView($viewName, 'html');
		if (!$controllerName) $controllerName = $viewName;
		$controllerClassName = 'VirtueMartController'.ucfirst ($controllerName) ;
		if (!class_exists($controllerClassName)) require(JPATH_VM_SITE.DS.'controllers'.DS.$controllerName.'.php');

		//Todo, do we need that? refering to http://forum.virtuemart.net/index.php?topic=96318.msg317277#msg317277
		$view->addTemplatePath(JPATH_VM_SITE.'/views/'.$viewName.'/tmpl');

		$vmtemplate = VmConfig::get('vmtemplate','default');
		if($vmtemplate=='default'){
			if(JVM_VERSION == 2){
				$q = 'SELECT `template` FROM `#__template_styles` WHERE `client_id`="0" AND `home`="1"';
			} else {
				$q = 'SELECT `template` FROM `#__templates_menu` WHERE `client_id`="0" AND `menuid`="0"';
			}
			$db = JFactory::getDbo();
			$db->setQuery($q);
			$template = $db->loadResult();
		} else {
			$template = $vmtemplate;
		}

		if($template){
			$view->addTemplatePath(JPATH_ROOT.DS.'templates'.DS.$template.DS.'html'.DS.'com_virtuemart'.DS.$viewName);
		} else {
			if(isset($db)){
				$err = $db->getErrorMsg() ;
			} else {
				$err = 'The selected vmtemplate is not existing';
			}
			if($err) vmError('renderMail get Template failed: '.$err);
		}

		foreach ($vars as $key => $val) {
			$view->$key = $val;
		}

		$user= self::sendVmMail($view, $recipient,$noVendorMail);
		if (isset($view->doVendor) && !$noVendorMail) {
			self::sendVmMail($view, $view->vendorEmail, TRUE);
		}
		return $user ;

	}


	/**
	 * With this function you can use a view to sent it by email.
	 * Just use a task in a controller
	 *
	 * @param string $view for example user, cart
	 * @param string $recipient shopper@whatever.com
	 * @param bool $vendor true for notifying vendor of user action (e.g. registration)
	 */

	private static function sendVmMail (&$view, $recipient, $noVendorMail=FALSE) {
		$jlang =JFactory::getLanguage();
		if(VmConfig::get('enableEnglish', 1)){
		     $jlang->load('com_virtuemart', JPATH_SITE, 'en-GB', TRUE);
		}
		$jlang->load('com_virtuemart', JPATH_SITE, $jlang->getDefault(), TRUE);
		$jlang->load('com_virtuemart', JPATH_SITE, NULL, TRUE);

		ob_start();
		$view->renderMailLayout($noVendorMail, $recipient);
		$body = ob_get_contents();
		ob_end_clean();

		$subject = (isset($view->subject)) ? $view->subject : JText::_('COM_VIRTUEMART_DEFAULT_MESSAGE_SUBJECT');
		$mailer = JFactory::getMailer();
		$mailer->addRecipient($recipient);
		$mailer->setSubject($subject);
		$mailer->isHTML(VmConfig::get('order_mail_html',TRUE));
		$mailer->setBody($body);

		if(!$noVendorMail){
			$replyto[0]=$view->vendorEmail;
			$replyto[1]= $view->vendor->vendor_name;
			$mailer->addReplyTo($replyto);
		}
 	/*	if (isset($view->replyTo)) {
 			$mailer->addReplyTo($view->replyTo);
 		}*/

		if (isset($view->mediaToSend)) {
			foreach ((array)$view->mediaToSend as $media) {
				//Todo test and such things.
				$mailer->addAttachment($media);
			}
		}

		// set proper sender
		$sender = array();
		if (!empty($view->vendorEmail) and VmConfig::get('useVendorEmail',0))
		{
			$sender[0] = $view->vendorEmail;
			$sender[1] = $view->vendor->vendor_name;
		}
		else
		{
			// use default joomla's mail sender
			$app = JFactory::getApplication();
			$sender[0] = $app->getCfg('mailfrom');
			$sender[1] = $app->getCfg('fromname');
		}
		$mailer->setSender($sender);

		return $mailer->Send();
	}


	/**
	 * This function sets the right template on the view
	 * @author Max Milbers
	 */
	static function setVmTemplate($view,$catTpl=0,$prodTpl=0,$catLayout=0,$prodLayout=0){

		//Lets get here the template set in the shopconfig, if there is nothing set, get the joomla standard
		$template = VmConfig::get('vmtemplate','default');
		$db = JFactory::getDBO();
		//Set specific category template
		if(!empty($catTpl) && empty($prodTpl)){
			if(is_Int($catTpl)){
				$q = 'SELECT `category_template` FROM `#__virtuemart_categories` WHERE `virtuemart_category_id` = "'.(int)$catTpl.'" ';
				$db->setQuery($q);
				$temp = $db->loadResult();
				if (!empty($temp)) $template = $temp;
			} else {
				$template = $catTpl;
			}
		}

		//Set specific product template
		if(!empty($prodTpl)){
			if(is_Int($prodTpl)){
				$q = 'SELECT `product_template` FROM `#__virtuemart_products` WHERE `virtuemart_product_id` = "'.(int)$prodTpl.'" ';
				$db->setQuery($q);
				$temp = $db->loadResult();
				if (!empty($temp)) $template = $temp;
			} else {
				$template = $prodTpl;
			}
		}

		shopFunctionsF::setTemplate($template);

		//Lets get here the layout set in the shopconfig, if there is nothing set, get the joomla standard
		if(JRequest::getWord('view')=='virtuemart'){
			$layout = VmConfig::get('vmlayout','default');
			$view->setLayout(strtolower($layout));
		} else {

			if(empty($catLayout) and empty($prodLayout)){
				$catLayout = VmConfig::get ('productlayout','default');
			}

			//Set specific category layout
			if(!empty($catLayout) && empty($prodLayout)){
				if(is_Int($catLayout)){
					$q = 'SELECT `layout` FROM `#__virtuemart_categories` WHERE `virtuemart_category_id` = "'.(int)$catLayout.'" ';
					$db->setQuery($q);
					$temp = $db->loadResult();
					if (!empty($temp)) $layout = $temp;
				} else {
					$layout = $catLayout;
				}
			}

			//Set specific product layout
			if(!empty($prodLayout)){
				if(is_Int($prodLayout)){
					$q = 'SELECT `layout` FROM `#__virtuemart_products` WHERE `virtuemart_product_id` = "'.(int)$prodLayout.'" ';
					$db->setQuery($q);
					$temp = $db->loadResult();
					if (!empty($temp)) $layout = $temp;
				} else {
					$layout = $prodLayout;
				}
			}

		}

		if(!empty($layout)){
			$view->setLayout(strtolower($layout));
		}


	}

	/**
	 * Final setting of template
	 *
	 * @author Max Milbers
	 */
	static function setTemplate( $template ){

		if(!empty($template) && $template!='default'){
			if (is_dir(JPATH_THEMES.DS.$template)) {
				//$this->addTemplatePath(JPATH_THEMES.DS.$template);
				$mainframe = JFactory::getApplication('site');
				$mainframe->set('setTemplate', $template);
			} else{
				JError::raiseWarning(412,'The chosen template couldnt found on the filesystem: '.$template);
			}
		} else{
				//JError::raiseWarning('No template set : '.$template);
		}
	}

	/**
	 *
	 * Enter description here ...
	 * @author Max Milbers
	 * @author Iysov
	 * @param string $string
	 * @param int $maxlength
	 * @param string $suffix
	 */
	static public function limitStringByWord($string, $maxlength, $suffix=''){
		if(function_exists('mb_strlen')) {
			// use multibyte functions by Iysov
			if(mb_strlen($string)<=$maxlength) return $string;
			$string = mb_substr($string,0,$maxlength);
			$index = mb_strrpos($string, ' ');
			if($index===FALSE) {
				return $string;
			} else {
				return mb_substr($string,0,$index).$suffix;
			}
		} else { // original code here
			if(strlen($string)<=$maxlength) return $string;
			$string = substr($string,0,$maxlength);
			$index = strrpos($string, ' ');
			if($index===FALSE) {
				return $string;
			} else {
				return substr($string,0,$index).$suffix;
			}
		}
	}

	/**
	 * Admin UI Tabs
	 * Gives A Tab Based Navigation Back And Loads The Templates With A Nice Design
	 * @param $load_template = a key => value array. key = template name, value = Language File contraction
	 * @example 'shop' => 'COM_VIRTUEMART_ADMIN_CFG_SHOPTAB'
	 */
	static function buildTabs( $view, $load_template = array()) {

		vmJsApi::js( 'vmtabs');
		$html = '<div id="ui-tabs">';
		$i = 1;
		foreach ( $load_template as $tab_content => $tab_title ) {
			$html .= '<div id="tab-' . $i . '" class="tabs" title="' . JText::_ ( $tab_title ) . '">';
			$html .= $view->loadTemplate ( $tab_content );
			$html .= '<div class="clear"></div>
			    </div>';
			$i ++;
		}
		$html .= '</div>';
		echo $html;
	}

	/**
	 * Creates also for BE app a correct site link
	 * @static
	 * @param $link
	 */
	static function getSiteUrl($link){

		//site url if use administrator application
		$be_site = substr(JURI::root(), 0, -1);

		// Checking if we are in the backend and change to the app site
		if(JPATH_BASE == JPATH_ADMINISTRATOR) {
			JFactory::$application = JApplication::getInstance('site');
		}

		//VM homepage sef url (for example) - SEF url for be and fe. If you use clasic url will work also.
		$link = JURI::root().substr(JRoute::_('index.php?option=com_virtuemart&view=virtuemart'),strlen(JURI::base(TRUE))+1);

		// Check if we are in the backend again
		if(JPATH_BASE == JPATH_ADMINISTRATOR) {
			// when link produced in be we need to remove livesite/administrator in case that been added from URL (usually added instead
			//we have set the application to the site app)
			$link = str_replace($be_site .'/administrator', $be_site, $link);
			//echo $link;

			// Setting back the administrator app
			JFactory::$application = JApplication::getInstance('administrator');
		}
		return $link;
	}


	/**
	 * TODO remove this function, this function is not necessary and give back the wrong uri
	 * The right thing is to use JURI::root();, which is always giving back something like
	 * https://mydomain.com/myjoomla  , but JURI::base() is giving back the relative base, that means when the backend is used,
	 * it gives back https://mydomain.com/myjoomla/administrator;
	 * @static
	 * @return string
	 */
	/*	static function getBaseUrl() {
			$uri = JURI::getInstance();
			$baseUrl = $uri->getScheme() . "://" . $uri->getHost();
			if($uri->getPort()){
				$baseUrl = $baseUrl . ":" . $uri->getPort();
			}
			$base = $uri::base();
		$baseUrl = JURI::root();
		//vmdebug('getBaseUrl',$base,$root);
		//$baseUrl =  $baseUrl . "/";
		return $baseUrl;
	}**/

	/**
	 * Align in plain text the strings
	 * $string text to resize
	 * $size, number of char
	 * $toUpper uppercase Y/N ?
	 * @author kohl patrick
	 */
	function tabPrint( $size, $string,$header = FALSE){
		if ($header) $string = strtoupper (JText::_($string ) );
		sprintf("%".$size.".".$size."s",$string ) ;

	}
	function toupper($strings) {
		foreach ($strings as &$string) {
			$string = strtoupper (JText::_($string ) );
		}
		return $strings;

	}


	static function getComUserOption() {
	 if ( JVM_VERSION===1 ) {
		return 'com_user';
	    } else {
		return 'com_users';
	    }
	}

	/**
	 * Checks if Joomla language keys exist and combines it according to existing keys.
	 * @string $pkey : primary string to search for Language key (must have %s in the string to work)
	 * @string $skey : secondary string to search for Language key
	 * @return string
	 * @author Max Milbers
	 * @author Patrick Kohl
	 */
	function translateTwoLangKeys($pkey,$skey){
		$upper = strtoupper($pkey).'_2STRINGS';
		if( JText::_($upper) !== $upper ) {
			return JText::sprintf($upper,JText::_($skey));
		} else {
			return JText::_($pkey).' '.JText::_($skey);
		}
	}

	/**
	* Writes a PDF icon
	* @author Patrick Kohl
	* @param string $link
	* @param boolean $use_icon
	* @deprecated
	*/
	function PdfIcon( $link, $use_icon=TRUE,$modal=TRUE ) {

		return VmView::linkIcon($link,'COM_VIRTUEMART_PDF','pdf_button','pdf_button_enable',$modal,$use_icon);

	}

	/**
	 * Writes an Email icon
	 * @author Patrick Kohl
	 * @param string $link
	 * @param boolean $use_icon
	 * @deprecated
	 */
	function EmailIcon( $virtuemart_product_id, $use_icon,$modal ) {
		if ($virtuemart_product_id > 0  ) {
			$link = 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id='.$virtuemart_product_id.'&tmpl=component' ;
			return VmView::linkIcon($link,'COM_VIRTUEMART_EMAIL','emailButton','show_emailfriend',$modal ,$use_icon);
		}
	}

	/**
	 * @author RolandD, Christopher Roussel
	 *
	 * @deprecated
	 */
	function PrintIcon( $link='', $use_icon=TRUE, $add_text='' ) {

		if (VmConfig::get('show_printicon', 1) == '1') {

			$folder = (JVM_VERSION===1) ? '/images/M_images/' : '/media/system/images/';

			// checks template image directory for image, if non found default are loaded
			if ( $use_icon ) {
				$filter = JFilterInput::getInstance();
				$text = JHtml::_('image.site', 'printButton.png', $folder, NULL, NULL, JText::_('COM_VIRTUEMART_PRINT'));
				$text .= $filter->clean($add_text);
			} else {
				$text = '|&nbsp;'. JText::_('COM_VIRTUEMART_PRINT'). '&nbsp;|';
			}
			$isPopup = JRequest::getVar( 'pop' );
			if ( $isPopup ) {
				// Print Preview button - used when viewing page
				$html = '<span class="vmNoPrint">
					<a href="javascript:void(0)" onclick="javascript:window.print(); return false;" title="'. JText::_('COM_VIRTUEMART_PRINT').'">
					'. $text .'
					</a></span>';
				return $html;
			} else {
				// Print Button - used in pop-up window
				return self::vmPopupLink($link, $text, 640, 480, '_blank', JText::_('COM_VIRTUEMART_PRINT'));
			}
		}

	}
}