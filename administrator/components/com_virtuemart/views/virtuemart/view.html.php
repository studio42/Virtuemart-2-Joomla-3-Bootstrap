<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 5820 2012-04-06 19:14:38Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');
jimport('joomla.html.pane');

/**
 * HTML View class for the VirtueMart Component
 *
 * @package		VirtueMart
 * @author
 */
class VirtuemartViewVirtuemart extends VmView {

	function display($tpl = null) {
		VmConfig::loadJLang('com_virtuemart_orders',TRUE);
		$app = JFactory::getApplication();
		// Load the helper(s)
		if (JFactory::getUser()->authorise('core.admin', 'com_virtuemart')) {
			if($app->isadmin()) JToolBarHelper::preferences('com_virtuemart');
		}
		$this->loadHelper('image');

		$model = VmModel::getModel('virtuemart');

		$this->nbrCustomers = $model->getTotalCustomers();
		$this->nbrActiveProducts = $model->getTotalActiveProducts();
		$this->nbrInActiveProducts = $model->getTotalInActiveProducts();
		$this->nbrFeaturedProducts = $model->getTotalFeaturedProducts();
		$this->ordersByStatus = $model->getTotalOrdersByStatus();

		$recentOrders = $model->getRecentOrders();
			if(!class_exists('CurrencyDisplay'))require(JPATH_VM_ADMINISTRATOR.'/helpers'.DS.'currencydisplay.php');

			/* Apply currency This must be done per order since it's vendor specific */
			$_currencies = array(); // Save the currency data during this loop for performance reasons
			foreach ($recentOrders as $virtuemart_order_id => $order) {

				//This is really interesting for multi-X, but I avoid to support it now already, lets stay it in the code
				if (!array_key_exists('v'.$order->virtuemart_vendor_id, $_currencies)) {
					$_currencies['v'.$order->virtuemart_vendor_id] = CurrencyDisplay::getInstance('',$order->virtuemart_vendor_id);
				}
				$order->order_total = $_currencies['v'.$order->virtuemart_vendor_id]->priceDisplay($order->order_total);
			}
		$this->recentOrders = $recentOrders;
		$this->recentCustomers = $model->getRecentCustomers();
		parent::display($tpl);
	}
	/**
	 * Display an image icon for the given image and create a link to the given link.
	 *
	 * @param string $link Link to use in the href tag
	 * @param string $image Name of the image file to display
	 * @param string $text Text to use for the image alt text and to display under the image.
	 * @param string $route internal links.
	 */
	public function panelButton($link, $imageclass, $text, $route = true) {

		if ($route === true) $link = JROUTE::_('index.php?option=com_virtuemart&view='.$link.$this->tmpl);
		$button = '<a class="span12 hasTooltip" title="' . $text . '" href="' . $link . '">';
		$button .= '<i class="'.$imageclass.'"></i> ';
		$button .=  $text.'</a>';
		echo $button;

	}
}

//pure php no tag