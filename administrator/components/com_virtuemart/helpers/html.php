<?php
/**
 * HTML helper class
 *
 * This class was developed to provide some standard HTML functions.
 *
 * @package	VirtueMart
 * @subpackage Helpers
 * @author RickG
 * @copyright Copyright (c) 2004-2008 Soeren Eberhardt-Biermann, 2009 VirtueMart Team. All rights reserved.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HTML Helper
 *
 * @package VirtueMart
 * @subpackage Helpers
 * @author RickG
 */
class VmHTML{
	/**
	 * Converts all special chars to html entities
	 *
	 * @param string $string
	 * @param string $quote_style
	 * @param boolean $only_special_chars Only Convert Some Special Chars ? ( <, >, &, ... )
	 * @return string
	 */
	static function shopMakeHtmlSafe( $string, $quote_style='ENT_QUOTES', $use_entities=false ) {

		if( defined( $quote_style )) {
			$quote_style = constant($quote_style);
		}
		if( $use_entities ) {
			$string = @htmlentities( $string, constant($quote_style), self::vmGetCharset() );
		} else {
			$string = @htmlspecialchars( $string, $quote_style, self::vmGetCharset() );
		}
		return $string;
	}


	/**
	 * Returns the charset string from the global _ISO constant
	 *
	 * @return string UTF-8 by default
	 * @since 1.0.5
	 */
static function vmGetCharset() {
		$iso = explode( '=', @constant('_ISO') );
		if( !empty( $iso[1] )) {
			return $iso[1];
		}
		else {
			return 'UTF-8';
		}
	}

    /**
     * Generate HTML code for a row using VmHTML function
     * works also with shopfunctions, for example
	 * $html .= VmHTML::row (array('ShopFunctions', 'renderShopperGroupList'),
	 * 			'VMCUSTOM_BUYER_GROUP_SHOPPER', $field->shopper_groups, TRUE, 'custom_param['.$row.'][shopper_groups][]', ' ');
     *
     * @func string  : function to call
     * @label string : Text Label
     * @args array : arguments
     * @return string: HTML code for row table
     */
    static function row($func,$label){
		$VmHTML="VmHTML";
		if (!is_array($func)) {
			$func = array($VmHTML, $func);
		}

		$passedArgs = func_get_args();
		array_shift( $passedArgs );//remove function
		array_shift( $passedArgs );//remove label
			$args = array();
			foreach ($passedArgs as $k => $v) {
			    $args[] = &$passedArgs[$k];
			}
		$lang =JFactory::getLanguage();
		if($lang->hasKey($label.'_TIP')){
			$labelHint = JText::_($label.'_TIP');
			$label = '<span class="hasTooltip" title="'.JText::_($label.'_TIP').'">'.JText::_($label).'</span>' ;
		} //Fallback
		else if($lang->hasKey($label.'_EXPLAIN')){
			$labelHint = JText::_($label.'_EXPLAIN');
			$label = '<span class="hasTooltip" title="'.JText::_($label.'_EXPLAIN').'">'.JText::_($label).'</span>' ;
		} else {
			$label = JText::_($label);
		}

		$html = '
		<tr>
			<td class="key">
				'.$label.'
			</td>
			<td>
				'.call_user_func_array($func, $args).'
			</td>
		</tr>';
		return $html ;
	}
	/* simple value display */
	static function value( $value ){
		$lang =JFactory::getLanguage();
		return $lang->hasKey($value) ? JText::_($value) : $value;
	}

