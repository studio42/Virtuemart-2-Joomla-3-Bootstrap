<?php
if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR .'/components/com_virtuemart/helpers/config.php');
/**
 *
 * Class to provide js API of vm
 * @author Patrick Kohl
 * @author Max Milbers
 */
class vmJsApi{


	private function __construct() {

	}
	/**
	 * Write a <script></script> element
	 * @param   string   path to file
	 * @param   string   library name
	 * @param   string   library version
	 * @param   boolean  load minified version
	 * @return  nothing
	 */

	public static function js($namespace,$path=FALSE,$version='', $minified = NULL)
	{

		static $loaded = array();
		// Only load once
		// using of namespace assume same library have same namespace
		// NEVER WRITE FULL NAME AS $namespace IN CASE OF REVISION NUMBER IF YOU WANT PREVENT MULTI LOAD !!!
		// eg. $namespace = 'jquery.1.8.6' and 'jquery.1.6.2' does not prevent load it
		// use $namespace = 'jquery',$revision ='1.8.6' , $namespace = 'jquery',$revision ='1.6.2' ...
		// loading 2 time a JS file with this method simply return and do not load it the second time


		if (!empty($loaded[$namespace])) {
			return;
		}
		$file = vmJsApi::setPath($namespace,$path,$version, $minified , 'js');
		$document = JFactory::getDocument();
		$document->addScript( $file );
		$loaded[$namespace] = TRUE;
	}

	/**
	 * Write a <link ></link > element
	 * @param   string   path to file
	 * @param   string   library name
	 * @param   string   library version
	 * @param   boolean   library version
	 * @return  nothing
	 */

	public static function css($namespace,$path = FALSE ,$version='', $minified = NULL)
	{

		static $loaded = array();

		// Only load once
		// using of namespace assume same css have same namespace
		// loading 2 time css with this method simply return and do not load it the second time

		if (!empty($loaded[$namespace])) {
			return;
		}
		$file = vmJsApi::setPath( $namespace,$path,  $version='', $minified , 'css');

		$document = JFactory::getDocument();
		$document->addStyleSheet($file);
		$loaded[$namespace] = TRUE;

	}

	/*
	 * Set file path(look in template if relative path)
	 */
	public static function setPath( $namespace ,$path = FALSE ,$version='' ,$minified = NULL , $ext = 'js', $absolute_path=false)
	{

		$version = $version ? '.'.$version : '';
		$min	 = $minified ? '.min' : '';
		$file 	 = $namespace.$version.$min.'.'.$ext ;
		$template = JFactory::getApplication()->getTemplate() ;
		if ($path === FALSE) {
			$uri = JPATH_THEMES .'/'. $template.'/'.$ext ;
			$path= 'templates/'. $template .'/'.$ext ;
		}

		if (strpos($path, 'templates/'. $template ) !== FALSE)
		{
			// Search in template or fallback
			if (!file_exists($uri.'/'. $file)) {
				$assets_path = VmConfig::get('assets_general_path','components/com_virtuemart/assets/') ;
				$path = str_replace('templates/'. $template.'/',$assets_path, $path);
				// vmdebug('setPath',$assets_path,$path);
				// vmWarn('file not found in tmpl :'.$file );
			}
			if ($absolute_path) {
				$path = JPATH_BASE .'/'.$path;
			} else {
				$path = JURI::root(TRUE) .'/'.$path;
			}

		}
		elseif (strpos($path, '//') === FALSE)
		{
			if ($absolute_path) {
				$path = JPATH_BASE .'/'.$path;
			} else {
				$path = JURI::root(TRUE) .'/'.$path;
			}
		}
		return $path.'/'.$file ;
	}
	/**
	 * ADD some javascript if needed
	 * Prevent duplicate load of script
	 * @ Author KOHL Patrick
	 */
	static function jQuery() {
		// jquery and no conflict is provided by joomla 3
		JHtml::_('jquery.framework');
		$isSite = JFactory::getApplication()->isSite();
		if (!$isSite) {
			JHtml::_('jquery.ui');
			vmJsApi::js ('jquery.ui.autocomplete.html');
		}
		return true;
	}
	// Virtuemart product and price script
	static function jPrice()
	{

		if (!VmConfig::get ('jprice', TRUE) and JFactory::getApplication ()->isSite ()) {
			return FALSE;
		}
		static $jPrice;
		// If exist exit
		if ($jPrice) {
			return;
		}
		vmJsApi::jQuery();

		$lang = JFactory::getLanguage();
		$lang->load('com_virtuemart');
		vmJsApi::jSite();

		$closeimage = JURI::root(TRUE) .'/components/com_virtuemart/assets/images/facebox/closelabel.png';
		$jsVars  = '
//<![CDATA[
		'."vmSiteurl = '". JURI::root( ) ."' ;\n" ;
		if (VmConfig::get ('vmlang_js', 1))  {
			$jsVars .= "vmLang = '&amp;lang=" . substr (VMLANG, 0, 2) . "' ;\n";
		}
		else {
			$jsVars .= 'vmLang = "";' . "\n";
		}

		if(VmConfig::get('addtocart_popup',1)){
			$jsVars .= "Virtuemart.addtocart_popup = '".VmConfig::get('addtocart_popup',1)."' ; \n";
			if(VmConfig::get('usefancy',0)){
				$jsVars .= "usefancy = true";
				vmJsApi::js( 'fancybox/jquery.fancybox-1.3.4.pack');
				vmJsApi::css('jquery.fancybox-1.3.4');
			} else {//This is just there for the backward compatibility
				$jsVars .= "vmCartText = '". addslashes( JText::_('COM_VIRTUEMART_CART_PRODUCT_ADDED') )."' ;\n" ;
				$jsVars .= "vmCartError = '". addslashes( JText::_('COM_VIRTUEMART_MINICART_ERROR_JS') )."' ;\n" ;
				$jsVars .= "loadingImage = '".JURI::root(TRUE) ."/components/com_virtuemart/assets/images/facebox/loading.gif' ;\n" ;
				$jsVars .= "closeImage = '".$closeimage."' ; \n";
				//This is necessary though and should not be removed without rethinking the whole construction

				$jsVars .= "usefancy = false";
				vmJsApi::js( 'facebox' );
				vmJsApi::css( 'facebox' );
			}
		}

		$jsVars .= '
//]]>
';
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($jsVars);
		vmJsApi::js( 'vmprices');

		$jPrice = TRUE;
		return TRUE;
	}

