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
 * This element is used by the menu manager for J15
* Should be that way
*/

class JElementVmproductsmenu extends JElement {

	var $_name = 'productsmenu';

	function fetchElement($name, $value, &$node, $control_name) {

		return JHTML::_('select.genericlist', $this->_getProducts(), $control_name . '[' . $name . ']', '', 'value', 'text', $value, $control_name . $name);
	}

	private function _getProducts() {

		$productModel = VmModel::getModel('product');
		$productModel->_noLimit = true;
		$products = $productModel->getProductListing(false, false, false, false, true,false);
		$productModel->_noLimit = false;
		$i = 0;
		$list = array();
		foreach ($products as $product) {
			$list[$i]['value'] = $product->virtuemart_product_id;
			$list[$i]['text'] = $product->product_name. " (". $product->product_sku.")";
			$i++;
		}
		return $list;
	}


}