	/* simple raw render
	 * The sense is unclear !
	 * The sense is to us it with vmhtml::row
	 * @param $value
	 * @return mixed
	 */
	static function raw( $value ){
		return $value;
	}
    /**
     * Generate HTML code for a checkbox
     *
     * @param string Name for the checkbox
     * @param mixed Current value of the checkbox
     * @param mixed Value to assign when checkbox is checked
     * @param mixed Value to assign when checkbox is not checked
     * @return string HTML code for checkbox
     */
    static function checkbox($name, $value, $checkedValue=1, $uncheckedValue=0, $extraAttribs = '', $id = null) {
		if (!$id) $id=$name ;
	if ($value == $checkedValue) {
		$active = ' active';
	    $checked = 'checked="checked"';
		$state = "publish";
	}
	else {
		$active = '';
		$state = "unpublish";
	    $checked = '';
	}
	$htmlcode = '<input type="hidden" name="' . $name . '" value="' . $uncheckedValue . '">';
	$htmlcode .= '<label class="btn btn-toggle btn-small'.$active.'"><i class="icon-'.$state.'"></i><input '.$extraAttribs.' id="' . $id . '" type="checkbox" name="' . $name . '" value="' . $checkedValue . '" ' . $checked . ' /></label>';
	return $htmlcode;
    }

	/**
	 * Prints an HTML dropdown box named $name using $arr to
	 * load the drop down.  If $value is in $arr, then $value
	 * will be the selected option in the dropdown.
	 * @author gday
	 * @author soeren
	 *
	 * @param string $name The name of the select element
	 * @param string $value The pre-selected value
	 * @param array $arr The array containing $key and $val
	 * @param int $size The size of the select element
	 * @param string $multiple use "multiple=\"multiple\" to have a multiple choice select list
	 * @param string $extra More attributes when needed
	 * @return string HTML drop-down list
	 */
	static function selectList($name, $value, $arrIn, $size=1, $multiple="", $extra="",$class='') {
		if ($class) $class = ' class="'.$class.'"';
		$html = '';
		if( empty( $arrIn ) ) {
			$arr = array();
		} else {
			if(!is_array($arrIn)){
	        	 $arr=array($arrIn);
	        } else {
	        	 $arr=$arrIn;
	        }
		}


		$html = '<select'.$class.' id="'.$name.'" name="'.$name.'" size="'.$size.'" '.$multiple.' '.$extra.'>';

		while (list($key, $val) = each($arr)) {
//		foreach ($arr as $key=>$val){
			$selected = "";
			if( is_array( $value )) {
				if( in_array( $key, $value )) {
					$selected = 'selected="selected"';
				}
			}
			else {
				if(strtolower($value) == strtolower($key) ) {
					$selected = 'selected="selected"';
				}
			}

			$html .= '<option value="'.$key.'" '.$selected.'>'.self::shopMakeHtmlSafe($val);
			$html .= '</option>';

		}

		$html .= '</select>';

		return $html;
	}


//	/**
//	 *
//	 */
//    function selectListParamParser( $arrIn, $tag_name, $tag_attribs, $key, $text, $selected, $required=0 ) {
////    function selectListParamParser($tag_name ,$tag_attribs ,$arrIn , $key, $text, $selected, $required=0 ) {
//
//        echo '<br />$tag_name '.$tag_name;
//        echo '<br />$tag_attribs '.$tag_attribs;
//        echo '<br />$key '.$key;
//        echo '<br />$text '.$text;
//        echo '<br />$selected '.$selected;
//        if(empty($arrIn)){
//        	 return 'Error selectListParamParser no first argument given';
//        }
//        if(!is_array($arrIn)){
//        	 $arr=array($arrIn);
//        } else {
//        	 $arr=$arrIn;
//        }
//        reset( $arr );
//        $html = "\n<select name=\"$tag_name\" id=\"".str_replace('[]', '', $tag_name)."\" $tag_attribs>";
//        if(!$required) $html .= "\n\t<option value=\"\">".JText::_('COM_VIRTUEMART_SELECT')."</option>";
//        $n=count( $arr );
//        for ($i=0; $i < $n; $i++ ) {
//
//                $k = stripslashes($arr[$i]->$key);
//                $t = stripslashes($arr[$i]->$text);
//                $id = isset($arr[$i]->id) ? $arr[$i]->id : null;
//
//                $extra = '';
//                $extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
//                if (is_array( $selected )) {
//                        foreach ($selected as $obj) {
//                                $k2 = stripslashes($obj->$key);
//                                if ($k == $k2) {
//                                        $extra .= " selected=\"selected\"";
//                                        break;
//                                }
//                        }
//                } else {
//                        $extra .= ($k == stripslashes($selected) ? " selected=\"selected\"" : '');
//                }
//                $html .= "\n\t<option value=\"".$k."\"$extra>";
//				if( $t[0] == '_' ) $t = substr( $t, 1 );
//				$html .= JText::_($t);
//                $html .= "</option>";
//        }
//        $html .= "\n</select>\n";
//        return $html;
//	}

