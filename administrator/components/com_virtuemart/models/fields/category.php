<?php

/**
 *
 * @package	VirtueMart
 * @subpackage   Models Fields
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: $
 */
defined('JPATH_BASE') or die;
if (!class_exists('VmConfig'))
    require(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'config.php');
if (!class_exists('ShopFunctions'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
if (!class_exists('TableCategories'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . 'categories.php');
jimport('joomla.form.formfield');

/**
 * Supports a modal product picker.
 *
 *
 */
class JFormFieldCategory extends JFormField
{ 
	protected $type = 'category';

	/**
	 * Method to get the field input markup.
	 *
         * @author      Valerie Cartan Isaksen
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
  
     function getInput() {
        $key = ($this->element['key_field'] ? $this->element['key_field'] : 'value');
        $val = ($this->element['value_field'] ? $this->element['value_field'] : $this->name);
	     $lang = JFactory::getLanguage();
	     $lang->load('com_virtuemart',JPATH_ADMINISTRATOR);
	     $categorylist = ShopFunctions::categoryListTree(array($this->value));
        $html = '<select class="inputbox"   name="' . $this->name . '" >';
        $html .= '<option value="0">' . JText::_('COM_VIRTUEMART_CATEGORY_FORM_TOP_LEVEL') . '</option>';
        $html .= $categorylist;
        $html .="</select>";
        return $html;

    }

}