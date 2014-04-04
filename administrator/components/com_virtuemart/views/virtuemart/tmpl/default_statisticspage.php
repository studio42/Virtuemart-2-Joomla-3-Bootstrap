<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Config
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default_system.php 3477 2011-06-11 12:50:50Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<div class="well well-small">
	<div class="module-title nav-header"><?php echo JText::_('COM_VIRTUEMART_STATISTIC_STATISTICS') ?></div>
	<div class="row-striped">
		<?php if ( ShopFunctions::can('edit','user') ) { ?>
		<div class="row-fluid">
			<span class="badge <?php echo $this->nbrCustomers ? 'badge-info' : 'badge-warning' ?>"> <?php echo $this->nbrCustomers ?></span>
			<a href="<?php echo JROUTE::_('index.php?option=com_virtuemart&view=user'.$this->tmpl);?>">
				<?php echo JText::_('COM_VIRTUEMART_STATISTIC_CUSTOMERS') ?>
			</a>
		</div>
		<?php } ?>
		<div class="row-fluid">
			<span class="badge <?php echo $this->nbrActiveProducts ? 'badge-info' : 'badge-warning' ?>"> <?php echo $this->nbrActiveProducts ?></span>
			<a href="<?php echo JROUTE::_('index.php?option=com_virtuemart&view=product&filter_order_Dir=DESC&filter_order=published'.$this->tmpl);?>">
				<?php echo JText::_('COM_VIRTUEMART_STATISTIC_ACTIVE_PRODUCTS') ?>
			</a>
		</div>
		<div class="row-fluid">
			<span class="badge <?php echo $this->nbrInActiveProducts ? 'badge-warning' : '' ?>"><?php  echo $this->nbrInActiveProducts ?></span>
			<a href="<?php echo JROUTE::_('index.php?option=com_virtuemart&view=product&filter_order_Dir=ASC&filter_order=published'.$this->tmpl);?>">
			<?php echo JText::_('COM_VIRTUEMART_STATISTIC_INACTIVE_PRODUCTS') ?>
			</a>
		</div>
		<div class="row-fluid">
			<span class="badge <?php echo $this->nbrFeaturedProducts ? 'badge-success' : '' ?>"><?php echo $this->nbrFeaturedProducts ?></span>
			<a href="<?php echo JROUTE::_('index.php?option=com_virtuemart&view=product&filter_order_Dir=DESC&filter_order=product_special'.$this->tmpl);?>">
				<?php echo JText::_('COM_VIRTUEMART_SHOW_FEATURED') ?>
			</a>
		</div>
	</div>
</div>
<?php if ( ShopFunctions::can('edit','orders') ) { ?>
<div class="well well-small">
	<div class="module-title nav-header">
		<?php echo JText::_('COM_VIRTUEMART_ORDER_MOD') ?>
	</div>
	<div class="row-striped">

	<?php
	$sum = 0;
	// var_dump($this->ordersByStatus);
	foreach ( $this->ordersByStatus as $row ) {
		$link = JROUTE::_('index.php?option=com_virtuemart&view=orders&show='.$row->code.$this->tmpl);
		?>
		<div class="row-fluid">
			<span class="badge <?php echo $row->order_count ? 'badge-info' : '' ?>"><?php echo $row->order_count; ?></span>
			<a href="<?php echo $link; ?>"><?php echo jText::_($row->order_status_name) ; ?></a>
		</div>
	<?php
		$sum = $sum + $row->order_count;
	} ?>
		<div class="row-fluid">
			<span class="badge"><strong><?php echo $sum ?></strong></span>
			<a href="<?php echo JROUTE::_('index.php?option=com_virtuemart&view=orders'.$this->tmpl);?>">
				<?php echo JText::_('JALL') ?>
			</a>
		</div>
	</div>
</div>
<?php } if ( ShopFunctions::can('edit','orders') ) { ?>
<div class="well well-small">
	<div class="module-title nav-header"><?php echo JText::_('COM_VIRTUEMART_STATISTIC_NEW_CUSTOMERS') ?></div>
	<div class="row-striped">
		<?php
		for ($i=0, $n=count($this->recentCustomers); $i < $n; $i++) {
			$row = $this->recentCustomers[$i];
			$link = JROUTE::_('index.php?option=com_virtuemart&view=user&virtuemart_user_id='.$row->virtuemart_user_id.$this->tmpl);
			?>
			<div class="row-fluid">
	  			<a href="<?php echo $link; ?>">
		  				<?php echo   $row->first_name . ' ' . $row->last_name. ' (' . $row->order_number . ') '; ?>
	  			</a>
			</div>
		<?php
		}?>
	</div>
</div>
<?php } ?>