	// Virtuemart Site Js script
	static function jSite()
	{

		if (!VmConfig::get ('jsite', TRUE) and JFactory::getApplication ()->isSite ()) {
			return FALSE;
		}
		vmJsApi::js('vmsite');
	}

	static function JcountryStateList($stateIds, $prefix) {

		$id = $prefix . 'virtuemart_state_id';
		// prevent using same ID, in all case this break the sate render if id is same.
		static $keys = array();
		if (isset($keys[$id]) ) return ;
		$keys[$id] = true ;
		$document = JFactory::getDocument();
		VmJsApi::jSite();
		$document->addScriptDeclaration(' 
//<![CDATA[
		jQuery( function($) {
			$("#'.$prefix.'virtuemart_country_id").vm2front("list",{dest : "#'.$id.'",ids : "'.$stateIds.'"});
		});
//]]>
		');
		$JcountryStateList = TRUE;
		return;
	}


	static function JvalideForm($name='#adminForm')
	{
		static $jvalideForm;
		// If exist exit
		if ($jvalideForm === $name) {
			return;
		}
		$document = JFactory::getDocument();
		$document->addScriptDeclaration( "
//<![CDATA[
			jQuery(document).ready(function() {
				jQuery('".$name."').validationEngine();
			});
//]]>
"  );
		if ($jvalideForm) {
			return;
		}
		vmJsApi::js( 'jquery.validationEngine');

		$lg = JFactory::getLanguage();
		$lang = substr($lg->getTag(), 0, 2);
		/*$existingLang = array("cz", "da", "de", "en", "es", "fr", "it", "ja", "nl", "pl", "pt", "ro", "ru", "tr");
		if (!in_array ($lang, $existingLang)) {
			$lang = "en";
		}*/
		$vlePath = vmJsApi::setPath('languages/jquery.validationEngine-'.$lang, FALSE , '' ,$minified = NULL ,   'js', true);
		if(file_exists($vlePath) and !is_dir($vlePath)){
			vmJsApi::js( 'languages/jquery.validationEngine-'.$lang );
		} else {
			vmJsApi::js( 'languages/jquery.validationEngine-en' );
		}

		vmJsApi::css ( 'validationEngine.template' );
		vmJsApi::css ( 'validationEngine.jquery' );
		$jvalideForm = $name;
	}

	// Virtuemart product and price script
	static function jCreditCard()
	{

		static $jCreditCard;
		// If exist exit
		if ($jCreditCard) {
			return;
		}
		JFactory::getLanguage()->load('com_virtuemart');


		$js = "
//<![CDATA[
		var ccErrors = new Array ()
		ccErrors [0] =  '" . addslashes( JText::_('COM_VIRTUEMART_CREDIT_CARD_UNKNOWN_TYPE') ). "';
		ccErrors [1] =  '" . addslashes( JText::_("COM_VIRTUEMART_CREDIT_CARD_NO_NUMBER") ). "';
		ccErrors [2] =  '" . addslashes( JText::_('COM_VIRTUEMART_CREDIT_CARD_INVALID_FORMAT')) . "';
		ccErrors [3] =  '" . addslashes( JText::_('COM_VIRTUEMART_CREDIT_CARD_INVALID_NUMBER')) . "';
		ccErrors [4] =  '" . addslashes( JText::_('COM_VIRTUEMART_CREDIT_CARD_WRONG_DIGIT')) . "';
		ccErrors [5] =  '" . addslashes( JText::_('COM_VIRTUEMART_CREDIT_CARD_INVALID_EXPIRE_DATE')) . "';
//]]>
		";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		$jCreditCard = TRUE;
		return TRUE;
	}

	/**
	 * ADD some CSS if needed
	 * Prevent duplicate load of CSS stylesheet
	 * @ Author KOHL Patrick
	 */

	static function cssSite() {

		if (!VmConfig::get ('css', TRUE)) {
			return FALSE;
		}
		static $cssSite;
		if ($cssSite) {
			return;
		}
		// Get the Page direction for right to left support
		$document = JFactory::getDocument ();
		$direction = $document->getDirection ();
		$cssFile = 'vmsite-' . $direction ;

		// If exist exit
		vmJsApi::css ( $cssFile ) ;
		$cssSite = TRUE;
		return TRUE;
	}

	/*
	 * Virtuemart Datepicker script
	 * Author P. Kohl
	 * jquery.ui calendar code
	 * @ $date the date to display
	 * @ $name field name
	 * @ id not in use
	 * @ yearRange from - to year
	 * @ prepend Text before calendar
	 * @ return html calendar code
	 * $yearRange format >> 1980:2010
	 * removed ID can break javascript and has no real role
	 */
	static function jDate($date='',$name="date",$id=NULL,$resetBt = TRUE, $yearRange='',$prepend ='') {

		if ($yearRange) {
			$yearRange = 'yearRange: "' . $yearRange . '",';
		}
		if ($date == "0000-00-00 00:00:00") {
			$date = 0;
		}
		if ($id === null) {
			$id = $name;
		}
		static $jDate;

		$dateFormat = JText::_('COM_VIRTUEMART_DATE_FORMAT_INPUT_J16');//="m/d/y"
		$search  = array('m', 'd');
		$replace = array('mm', 'dd');
		$jsDateFormat = str_replace($search, $replace, $dateFormat);

		if ($date) {
			$formatedDate = JHTML::_('date', $date, $dateFormat );
		}
		else {
			$formatedDate = JText::_('COM_VIRTUEMART_NEVER');
		}
		$display  = '<span class="input-append'. ( $prepend ? ' input-prepend' : '' ) .'">';
		if ( $prepend ) $display  .= '<span class="add-on">'.$prepend.'</span>';
		
		$display .='<input class="datepicker-db " type="hidden" name="'.$name.'" value="'.$date.'" />';
		$display .= '<input class="datepicker input-mini" type="text" value="'.$formatedDate.'" />';
		if ($resetBt) {
			$display .= '<span class="btn js-date-reset"><i class="icon icon-remove"></i></span>';
			
		}
		$display .='</span>';
		// If exist exit
		if ($jDate) {
			return $display;
		}
		$front = 'components/com_virtuemart/assets/';

		$document = JFactory::getDocument();
		$document->addScriptDeclaration('
//<![CDATA[
			jQuery(document).ready( function($) {
			$(".datepicker").live( "focus", function() {
				$( this ).datepicker({
					changeMonth: true,
					changeYear: true,
					'.$yearRange.'
					dateFormat:"'.$jsDateFormat.'",
					altField: $(this).prev(),
					altFormat: "yy-mm-dd"
				});
			});
			$(".js-date-reset").click(function() {
				$(this).prev("input").val("'.JText::_('COM_VIRTUEMART_NEVER').'").prev("input").val("0");
			});
		});
//]]>
		');
		jHtml::_('jquery.ui');
		// vmJsApi::js ('jquery.ui.core',FALSE,'',TRUE);
		vmJsApi::js ('jquery.ui.datepicker',FALSE,'',TRUE);

		vmJsApi::css ('jquery.ui.all',$front.'css/ui' ) ;
		$lg = JFactory::getLanguage();
		$lang = $lg->getTag();

		$existingLang = array("af","ar","ar-DZ","az","bg","bs","ca","cs","da","de","el","en-AU","en-GB","en-NZ","eo","es","et","eu","fa","fi","fo","fr","fr-CH","gl","he","hr","hu","hy","id","is","it","ja","ko","kz","lt","lv","ml","ms","nl","no","pl","pt","pt-BR","rm","ro","ru","sk","sl","sq","sr","sr-SR","sv","ta","th","tj","tr","uk","vi","zh-CN","zh-HK","zh-TW");
		if (!in_array ($lang, $existingLang)) {
			$lang = substr ($lang, 0, 2);
		}
		elseif (!in_array ($lang, $existingLang)) {
			$lang = "en-GB";
		}
		vmJsApi::js ('jquery.ui.datepicker-'.$lang, $front.'js/i18n','',true ) ;
		$jDate = TRUE;
		return $display;
	}


	/*
	 * Convert formated date;
	 * @ $date the date to convert
	 * @ $format Joomla DATE_FORMAT Key endding eg. 'LC2' for DATE_FORMAT_LC2
	 * @ revert date format for database- TODO ?
	 */

	static function date($date , $format ='LC2', $joomla=FALSE ,$revert=FALSE ){

		if (!strcmp ($date, '0000-00-00 00:00:00')) {
			return JText::_ ('COM_VIRTUEMART_NEVER');
		}
		If ($joomla) {
			$formatedDate = JHTML::_('date', $date, JText::_('DATE_FORMAT_'.$format));
		} else {
			$formatedDate = JHTML::_('date', $date, JText::_('COM_VIRTUEMART_DATE_FORMAT_'.$format));
		}
		return $formatedDate;
	}

}