	/**
	 * Creates a Radio Input List
	 *
	 * @param string $name
	 * @param string $value default value
	 * @param string $arr
	 * @param string $extra
	 * @return string
	 */
	static function radioList($name, $value, &$arr, $extra="", $classlbl ="") {
		$html = '';
		if( empty( $arr ) ) {
			$arr = array();
		}
		$html = '';
		$i = 0;
		if ($extra) $extra='class="'.$extra.'"';
		if ($classlbl) $classlbl='class="'.$classlbl.'"';
		foreach($arr as $key => $val) {
			$checked = '';
			if( is_array( $value )) {
				if( in_array( $key, $value )) {
					$checked = 'checked="checked"';
				}
			}
			else {
				if(strtolower($value) == strtolower($key) ) {
					$checked = 'checked="checked"';
				}
			}
			$input = '<input type="radio" name="'.$name.'" id="'.$name.$i.'" value="'.htmlspecialchars($key, ENT_QUOTES).'" '.$checked.' '.$extra." />\n";
			$html .= '<label for="'.$name.$i++.'" '.$classlbl.'>'.$input.$val."</label>\n";
		}

		return $html;
	}
	// enchanced radio list grouped for bootstrap
	static function radioListGroup($name, $value, &$arr) {
		return '<fieldset class="radio btn-group">'.self::radioList($name, $value, &$arr, "", 'radio btn').'<fieldset>';
	}

	/**
	 * Creates radio List
	 * @param array $radios
	 * @param string $name
	 * @param string $default
	 * @return string
	 */
	static function radio( $name, $radios, $default,$key='value',$text='text') {
		return '<fieldset class="radio">'.JHTML::_('select.radiolist', $radios, $name, '', $key, $text, $default).'</fieldset>';
	}
	/**
	 * Creating rows with boolean list
	 *
	 * @author Patrick Kohl
	 * @param string $label
	 * @param string $name
	 * @param string $value
	 *
	 */
	public static function booleanlist (  $name, $value,$class='class="inputbox"'){
		return '<fieldset class="radio btn-group">'.JHTML::_( 'select.booleanlist',  $name , $class , $value).'</fieldset>' ;
	}
		/**
	 * Creating rows with input fields
	 *
	 * @author Patrick Kohl
	 * @param string $text
	 * @param string $name
	 * @param string $value
	 */
	public static function input($name,$value,$class='class="inputbox"',$readonly='',$size='37',$maxlength='255',$more=''){
		if (strpos($more,'add-on')) {
			$before ='<div class="input-append">';
			$more .='</div>';
		} else $before = '';
		return $before.'<input type="text" '.$readonly.' '.$class.' id="'.$name.'" name="'.$name.'" size="'.$size.'" maxlength="'.$maxlength.'" value="'.htmlspecialchars($value).'" />'.$more;
	}

	/**
	 * Creating rows with input fields
	 *
	 * @author Patrick Kohl
	 * @param string $text
	 * @param string $name
	 * @param string $value
	 */
	public static function textarea($name,$value,$class='class="inputbox"',$cols='70',$rows="10"){
		return '<textarea '.$class.' id="'.$name.'" name="'.$name.'" cols="'.$cols.'" rows="'.$rows.'"/>'.$value.'</textarea >';
	}
	/**
	 * render editor code
	 *
	 * @author Patrick Kohl
	 * @param string $text
	 * @param string $name
	 * @param string $value
	 */
	public static function editor($name,$value,$size='100%',$height='300',$hide = array('pagebreak', 'readmore')){
		$editor =JFactory::getEditor();
		return $editor->display($name, $value, $size, $height, null, null ,$hide )  ;
	}

