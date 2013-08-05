<?php
if( !defined( '_JEXEC' ) ) die();

/**
*
* @version $Id: default.php 6489 2012-10-01 23:17:36Z Milbo $
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
// NOTE : TODO "order_subtotal_netto" and  order_subtotal_brutto ordering do not exist in model !!
/* Load some variables */
$intervalTitle = JRequest::getVar('intervals','day');
if ( ($intervalTitle =='week') or ($intervalTitle =='month') ) $addDateInfo = true ;
else $addDateInfo = false;

?>

<h2><?php echo JText::sprintf('COM_VIRTUEMART_REPORT_TITLE', vmJsApi::date( $this->from_period, 'LC',true) , vmJsApi::date( $this->until_period, 'LC',true) ); ?></h2>
<div id="resultscounter">
	<?php echo $this->pagination->getResultsCounter();?>
</div>
<table class="table table-striped">
		<thead>
			<tr>
				<th>
					<?php echo $this->sort('created_on', 'COM_VIRTUEMART_'.$intervalTitle); ?>
				</th>
				<th>
					<?php echo $this->sort('o.virtuemart_order_id', 'COM_VIRTUEMART_REPORT_BASIC_ORDERS') ; ?>
				</th>
				<th>
					<?php echo $this->sort('product_quantity', 'COM_VIRTUEMART_REPORT_BASIC_TOTAL_ITEMS') ; ?>
				</th>
				<th>
					<?php echo $this->sort('order_subtotal_netto', 'COM_VIRTUEMART_REPORT_BASIC_REVENUE_NETTO') ; ?>
				</th>
				<th>
					<?php echo $this->sort('order_subtotal_brutto', 'COM_VIRTUEMART_REPORT_BASIC_REVENUE_BRUTTO') ; ?>
				</th>
			<?php
				$intervals = JRequest::getWord ('intervals', 'day');
				if($intervals=='product_s'){
			?>
				<th>
					<?php echo $this->sort('order_item_name', 'COM_VIRTUEMART_PRODUCT_NAME') ; ?>
				</th>
				<th>
					<?php echo $this->sort('virtuemart_product_id', 'COM_VIRTUEMART_PRODUCT_ID') ; ?>
				</th>
			<?php
				}
			?>
			</tr>
		</thead>
		<tbody>
		<?php
		$rows = count( $this->report );
		for ($j =0; $j < $rows; ++$j ){
			$r = $this->report[$j];
			?>
			<tr>
				<td align="center">
					<?php echo $r['intervals'] ;
					if ( $addDateInfo ) {
						echo ' ('.substr ( $r['created_on'],0,4 ).')';
					}
				 ?>
				</td>
				<td align="center">
					<?php echo $r['count_order_id'];?>
				</td>
				<td align="center">
					<?php echo $r['product_quantity'];?>
				</td>
				<td align="right">
					<?php echo $r['order_subtotal_netto'];?>
				</td>
				<td align="right">
					<?php echo $r['order_subtotal_brutto'];?>
				</td>
				<?php if($intervals=='product_s')
				{ ?>
					<td align="center">
						<?php echo $r['order_item_name'];?>
					</td>
					<td align="center">
						<?php echo $r['virtuemart_product_id'];?>
					</td>
					<?php
				} ?>
			</tr>
			<?php
		} ?>
		</tbody>
		<thead>
			<tr>
				<th  class="right"><?php echo JText::_('COM_VIRTUEMART_TOTAL').' : '; ?></th>
				<th><?php echo $this->totalReport['number_of_ordersTotal']?></th>
				<th><?php echo $this->totalReport['itemsSoldTotal'];?></th>
				<th class="right"><?php echo $this->totalReport['revenueTotal_netto'];?></th>
				<th class="right"><?php echo $this->totalReport['revenueTotal_brutto'];?></th>
				<th colspan="<?php echo $intervals=='product_s' ? "3" : "1" ?>"></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<?php echo $this->addStandardHiddenToForm(); ?>

