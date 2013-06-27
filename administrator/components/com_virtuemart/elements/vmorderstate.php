<?php
defined ('_JEXEC') or die();
/**
 *
 * @package    VirtueMart
 * @subpackage Plugins  - Elements
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id:$
 */
/*
 * This class is used by VirtueMart Payment or Shipment Plugins
 * which uses JParameter
 * So It should be an extension of JElement
 * Those plugins cannot be configured througth the Plugin Manager anyway.
 */
class JElementVmOrderState extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'OrderStates';

	function fetchElement ($name, $value, &$node, $control_name) {

		$db = JFactory::getDBO ();

		$query = 'SELECT `order_status_code` AS value, `order_status_name` AS text
                 FROM `#__virtuemart_orderstates`
                 WHERE `virtuemart_vendor_id` = 1
                 ORDER BY `ordering` ASC ';

		$db->setQuery ($query);
		$fields = $db->loadObjectList ();
		$class = '';
		foreach ($fields as $field) {
			$field->text= JText::_ ($field->text);
		}

		return JHTML::_ ('select.genericlist', $fields, $control_name . '[' . $name . ']', $class, 'value', 'text', $value, $control_name . $name);
	}

}