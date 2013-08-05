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
    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
/**
 * Supports an HTML select list of files
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldlwhUnit extends JFormFieldList
{

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'lwhUnit';

	/**
	 * Method to get the list of files for the field options.
	 * Specify the target directory with a directory attribute
	 * Attributes allow an exclude mask and stripping of extensions from file name.
	 * Default attribute may optionally be set to null (no file) or -1 (use a default).
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$options = array();
		$values = array(
		 'M' => JText::_ ('COM_VIRTUEMART_UNIT_NAME_M')
		,'CM'  => JText::_ ('COM_VIRTUEMART_UNIT_NAME_CM')
		,'MM'  => JText::_ ('COM_VIRTUEMART_UNIT_NAME_MM')
		,'YD'  => JText::_ ('COM_VIRTUEMART_UNIT_NAME_YARD')
		,'FT'  => JText::_ ('COM_VIRTUEMART_UNIT_NAME_FOOT')
		,'IN'  => JText::_ ('COM_VIRTUEMART_UNIT_NAME_INCH')
		);
		foreach ($values as $k => $v) {
			$options[] = JHtml::_('select.option', $k, $v);
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
