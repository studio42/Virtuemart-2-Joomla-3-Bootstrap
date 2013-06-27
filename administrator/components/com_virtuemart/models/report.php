<?php
if (!defined ('_JEXEC')) {
	die('Direct Access to ' . basename (__FILE__) . ' is not allowed.');
}

/**
 *
 * Report Model
 *
 * @version $Id: report.php 6489 2012-10-01 23:17:36Z Milbo $
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

if (!class_exists ('VmModel')) {
	require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmmodel.php');
}

/**
 * Report Model
 * TODO nothing is displayed
 *
 * @package    VirtueMart
 * @subpackage Report
 * @author Wicksj
 */

class VirtuemartModelReport extends VmModel {

	var $from_period = '';
	var $until_period = '';
	private $date_presets = NULL;
	//private $tzoffset = NULL;
	private $period = NULL;

	function __construct () {

		parent::__construct ();
		$this->setMainTable ('orders');

		$this->setDatePresets ();

		$app = JFactory::getApplication ();
		$this->period = $app->getUserStateFromRequest ('com_virtuemart.revenue.period', 'period', 'last30', 'string');

		//$post = JRequest::get ('post');
		//vmdebug ('$post ', $post);
		if (empty($this->period) or $this->period != 'none') {
			$this->setPeriodByPreset ();
		}
		else {
			$this->setPeriod ();
		}

		$this->removevalidOrderingFieldName ('virtuemart_order_id');
		$this->addvalidOrderingFieldName (array('product_quantity', 'o.virtuemart_order_id'));
		$this->_selectedOrdering = 'created_on';

	}


	function correctTimeOffset(&$inputDate){

		$config = JFactory::getConfig();
		$this->siteOffset = $config->getValue('config.offset');

		$date = new JDate($inputDate);

		$date->setTimezone($this->siteTimezone);
		$inputDate = $date->format('Y-m-d H:i:s',true);
	}

	/*
	* Set Start & end Date
	*/
	function  setPeriod () {

		$this->from_period = JRequest::getVar ('from_period', $this->date_presets['last30']['from']);
		$this->until_period = JRequest::getVar ('until_period', $this->date_presets['last30']['until']);

		$config = JFactory::getConfig();
		$siteOffset = $config->getValue('config.offset');
		$this->siteTimezone = new DateTimeZone($siteOffset);

		$this->correctTimeOffset($this->from_period);
		$this->correctTimeOffset($this->until_period);

	}

	/*
	* Set Start & end Date if Var peroid
	*/
	function  setPeriodByPreset () {

		$this->from_period = $this->date_presets[$this->period]['from'];
		$this->until_period = $this->date_presets[$this->period]['until'];

		$config = JFactory::getConfig();
		$siteOffset = $config->getValue('config.offset');
		$this->siteTimezone = new DateTimeZone($siteOffset);

		$this->correctTimeOffset($this->from_period);
		$this->correctTimeOffset($this->until_period);
	}

	function  getItemsByRevenue ($revenue) {

		$q = 'select SUM(`product_quantity`) as product_quantity from `#__virtuemart_order_items` as i LEFT JOIN #__virtuemart_orders as o ON o.virtuemart_order_id=i.virtuemart_order_id ' . $this->whereItem . ' CAST(' . $this->intervals . ' AS DATE) = CAST("' . $revenue['intervals'] . '" AS DATE) ';
		$this->_db->setQuery ($q);
		//echo $this->_db->_sql;
		return $this->_db->loadResult ();

	}

