<?php
if( !defined( '_JEXEC' ) ) die('Restricted access');

/**
*
* @version $Id: view.html.php 6489 2012-10-01 23:17:36Z Milbo $
* @package VirtueMart
* @subpackage Report
* @copyright Copyright (C) VirtueMart Team - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.org
*/

if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');

/**
 * Report View class
 *
 * @package	VirtueMart
 * @subpackage Report
 * @author Wicksj
 */
class VirtuemartViewReport extends VmView {

	/**
	 * Render the view
	 */
	function display($tpl = null){

		// Load the helper(s)

		$this->loadHelper('html');
		$this->loadHelper('currencydisplay');

		$model		= VmModel::getModel();

		JRequest::setvar('task','');

		$this->SetViewTitle('REPORT');

		$myCurrencyDisplay = CurrencyDisplay::getInstance();

		//update order items button
		$q = 'SELECT * FROM #__virtuemart_order_items WHERE `product_discountedPriceWithoutTax` IS NULL ';
		$db = JFactory::getDBO();
		$db->setQuery($q);
		$res = $db->loadRow();
		if($res) {
			JToolBarHelper::custom('updateOrderItems', 'new', 'new', JText::_('COM_VIRTUEMART_REPORT_UPDATEORDERITEMS'),false);
			vmError('COM_VIRTUEMART_REPORT_UPDATEORDERITEMS_WARN');
		}

		$this->addStandardDefaultViewLists($model);
		$revenueBasic = $model->getRevenue();

		if($revenueBasic){
			$totalReport = array();
			$totalReport['revenueTotal_brutto']= $totalReport['revenueTotal_netto']= $totalReport['number_of_ordersTotal'] = $totalReport['itemsSoldTotal'] = 0 ;
			foreach($revenueBasic as &$j){
				vmdebug('VirtuemartViewReport revenue',$j);
				$totalReport['revenueTotal_netto'] += $j['order_subtotal_netto'];
				$totalReport['revenueTotal_brutto'] += $j['order_subtotal_brutto'];
				$totalReport['number_of_ordersTotal'] += $j['count_order_id'];
				$j['order_subtotal_netto'] = $myCurrencyDisplay->priceDisplay($j['order_subtotal_netto']);
				$j['order_subtotal_brutto'] = $myCurrencyDisplay->priceDisplay($j['order_subtotal_brutto']);
				//$j['product_quantity'] = $model->getItemsByRevenue($j);
				$totalReport['itemsSoldTotal'] +=$j['product_quantity'];
			}
			$totalReport['revenueTotal_netto'] = $myCurrencyDisplay->priceDisplay($totalReport['revenueTotal_netto']);
			$totalReport['revenueTotal_brutto'] = $myCurrencyDisplay->priceDisplay($totalReport['revenueTotal_brutto']);
			// if ( 'product_quantity'==JRequest::getWord('filter_order')) {
				// foreach ($revenueBasic as $key => $row) {
					// $created_on[] =$row['created_on'];
					// $intervals[] =$row['intervals'];
					// $itemsSold[] =$row['product_quantity'];
					// $number_of_orders[] =$row['count_order_id'];
					// $revenue[] =$row['revenue'];

				// }
				// if (JRequest::getWord('filter_order_Dir') == 'desc') array_multisort($itemsSold, SORT_DESC,$revenueBasic);
				// else array_multisort($itemsSold, SORT_ASC,$revenueBasic);
			// }
		} else  $totalReport = null;
		$this->report = $revenueBasic;
		$this->totalReport = $totalReport;

		//$itemsSold = $model->getItemsSold($revenueBasic);
		//$this->itemsSold', $itemsSold);
		// I tihnk is to use in a different layout such as product solds
		// PATRICK K.
		// $productList = $model->getOrderItems();
		// $this->productList', $productList);

		$orderstatusM =VmModel::getModel('orderstatus');
		$this->lists['select_date'] = $model->renderDateSelectList();
		$orderstates = JRequest::getVar ('order_status_code', array('C','S'));
		$this->lists['state_list'] = $orderstatusM->renderOSList($orderstates,'order_status_code',TRUE);
		$this->lists['intervals'] = $model->renderIntervalsList();
		$this->from_period = $model->from_period;
		$this->until_period = $model->until_period;
		$this->pagination = $model->getPagination();

		parent::display($tpl);
	}
}
