<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Paymentmethod
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 6475 2012-09-21 11:54:21Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
//if($virtuemart_vendor_id==1 || $perm->check( 'admin' )){
$multivendor = Vmconfig::get('multix','none');
$multiX = $multivendor !=='none' && $multivendor !='' ? true : false ;
$cols = $multiX ? 10 : 9 ;
?>
	<table class="table table-striped">
		<thead>
		<tr>

			<th width="2">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
			</th>
			<th >
				<?php echo JText::_('COM_VIRTUEMART_PAYMENT_LIST_NAME'); ?>
			</th>
			 <th>
				<?php echo JText::_('COM_VIRTUEMART_DESCRIPTION'); ?>
			</th>
			<?php if($this->perms->check( 'admin' )){ ?>
			<th >
				<?php echo JText::_('COM_VIRTUEMART_VENDOR');  ?>
			</th><?php }?>

			<th  >
				<?php echo JText::_('COM_VIRTUEMART_PAYMENT_SHOPPERGROUPS'); ?>
			</th>
			<th >
				<?php echo JText::_('COM_VIRTUEMART_PAYMENT_ELEMENT'); ?>
			</th>
			<th  >
				<?php echo JText::_('COM_VIRTUEMART_LIST_ORDER'); ?>
			</th>
			<th >
				<?php echo JText::_('COM_VIRTUEMART_PUBLISHED'); ?>
			</th>
			<?php if ($multiX ){ ?>
			<th width="10">
				<?php echo JText::_('COM_VIRTUEMART_SHARED'); ?>
			</th>
			<?php } ?>
			 <th><?php echo $this->sort('virtuemart_paymentmethod_id', 'COM_VIRTUEMART_ID')  ?></th>
		</tr>
		</thead>
		<?php
		$k = 0;

		for ($i=0, $n=count( $this->payments ); $i < $n; $i++) {

			$row = $this->payments[$i];
			$checked = JHTML::_('grid.id', $i, $row->virtuemart_paymentmethod_id);
			$canDo = $this->canChange($row->created_by);
			$published = $this->toggle( $row->published, $i, 'published',$canDo);
			?>
			<tr class="<?php echo "row".$k; ?>">

				<td align="center" >
					<?php echo $checked; ?>
				</td>
				<td align="left">
					<?php echo $this->editLink($row->virtuemart_paymentmethod_id, $row->payment_name) ?>
				</td>
				 <td align="left">
					<?php echo $row->payment_desc; ?>
				</td>
				<?php if($this->perms->check( 'admin' )){?>
				<td align="left">
					<?php echo JText::_($row->virtuemart_vendor_id); ?>
				</td>
				<?php } ?>

				<td>
					<?php echo $row->paymShoppersList; ?>
				</td>
				<td>
					<?php echo $row->payment_element; ?>
				</td>
				<td>
					<?php echo $row->ordering; ?>
				</td>
				<td align="center">
					<?php echo $published; ?>
				</td>
				<?php if ($multiX ){ ?>
				<td align="center">
					<?php echo $row->shared; ?>
				</td>
				<?php } ?>
				<td align="center">
					<?php echo $row->virtuemart_paymentmethod_id; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		<tfoot>
			<tr>
				<td colspan="<?php echo $cols ?>">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<?php echo $this->addStandardHiddenToForm(); ?>