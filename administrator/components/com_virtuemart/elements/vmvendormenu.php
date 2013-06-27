<?php
defined('_JEXEC') or die();
/**
 *
 * @package	VirtueMart
 * @subpackage Plugins  - Elements
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
if (!class_exists('VmConfig'))
    require(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'config.php');
if (!class_exists('ShopFunctions'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
if (!class_exists('TableCategories'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . 'categories.php');

if (!class_exists('VmElements'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'elements' . DS . 'vmelements.php');
/*
 * This element is used by the menu manager
 * Should be that way
 */

class JElementVmvendormenu extends JElement {

    var $_name = 'vendormenu';

    function fetchElement($name, $value, &$node, $control_name) {
	    $lang = JFactory::getLanguage();
	    $lang->load('com_virtuemart',JPATH_ADMINISTRATOR);
	$model = VmModel::getModel('Vendor');
	$vendors = $model->getVendors(true, true, false);
	return JHTML::_('select.genericlist', $vendors, $control_name . '[' . $name . ']', '', $name, 'vendor_name', $value, $control_name . $name);
    }

}