	function getRevenueSortListOrderQuery ($sold = FALSE, $items = FALSE) {

		$selectFields = array();
		$mainTable = '';
		$joinTables = array();
		$joinedTables = '';
		$where = array();

		// group always by intervals (day,week, ... or ID) and set grouping and defaut ordering

		$intervals = JRequest::getWord ('intervals', 'day');
		switch ($intervals) {

			case 'day':
				$this->intervals = 'DATE( o.created_on )';
				break;
			case 'week':
				$this->intervals = 'WEEK( o.created_on )';
				break;
			case 'month':
				$this->intervals = 'MONTH( o.created_on )';
				break;
			case 'year':
				$this->intervals = 'YEAR( o.created_on )';
				break;
			default:
				// invidual grouping
				$this->intervals = 'o.created_on';
				break;
		}
// 		if(!empty($this->intervals)){
// 			$orderBy = $this->_getOrdering('o.`created_on`');
// 		}
		$selectFields['intervals'] = $this->intervals . ' AS intervals, CAST( o.`created_on` AS DATE ) AS created_on';
		vmdebug('getRevenueSortListOrderQuery '.$intervals);
		if($intervals=='product_s'){

			$selectFields[] = '`order_item_name`';
			$selectFields[] = '`virtuemart_product_id`';
			$groupBy = 'GROUP BY `virtuemart_product_id` ';
		} else {
			$groupBy = 'GROUP BY intervals ';
		}


		//$selectFields[] = 'COUNT(virtuemart_order_id) as number_of_orders';
		//with tax => brutto
		//$selectFields[] = 'SUM(product_subtotal_with_tax) as order_total';

		//without tax => netto
		//$selectFields[] = 'SUM(product_item_price) as order_subtotal';
		$selectFields[] = 'SUM(product_item_price * product_quantity) as order_subtotal_netto';
		$selectFields[] = 'SUM(product_subtotal_with_tax) as order_subtotal_brutto';

		$this->dates = ' DATE( o.created_on ) BETWEEN "' . $this->from_period . '" AND "' . $this->until_period . '" ';

		$statusList = array();
		// Filter by statut
		if ($orderstates = JRequest::getVar ('order_status_code', NULL)) {
			$query = 'SELECT `order_status_code`
				FROM `#__virtuemart_orderstates`
				WHERE published=1 ';
			$this->_db->setQuery ($query);
			$list = $this->_db->loadResultArray ();
			foreach ($orderstates as $val) {
				if (in_array ($val, $list)) {
					$statusList[] = '`i`.`order_status` = "' . $val . '"';
				}
			}
			if ($statusList) {
				$where[] = '(' . implode (' OR ', $statusList) . ')';
			}
		}
		//getRevenue
		// select wich table to order sum ordered
		$filterorders = JRequest::getvar ('filter_order', 'intervals');
		$orderdir = (JRequest::getWord ('filter_order_Dir', NULL) == 'desc') ? 'desc' : '';

		switch ($filterorders) {

			case 'o.virtuemart_order_id':
				$orderBy = ' ORDER BY count_order_id ' . $orderdir;
				$groupBy = 'GROUP BY intervals ';
				break;
			case 'product_quantity'   :
				// GROUP BY product_quantity, intervals
				// ORDER BY `product_quantity` ASC
				// TODO grouping and ordering
				$orderBy = ' ORDER BY product_quantity ' . $orderdir;
				$groupBy = 'GROUP BY intervals ';

				//$selectFields['intervals'] = $this->intervals.' AS intervals, i.`created_on` ';
				break;
			case 'o.order_subtotal'   :
				$orderBy = ' ORDER BY order_subtotal';
				break;
				//getOrderItemsSumGrouped($this->intervals , $filterorders);
				break;
			default:
				// invidual grouping
				$orderBy = $this->_getOrdering ();
				vmdebug ('default case', $orderBy);
				//$this->intervals= '`o`.`created_on`';
// 				$orderBy = ' ORDER BY '.$filterorders.' '.$orderdir;
				break;
		}

		$selectFields[] = 'COUNT(DISTINCT o.virtuemart_order_id) as count_order_id';
		$selectFields[] = 'SUM(product_quantity) as product_quantity';

		$mainTable = '`#__virtuemart_order_items` as i';

		$joinTables['orders'] = ' LEFT JOIN `#__virtuemart_orders` as o ON o.virtuemart_order_id=i.virtuemart_order_id ';

		if (count ($selectFields) > 0) {

			$select = implode (', ', $selectFields) . ' FROM ' . $mainTable;
			//$selectFindRows = 'SELECT COUNT(*) FROM '.$mainTable;
			if (count ($joinTables) > 0) {
				foreach ($joinTables as $table) {
					$joinedTables .= $table;
				}
			}

		}
		else {
			vmError ('No select fields given in getRevenueSortListOrderQuery', 'No select fields given');
			return FALSE;
		}

		$virtuemart_product_id = JRequest::getInt ('virtuemart_product_id', FALSE);
		if ($virtuemart_product_id) {
			$where[] = 'i.virtuemart_product_id = "' . $virtuemart_product_id . '" ';
		}

		if (VmConfig::get ('multix', 'none') != 'none') {

			$vendorId = JRequest::getInt ('virtuemart_vendor_id', 0);
			if ($vendorId != 0) {
				$where[] = 'i.virtuemart_vendor_id = "' . $vendorId . '" ';
			}
		}
		if (count ($where) > 0) {
			$this->whereItem = ' WHERE ' . implode (' AND ', $where) . ' AND ';
		}
		else {
			$this->whereItem = ' WHERE ';
		}

// 		$this->whereItem;
		/* WHERE differences with orders and items from orders are only date periods and ordering */
		$whereString = $this->whereItem . $this->dates;

		return $this->exeSortSearchListQuery (1, $select, $joinedTables, $whereString, $groupBy, $orderBy);

	}

