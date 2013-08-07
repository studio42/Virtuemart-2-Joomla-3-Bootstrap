<?php

/**
 *
 * View for the shopping cart
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2013 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 6292 2012-07-20 12:27:44Z alatak $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView'))require(JPATH_VM_SITE.DS.'helpers'.DS.'vmview.php');

/**
 * View for the shopping cart
 * @package VirtueMart
 * @author Max Milbers
 */
class VirtueMartViewCart extends VmView {

	public function display($tpl = null) {

		$document = JFactory::getDocument();

		$layoutName = $this->getLayout();
		if (!$layoutName) $layoutName = JRequest::getWord('layout', 'default');
		$this->assignRef('layoutName', $layoutName);
		$format = JRequest::getWord('format');

		if (!class_exists('VirtueMartCart'))
		require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
		$cart = VirtueMartCart::getCart();
		$this->assignRef('cart', $cart);

    	$this->prepareContinueLink();
		shopFunctionsF::setVmTemplate($this, 0, 0, $layoutName);

		parent::display($tpl);
	}

	private function prepareContinueLink() {
		// Get a continue link
		$menuid = JRequest::getVar('Itemid','');
		if(!empty($menuid)){
			$menuid = '&Itemid='.$menuid;
			echo '$menuid';
		}

		$virtuemart_category_id = shopFunctionsF::getLastVisitedCategoryId();
		$categoryLink = '';
		if ($virtuemart_category_id) {
			$categoryLink = '&virtuemart_category_id=' . $virtuemart_category_id;
		}
		$continue_link = JRoute::_('index.php?option=com_virtuemart&view=category' . $categoryLink.$menuid, FALSE);

		$continue_link_html = '<a class="continue_link" href="' . $continue_link . '" ><span>' . JText::_('COM_VIRTUEMART_CONTINUE_SHOPPING') . '</span></a>';
		$this->assignRef('continue_link_html', $continue_link_html);
		$this->assignRef('continue_link', $continue_link);

		$cart_link = JRoute::_('index.php?option=com_virtuemart&view=cart'.$menuid, FALSE);
		$this->assignRef('cart_link', $cart_link);

	}


}

//no closing tag
