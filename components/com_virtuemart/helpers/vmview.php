<?php
defined('_JEXEC') or die('');
/**
 * abstract controller class containing get,store,delete,publish and pagination
 *
 *
 * This class provides the functions for the calculatoins
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

class VmView extends JViewLegacy{

	function linkIcon($link,$altText ='',$boutonName,$verifyConfigValue=false, $modal = true, $use_icon=true,$use_text=false){
		if ($verifyConfigValue) {
			if ( !VmConfig::get($verifyConfigValue, 0) ) return '';
		}
		$folder = '/media/system/images/'; // use of relative path, $folder is not needed in j3
		$text='';
		if ( $use_icon ) $text .= JHtml::_('image', $boutonName.'.png', JText::_($altText),  null, true);
		if ( $use_text ) $text .= '&nbsp;'. JText::_($altText);
		if ( $text=='' )  $text .= '&nbsp;'. JText::_($altText);
		if ($modal) return '<a class="modal" rel="{handler: \'iframe\', size: {x: 700, y: 550}}" title="'. JText::_($altText).'" href="'.JRoute::_($link).'">'.$text.'</a>';
		else 		return '<a title="'. JText::_($altText).'" href="'.JRoute::_($link, FALSE).'">'.$text.'</a>';
	}

	public function escape($var)
	{
		if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities')))
		{
			$result = call_user_func($this->_escape, $var, ENT_COMPAT, $this->_charset);
		} else {
			$result =  call_user_func($this->_escape, $var);
		}

		return $result;
	}
}