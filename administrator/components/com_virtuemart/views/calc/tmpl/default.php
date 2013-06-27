<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Calculation tool
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
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div id="header">
		<div id="filterbox">
		<table>
		  <tr>
			 <td align="left">
				<?php echo $this->displayDefaultViewSearch() ?>
			 </td>
		  </tr>
		</table>
		</div>
		<div id="resultscounter" ><?php echo $this->pagination->getResultsCounter();?></div>
	</div>
	<br />
	<div id="editcell">
		<table class="adminlist" cellspacing="0" cellpadding="0">
		<thead>
		<tr>

			<th>
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->calcs); ?>);" />
			</th>
			<th><?php echo $this->sort('calc_name', 'COM_VIRTUEMART_NAME') ; ?></th>
			<?php if((Vmconfig::get('multix','none')!='none') && $this->perms->check( 'admin' )){ ?>
			<th width="20">
				<?php echo JText::_('COM_VIRTUEMART_VENDOR');  ?>
			</th><?php }  ?>
			<th><?php echo $this->sort('calc_descr' , 'COM_VIRTUEMART_DESCRIPTION'); ?></th>
			<th><?php echo $this->sort('ordering') ; ?></th>
			<th><?php echo $this->sort('calc_kind') ; ?></th>
			<th><?php echo JText::_('COM_VIRTUEMART_CALC_VALUE_MATHOP'); ?></th>
			<th><?php echo $this->sort('calc_value' , 'COM_VIRTUEMART_VALUE'); ?></th>
			<th><?php echo $this->sort('calc_currency' , 'COM_VIRTUEMART_CURRENCY'); ?></th>
			<th><?php echo JText::_('COM_VIRTUEMART_CATEGORY_S'); ?></th>
			<th><?php echo JText::_('COM_VIRTUEMART_MANUFACTURER'); // Mod. <mediaDESIGN> St.Kraft 2013-02-24  ?></th>
			<th><?php echo JText::_('COM_VIRTUEMART_SHOPPERGROUP_IDS'); ?></th>
			<th><?php echo JText::_('COM_VIRTUEMART_CALC_VIS_SHOPPER'); ?></th>
<?php /*	<th width="10"><?php echo JText::_('COM_VIRTUEMART_CALC_VIS_VENDOR'); ?></th> */  ?>
			<th><?php echo $this->sort('publish_up' , 'COM_VIRTUEMART_START_DATE'); ?></th>
			<th><?php echo $this->sort('publish_down' , 'COM_VIRTUEMART_END_DATE'); ?></th>
