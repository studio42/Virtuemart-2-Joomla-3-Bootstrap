<?php
defined('JPATH_BASE') or die;

if (!class_exists('VmConfig'))
require(JPATH_ROOT.'/administrator/components/com_virtuemart/helpers/config.php');
if (!class_exists('ShopFunctions'))
require(JPATH_VM_ADMINISTRATOR.'/helpers/shopfunctions.php');

if (!class_exists('TableCategories'))
require(JPATH_VM_ADMINISTRATOR.'/tables/categories.php');

jimport('joomla.form.formfield');
/**
 * Supports a modal product picker.
 *
 *
 */
class JFormFieldProduct extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @author      Valerie Cartan Isaksen, Patrick Kohl
	 * @var		string
	 *
	 */
	protected $type = 'product';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */


	function getInput() {

		$key = ($this->element['key_field'] ? $this->element['key_field'] : 'value');
		$val = ($this->element['value_field'] ? $this->element['value_field'] : $this->name);

		return JHTML::_('select.genericlist',  $this->_getProducts(), $this->name, 'class="inputbox"   ', 'value', 'text', $this->value, $this->id);
	}
	private function _getProducts() {
		if (!class_exists('VmModel'))
		require(JPATH_VM_ADMINISTRATOR.'/helpers/vmmodel.php');
		$productModel = VmModel::getModel('Product');
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