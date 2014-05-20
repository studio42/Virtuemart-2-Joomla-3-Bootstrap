<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Utility class for icons.
 *
 * @package     Joomla.Libraries
 * @subpackage  HTML
 * @since       2.5
 */
abstract class JHtmlVm
{
	/**
	 * Method to generate html code for a list of the orderby keys
	 *
	 * @param   array  $buttons  Array of buttons
	 *
	 * @return  string
	 *
	 * @since   2.5
	 */
	public static function orderingOrg($fields)
	{
		if ( !$fields) return;
		$orderByLink = '<div class="orderlistcontainer"><div class="title hidden-phone">' . JText::_ ('COM_VIRTUEMART_ORDERBY') . '</div>';
		if ($fields) {
			$order = JRequest::getWord ('order', 'ASC');
			/* invert order value set*/
			if ($order == 'ASC') {
				$orderTxt = JText::_ ('COM_VIRTUEMART_SEARCH_ORDER_DESC');
			}
			else {
				$orderTxt = JText::_ ('COM_VIRTUEMART_SEARCH_ORDER_ASC');
			}
			$field = array_shift($fields);
			$text = JText::_ ('COM_VIRTUEMART_' . $field['text']);
			$orderByLink .='<div class="activeOrder"><a title="' . $text . '" href="' . $field['link'] . '">' . $text .' '.$orderTxt   . '</a></div>';
		}
		if ($fields) {
			$orderByLink .= '<div class="orderlist">';
			foreach ($fields as $field){
				$text = JText::_ ('COM_VIRTUEMART_' . $field['text']);
				$orderByLink .= '<div><a title="' . $text . '" href="' . $field['link'] . '">' . $text . '</a></div>';
			}
			$orderByLink .= '</div>';
		}
		$orderByLink .= '</div>';
		return $orderByLink;
	}

	/**
	 * Method to generate html code for a list of the orderby keys
	 *
	 * @param   array  $buttons  Array of buttons
	 *
	 * @return  string
	 *
	 * @since   2.5
	 */
	public static function ordering($fields)
	{
		$count = count($fields);
		if ( !$count) return;

		/* invert order value set*/
		$order = JRequest::getWord ('order', 'ASC');
		if ($order == 'ASC') $orderTxt = JText::_ ('COM_VIRTUEMART_SEARCH_ORDER_DESC');
		else $orderTxt = JText::_ ('COM_VIRTUEMART_SEARCH_ORDER_ASC');

		$field = array_shift($fields);
		$text = JText::_ ('COM_VIRTUEMART_' . $field['text']);
		$orderByLink ='<a class="btn btn-mini" title="' . $text . '" href="' . $field['link'] . '">' . $text .' '.$orderTxt   . '</a>';
		if ( $count >1) {
			$orderByLink = '<div class="btn-group pull-left">'
				.$orderByLink.
				'<a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#"><span style="margin-bottom:0px;" class="caret"></span> </a>
					<ul class="dropdown-menu">';
						foreach ($fields as $field){
							$text = JText::_ ('COM_VIRTUEMART_' . $field['text']);
							$orderByLink .= '<li><a title="' . $text . '" href="' . $field['link'] . '">' . $text . '</a></li>';
						}
			$orderByLink .= '</ul>
				</div>';
		}
		return $orderByLink;
	}

	/**
	 * Method to generate html code for a list of manufacturers
	 *
	 * @param   array  $button  Button properties
	 *
	 * @return  string
	 *
	 * @since   2.5
	 */
	public static function manufacturers($fields)
	{
		$count = count($fields);
		if (!$count) return;
		$field = array_shift($fields);
		if ($count >1) {
			$manufacturerLink = '<div class="btn-group pull-left">
				<a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#" >' . $field['text']  . ' <span style="margin-bottom:0px;" class="caret"></span></a>
					<ul class="dropdown-menu">';
					foreach ($fields as $field){
						$manufacturerLink .= '<li><a title="' . $field['text'] . '" href="' . $field['link'] . '">' . $field['text'] . '</a></li>';
					}
			$manufacturerLink .= '</ul>
				</div>';
		} else $manufacturerLink ='<a class="btn btn-mini" href="#" >' . $field['text']  . '</a>';
		
		return $manufacturerLink;
		// Instantiate a new JLayoutFile instance and render the layout
		// $layout = new JLayoutFile('joomla.quickicons.icon');
		// return $layout->render($button);
	}

	/**
	 * Method to generate html code for a list of manufacturers
	 *
	 * @param   array  $button  Button properties
	 *
	 * @return  string
	 *
	 * @since   2.5
	 */
	public static function manufacturersOld($fields)
	{
		$manufacturerLink = '';
		$count = count($fields);
		if ($count ===1) {
			$field = array_shift($fields);
			$manufacturerLink .='<div class="orderlistcontainer"><div class="title">' . $field['text'] . '</div></div>';
		} elseif ($count) {
			$manufacturerLink = '<div class="orderlistcontainer">';
			$field = array_shift($fields);
			$manufacturerLink .='<div class="activeOrder">' . $field['text'] . '</div>';
			if ($fields) {
				$manufacturerLink .= '<div class="orderlist">';
				foreach ($fields as $field){
					$manufacturerLink .= '<div><a title="' . $field['text'] . '" href="' . $field['link'] . '">' . $field['text'] . '</a></div>';
				}
				$manufacturerLink .= '</div>';
			}
			$manufacturerLink .= '</div>';
		}
		return $manufacturerLink;
		// Instantiate a new JLayoutFile instance and render the layout
		// $layout = new JLayoutFile('joomla.quickicons.icon');
		// return $layout->render($button);
	}
}
