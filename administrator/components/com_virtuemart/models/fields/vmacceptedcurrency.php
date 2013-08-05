<?php
defined('JPATH_PLATFORM') or die;

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
    require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');
/*
 * This class is used by VirtueMart Payment or Shipment Plugins
 * which uses JParameter
 * So It should be an extension of JFormField
 * Those plugins cannot be configured througth the Plugin Manager anyway.
 */
 JFormHelper::loadFieldClass('list');

class JFormFieldVmAcceptedCurrency extends JFormFieldList {

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'vmacceptedcurrency';

	protected function getOptions()
	{ 
		$options = array();
		if (!class_exists('VirtueMartModelVendor'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'vendor.php');
		$vendorId = 1;//VirtueMartModelVendor::getLoggedVendor();
		$db = JFactory::getDBO();

		$q = 'SELECT `vendor_accepted_currencies`, `vendor_currency` FROM `#__virtuemart_vendors` WHERE `virtuemart_vendor_id`=' . $vendorId;
		$db->setQuery($q);
		$vendor_currency = $db->loadAssoc();

		if (!$vendor_currency['vendor_accepted_currencies']) {
			$vendor_currency['vendor_accepted_currencies'] = $vendor_currency['vendor_currency'];
		}
		$q = 'SELECT `virtuemart_currency_id` AS value ,CONCAT_WS(" ",`currency_name`,`currency_symbol`) as text FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id` IN (' . $vendor_currency['vendor_accepted_currencies'] . ') and (`virtuemart_vendor_id` = "' . $vendorId . '" OR `shared`="1") AND published = "1" ORDER BY `ordering`,`currency_name`';
		$db->setQuery($q);
		$values = $db->loadObjectList();

		$options[] = JHtml::_('select.option', 0 , JText::_('COM_VIRTUEMART_DEFAULT_VENDOR_CURRENCY'));
		foreach ($values as $v) {
			$options[] = JHtml::_('select.option', $v->value, $v->text);
		}

		return $options;
	}

}