	/**
	 *
	 * @author Patrick Kohl
	 * @param array $options( value & text)
	 * @param string $name option name
	 * @param string $defaut defaut value
	 * @param string $key option value
	 * @param string $text option text
	 * @param boolean $zero add  a '0' value in the option
	 * return a select list
	 */
	public static function select($name, $options, $default = '0',$attrib = "onchange='submit();'",$key ='value' ,$text ='text', $zero=true){
		if ($zero==true) {
		$option  = array($key =>"0", $text => JText::_('COM_VIRTUEMART_LIST_EMPTY_OPTION'));
		$options = array_merge(array($option), $options);
		}
		return JHTML::_('select.genericlist', $options,$name,$attrib,$key,$text,$default,false,true);
	}
	/**
	 * renders the hidden input
	 * @author Max Milbers
	 */
	public static function inputHidden($values){
		$html='';
		foreach($values as $k=>$v){
			$html .= '<input type="hidden" name="'.$k.'" value="'.$v.'" />';
		}
		return $html;
	}

	/**
	* @author Patrick Kohl
	* @var $type type of regular Expression to validate
	* $type can be I integer, F Float, A date, M, time, T text, L link, U url, P phone
	*@bool $required field is required
	*@Int $min minimum of char
	*@Int $max max of char
	*@var $match original ID field to compare with this such as Email, passsword
	*@ Return $html class for validate javascript
	**/
	public static function validate($type='',$required=true, $min=null,$max=null,$match=null) {

		if ($required) $validTxt = 'required';
		else $validTxt = 'optional';
		if (isset($min)) $validTxt .= ',minSize['.$min.']';
		if (isset($max)) $validTxt .= ',maxSize['.$max.']';
		static $validateID=0 ;
		$validateID++;
		if ($type=='S' ) return 'id="validate'.$validateID.'" class="validate[required,minSize[2],maxSize[255]]"';
		$validate = array ( 'I'=>'onlyNumberSp', 'F'=>'number','D'=>'dateTime','A'=>'date','M'=>'time','T'=>'Text','L'=>'link','U'=>'url','P'=>'phone');
		if (isset ($validate[$type])) $validTxt .= ',custom['.$validate[$type].']';
		$html ='id="validate'.$validateID.'" class="validate['.$validTxt.']"';

		return $html ;
	}
	/**
	 * @author Patrick Kohl
	 * converted from Joomla 3.0 featured(bootstrap style)
	 * @param   int $value	The state value
	 * @param   int $i
	 */
	public static function featured($value = 0, $i, $canChange = true)
	{
		JHtml::_('bootstrap.tooltip');

		// Array of image, task, title, action
		$states	= array(
			0	=> array('star-empty',	'toggle.product_special.1',	'COM_VIRTUEMART_DISABLED',	'COM_VIRTUEMART_ENABLE_ITEM'),
			1	=> array('star',		'toggle.product_special.0',	'COM_VIRTUEMART_FEATURED',		'COM_VIRTUEMART_DISABLE_ITEM'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon	= $state[0];
		if ($canChange)
		{
			$html	= '<a href="#" data-task="'.$state[1].'" onclick="return Joomla.featuredJson( this, \'cb'.$i.'\')" class="btn btn-micro hasTooltip' . ($value == 1 ? ' active' : '') . '" title="'.JText::_($state[3]).'"><i class="icon-'
					. $icon.'"></i></a>';
		}
		else
		{
			$html	= '<a class="btn btn-micro hasTooltip disabled' . ($value == 1 ? ' active' : '') . '" title="'.JText::_($state[2]).'"><i class="icon-'
					. $icon.'"></i></a>';
		}

		return $html;
	}

}