<?php /*	<th width="20"><?php echo JText::_('COM_VIRTUEMART_CALC_AMOUNT_COND'); ?></th>
			<th width="10"><?php echo JText::_('COM_VIRTUEMART_CALC_AMOUNT_DIMUNIT'); ?></th> */  ?>
			<th><?php echo JText::_('COM_VIRTUEMART_COUNTRY_S'); ?></th>
			<th><?php echo JText::_('COM_VIRTUEMART_STATE_IDS'); ?></th>
			<th><?php echo JText::_('COM_VIRTUEMART_PUBLISHED'); ?></th>
			<?php if((Vmconfig::get('multix','none')!='none') && $this->perms->check( 'admin' )){ ?>
			<th width="20">
				<?php echo JText::_( 'COM_VIRTUEMART_SHARED')  ?>
			</th><?php }  ?>
			<th><?php echo $this->sort('virtuemart_calc_id', 'COM_VIRTUEMART_ID')  ?></th>
		<?php /*	<th width="10">
				<?php echo JText::_('COM_VIRTUEMART_SHARED'); ?>
			</th> */ ?>
		</tr>
		</thead>
		<?php
		$k = 0;

		for ($i=0, $n=count( $this->calcs ); $i < $n; $i++) {

			$row = $this->calcs[$i];
			$checked = JHTML::_('grid.id', $i, $row->virtuemart_calc_id);
			$published = JHTML::_('grid.published', $row, $i);
			$shared = $this->toggle($row->shared, $i, 'toggle.shared');
			$editlink = JROUTE::_('index.php?option=com_virtuemart&view=calc&task=edit&cid[]=' . $row->virtuemart_calc_id);
			?>
			<tr class="<?php echo "row".$k; ?>">

				<td>
					<?php echo $checked; ?>
				</td>
				<td align="left">
					<a href="<?php echo $editlink; ?>"><?php echo $row->calc_name; ?></a>
				</td>
				<?php  if((Vmconfig::get('multix','none')!='none') && $this->perms->check( 'admin' )){ ?>
				<td align="left">
					<?php echo $row->virtuemart_vendor_id; ?>
				</td>
				<?php } ?>
				<td>
					<?php echo $row->calc_descr; ?>
				</td>
				<td>
					<?php echo $row->ordering; ?>
				</td>
				<td>
					<?php echo $row->calc_kind; ?>
				</td>
				<td>
					<?php echo $row->calc_value_mathop; ?>
				</td>
				<td>
					<?php echo $row->calc_value; ?>
				</td>
				<td>
					<?php echo $row->currencyName; ?>
				</td>
				<td>
					<?php echo $row->calcCategoriesList; ?>
				</td>
        <td>
        	<?php echo $row->calcManufacturersList; /* Mod. <mediaDESIGN> St.Kraft 2013-02-24 Herstellerrabatt */ ?>
        </td>
				<td>
					<?php echo $row->calcShoppersList; ?>
				</td>
				<td align="center">
					<a href="#" onclick="return listItemTask('cb<?php echo $i;?>', 'toggle.calc_shopper_published')" title="<?php echo ( $row->calc_shopper_published == '1' ) ? JText::_('COM_VIRTUEMART_YES') : JText::_('COM_VIRTUEMART_NO');?>">
						<?php echo JHtml::_('image.administrator', ((JVM_VERSION===1) ? '' : 'admin/') . ($row->calc_shopper_published ? 'tick.png' : 'publish_x.png')); ?>
					</a>
				</td>
<?php /*				<td align="center">
					<a href="#" onclick="return listItemTask('cb<?php echo $i;?>', 'toggle.calc_vendor_published')" title="<?php echo ( $row->calc_vendor_published == '1' ) ? JText::_('COM_VIRTUEMART_YES') : JText::_('COM_VIRTUEMART_NO');?>">
						<?php echo JHtml::_('image.administrator', ((JVM_VERSION===1) ? '' : 'admin/') . ($row->calc_vendor_published ? 'tick.png' : 'publish_x.png')); ?>
					</a>
				</td> */  ?>
				<td>
					<?php
						echo vmJsApi::date( $row->publish_up, 'LC4',true);
					?>
				</td>
				<td>
					<?php
							echo vmJsApi::date( $row->publish_down, 'LC4',true);
					?>
				</td>
<?php /*				<td>
					<?php echo $row->calc_amount_cond; ?>
				</td>
				<td>
					<?php echo JText::_($row->calc_amount_dimunit); ?>
				</td> */  ?>
				<td>
					<?php echo JText::_($row->calcCountriesList); ?>
				</td>
				<td>
					<?php echo JText::_($row->calcStatesList); ?>
				</td>
				<td align="center">
					<?php echo $published; ?>
				</td>

				<?php
				if((Vmconfig::get('multix','none')!='none')) {
				?><td align="center">
					   <?php echo $shared; ?>
			        </td>
				<?php
				}
			?>
				<td align="right">
					<?php echo $row->virtuemart_calc_id; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		<tfoot>
			<tr>
				<td colspan="21">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
</div>
	<?php echo $this->addStandardHiddenToForm(); ?>
</form>


<?php AdminUIHelper::endAdminArea(); ?>
