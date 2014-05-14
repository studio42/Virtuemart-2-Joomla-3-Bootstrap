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
    require(JPATH_ROOT.'/administrator/components/com_virtuemart/helpers/config.php');

if (!class_exists('ShopFunctions'))
    require(JPATH_VM_ADMINISTRATOR.'/helpers/shopfunctions.php');


/**
 * Return the taxes list.
 *
 *
 */
class JFormFieldVmTaxes extends JFormField {

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type ='vmtaxes';

    function getInput() {


    return ShopFunctions::renderTaxList($this->value,  $this->name, '');

        // $class = 'multiple="true" size="10"';
       // return JHTML::_('select.genericlist', $taxrates, $control_name . '[' . $name . '][]', $class, 'value', 'text', $value, $control_name . $name);
    }

}