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
		if ($this->document->_type == 'pdf') return;
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
	function can($task,$view,$created_by = null,$vendorId = null){
		static $user_id = null;
		static $isAdmin = null;
		static $vendor = null;
		if ($vendor === null) {
			if (!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
			$vendor = Permissions::getInstance()->isSuperVendor();
			if ( $user_id = JFactory::getUser()->get('id') ) {
				$isAdmin = Permissions::getInstance()->check("admin,storeadmin");
				if(!class_exists('ShopFunctions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'shopfunctions.php'); 
			}
		}
		if (!$vendor) return '';

		if ( $vendor > 1) {

			if (!ShopFunctions::can($task,$view) )  return false;
			// only link to own entries or same vendor id
			if ($vendorId) {
				if( $vendorId != $vendor) return false;
			}
			elseif ($created_by != $user_id) return false;
		}
		return true;
	}
	// display edit link if the user have the rights.
	function newLink($view,$idLink,$created_by,$task="add",$vendorId = null) {

		if($idLink === null) return;
		if (!$this->can($task,$view,$created_by,$vendorId)) return '';
	    $edit_link = 'index.php?option=com_virtuemart&tmpl=component&view='.$view.'&task='.$task.$idLink ;
		return '<a title="'. JText::_('JNEW').' '.$view.'" href="'.JRoute::_($edit_link, FALSE).'" class="btn btn-default pull-right"><span class="icon icon-new"> </span> '.$view.'</a>';		
	}	// display edit link if the user have the rights.
	function editLink($view,$id,$created_by,$task="edit",$vendorId = null) {

		if(!$id) return;
		if (!$this->can($task,$view,$created_by,$vendorId)) return '';
	    $edit_link = 'index.php?option=com_virtuemart&tmpl=component&view='.$view.'&task='.$task.'&virtuemart_'.$view.'_id='.$id ;
		return '<a title="'. JText::_('JGLOBAL_EDIT').'" href="'.JRoute::_($edit_link, FALSE).'" class="btn btn-default pull-right"><span class="icon icon-edit"> </span></a>';		
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