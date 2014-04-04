<?php
/**
*
* Coupon View
*
* @package	VirtueMart
* @subpackage Coupon
* @author RickG
 * @author Valerie Isaksen
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 5601 2012-03-04 18:22:24Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');

/**
 * HTML View class for maintaining the list of Coupons
 *
 * @package	VirtueMart
 * @subpackage Coupon
 * @author RickG
 * @author Valerie Isaksen
 */


class VirtuemartViewCoupon extends VmView {

	function display($tpl = null) {

		// Load the helper(s)


		$this->loadHelper('html');
		$this->loadHelper('currencydisplay');
		$model = VmModel::getModel();
		$vendorModel = VmModel::getModel('Vendor');
		$vendorModel->setId(1);
		$vendor = $vendorModel->getVendor();
		// something was wrong here !!! $currencyModel === $currencyModel
		$currencyModel = VmModel::getModel('Currency');
		$currency = $currencyModel->getCurrency($vendor->vendor_currency);
		$this->vendor_currency = $currency->currency_symbol;

		$this->addStandardDefaultViewCommands();
		$this->addStandardDefaultViewLists($model,0,'ASC');
		$code = JRequest::getWord('search', false);
		$this->coupons = $model->getCoupons($code);
		$this->pagination = $model->getPagination();

		parent::display('results');
		echo $this->AjaxScripts();
	}

}
// pure php no closing tag
