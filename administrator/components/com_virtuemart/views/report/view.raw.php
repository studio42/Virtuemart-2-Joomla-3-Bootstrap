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

		$this->loadHelper('html');
		$this->loadHelper('currencydisplay');
		$this->loadHelper('reportFunctions');

		$model		= VmModel::getModel();
		$this->SetViewTitle('REPORT');

		$myCurrencyDisplay = CurrencyDisplay::getInstance();

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
		} else $totalReport = null;
		$this->report = $revenueBasic;
		$this->totalReport = $totalReport;

		$this->lists['select_date'] = $model->renderDateSelectList();
		$this->lists['state_list'] = $model->renderOrderstatesList();
		$this->lists['intervals'] = $model->renderIntervalsList();
		$this->from_period = $model->from_period;
		$this->until_period = $model->until_period;
		$this->pagination = $model->getPagination();

		parent::display('results');
		echo $this->AjaxScripts();
	}
}
