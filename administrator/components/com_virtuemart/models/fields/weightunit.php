<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

if (!class_exists('VmConfig'))
    require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');
if (!class_exists('ShopFunctions'))
    require(JPATH_VM_ADMINISTRATOR.'/helpers/shopfunctions.php');

/**
 * Return the Weight Units list.
 *
 *
 */
class JFormFieldWeightUnit extends JFormFieldList
{

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'WeightUnit';

	/**
	 * Method to get the list of Weight Unit for the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$options = array();
		$values = ShopFunctions::getWeightUnit();
		foreach ($values as $k => $v) {
			$options[] = JHtml::_('select.option', $k, $v);
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