	/**
	 * Retrieve a list of report items from the database.
	 *
	 * @author Wicksj
	 * @param string $noLimit True if no record count limit is used, false otherwise
	 * @return object List of order objects

	 */
	function getRevenue ($noLimit = FALSE) {

		return $this->getRevenueSortListOrderQuery ();
	}


	/**
	 * Retrieve a list of report items from the database.
	 * DONT know why this ???? Patrick Kohl
	 *
	 * @author Wicksj
	 * @param string $noLimit True if no record count limit is used, false otherwise
	 * @return object List of order objects
	 */
	function getOrderItems ($noLimit = FALSE) {

		// $db = JFactory::getDBO();

		$query = "SELECT `product_name`, `product_sku`, ";
		$query .= "i.created_on as order_date, ";
		$query .= "SUM(product_quantity) as product_quantity ";
		$query .= "FROM #__virtuemart_order_items i, #__virtuemart_orders o, #__virtuemart_products p ";
		$query .= "WHERE i.created_on BETWEEN '{$this->start_date} 00:00:00' AND '{$this->until_period} 23:59:59' ";
		$query .= "AND o.virtuemart_order_id=i.virtuemart_order_id ";
		$query .= "AND i.virtuemart_product_id=p.virtuemart_product_id ";
		$query .= "GROUP BY product_sku, product_name, order_date ";
		$query .= " ORDER BY order_date, product_name ASC";

		if ($noLimit) {
			$this->_data = $this->_getList ($query);
		}
		else {
			$this->_data = $this->_getList ($query, $this->getState ('limitstart'), $this->getState ('limit'));
		}
		if (!$this->_total) {
			$this->_total = $this->_getListCount ($query);
		}

		return $this->_data;
	}


	public function setDatePresets () {

		if ($this->date_presets) {
			return $this->date_presets;
		}
		// set date presets
		$curDate = JFactory::getDate ();
		$curDate = $curDate->toUnix ();
		$curDate = mktime (0, 0, 0, date ('m', $curDate), date ('d', $curDate), date ('Y', $curDate));
		$monday = (date ('w', $curDate) == 1) ? $curDate : strtotime ('last Monday', $curDate);
		$this->date_presets['last90'] = array(
			'name'  => JText::_ ('COM_VIRTUEMART_REPORT_PERIOD_LAST90'),
			'from'  => date ('Y-m-d', strtotime ('-89 day', $curDate)),
			'until' => date ('Y-m-d', $curDate));
		$this->date_presets['last60'] = array(
			'name'  => JText::_ ('COM_VIRTUEMART_REPORT_PERIOD_LAST60'),
			'from'  => date ('Y-m-d', strtotime ('-59 day', $curDate)),
			'until' => date ('Y-m-d', $curDate));
		$this->date_presets['last30'] = array(
			'name'  => JText::_ ('COM_VIRTUEMART_REPORT_PERIOD_LAST30'),
			'from'  => date ('Y-m-d', strtotime ('-29 day', $curDate)),
			'until' => date ('Y-m-d', $curDate));
		$this->date_presets['today'] = array(
			'name'  => JText::_ ('COM_VIRTUEMART_REPORT_PERIOD_TODAY'),
			'from'  => date ('Y-m-d', $curDate),
			'until' => date ('Y-m-d', $curDate));
		$this->date_presets['this-week'] = array(
			'name'  => JText::_ ('COM_VIRTUEMART_REPORT_PERIOD_THIS_WEEK'),
			'from'  => date ('Y-m-d', $monday),
			'until' => date ('Y-m-d', strtotime ('+6 day', $monday)));
		$this->date_presets['this-month'] = array(
			'name'  => JText::_ ('COM_VIRTUEMART_REPORT_PERIOD_THIS_MONTH'),
			'from'  => date ('Y-m-d', mktime (0, 0, 0, date ('n', $curDate), 1, date ('Y', $curDate))),
			'until' => date ('Y-m-d', mktime (0, 0, 0, date ('n', $curDate) + 1, 0, date ('Y', $curDate))));
		$this->date_presets['this-year'] = array(
			'name'  => JText::_ ('COM_VIRTUEMART_REPORT_PERIOD_THIS_YEAR'),
			'from'  => date ('Y-m-d', mktime (0, 0, 0, 1, 1, date ('Y', $curDate))),
			'until' => date ('Y-m-d', mktime (0, 0, 0, 12, 31, date ('Y', $curDate))));

	}

