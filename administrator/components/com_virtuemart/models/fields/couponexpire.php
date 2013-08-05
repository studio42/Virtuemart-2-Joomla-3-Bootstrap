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

/**
 * Supports an HTML select list of files
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldcouponExpire extends JFormFieldList
{

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'couponExpire';

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
		$_defaultExpTime = array(
			 '1,D' => '1 '.JText::_('COM_VIRTUEMART_DAY')
			,'1,W' => '1 '.JText::_('COM_VIRTUEMART_WEEK')
			,'2,W' => '2 '.JText::_('COM_VIRTUEMART_WEEK_S')
			,'1,M' => '1 '.JText::_('COM_VIRTUEMART_MONTH')
			,'3,M' => '3 '.JText::_('COM_VIRTUEMART_MONTH_S')
			,'6,M' => '6 '.JText::_('COM_VIRTUEMART_MONTH_S')
			,'1,Y' => '1 '.JText::_('COM_VIRTUEMART_YEAR')
		);
		foreach ($_defaultExpTime as $k => $v) {
			$options[] = JHtml::_('select.option', $k, $v);
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