	public function renderDateSelectList () {

		// simpledate select
		$select = '';
		$options = array(JHTML::_ ('select.option', 'none', '- ' . JText::_ ('COM_VIRTUEMART_REPORT_SET_PERIOD') . ' -', 'text', 'value'));

		$app = JFactory::getApplication ();
		$select = $app->getUserStateFromRequest ('com_virtuemart.revenue.period', 'period', 'last30', 'string');

		foreach ($this->date_presets as $name => $value) {
			$options[] = JHTML::_ ('select.option', $name, JText::_ ($value['name']), 'text', 'value');
		}
		$listHTML = JHTML::_ ('select.genericlist', $options, 'period', 'size="7" class="inputbox" onchange="this.form.submit();" ', 'text', 'value', $select);
		//$listHTML = JHTML::_ ('select.genericlist', $options, 'period', 'size="7" class="inputbox" ', 'text', 'value', $select);

		return $listHTML;
	}

	public function renderOrderstatesList () {

		$orderstates = JRequest::getVar ('order_status_code', 'C');
		//print_r($orderstates);
		$query = 'SELECT `order_status_code` as value, `order_status_name` as text
			FROM `#__virtuemart_orderstates`
			WHERE published=1 ';
		$this->_db->setQuery ($query);
		$list = $this->_db->loadObjectList ();
		//$html = VmHTML::select ('order_status_code[]', $list, $orderstates, 'size="7" class="inputbox" onchange="this.form.submit();" multiple="multiple"');
		$html = VmHTML::select ('order_status_code[]', $list, $orderstates, 'size="7" class="inputbox"  multiple="multiple"');
		return $html;
	}

	public function renderIntervalsList () {

		$intervals = JRequest::getWord ('intervals', 'day');

		$options = array();
		$options[] = JHTML::_ ('select.option', JText::_ ('COM_VIRTUEMART_PRODUCT_S'), 'product_s');
		$options[] = JHTML::_ ('select.option', JText::_ ('COM_VIRTUEMART_ORDERS'), 'orders');
		$options[] = JHTML::_ ('select.option', JText::_ ('COM_VIRTUEMART_REPORT_INTERVAL_GROUP_DAILY'), 'day');
		$options[] = JHTML::_ ('select.option', JText::_ ('COM_VIRTUEMART_REPORT_INTERVAL_GROUP_WEEKLY'), 'week');
		$options[] = JHTML::_ ('select.option', JText::_ ('COM_VIRTUEMART_REPORT_INTERVAL_GROUP_MONTHLY'), 'month');
		$options[] = JHTML::_ ('select.option', JText::_ ('COM_VIRTUEMART_REPORT_INTERVAL_GROUP_YEARLY'), 'year');
		//$listHTML = JHTML::_ ('select.genericlist', $options, 'intervals', 'class="inputbox" onchange="this.form.submit();" size="5"', 'text', 'value', $intervals);
		$listHTML = JHTML::_ ('select.genericlist', $options, 'intervals', 'class="inputbox" size="6"', 'text', 'value', $intervals);
		return $listHTML;
	}
}
