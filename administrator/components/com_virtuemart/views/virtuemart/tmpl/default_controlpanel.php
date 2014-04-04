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
$admin = JFactory::getUser()->authorise('core.admin');
// Include ALU System
if ($admin) require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'liveupdate'.DS.'liveupdate.php';

?> 

<div class="well well-small hidden-phone">
	<div class="module-title nav-header"><?php echo JText::_('COM_VIRTUEMART_CONTROL_PANEL') ?></div>
	<div class="row-striped">
		<?php if ( ShopFunctions::can('add','product') ) { ?>
		<div class="row-fluid"><?php $this->panelButton('product&task=add', 'icon icon-new', JText::_('COM_VIRTUEMART_PRODUCT').'<small>('.JText::_('COM_VIRTUEMART_ADD').')</small>'); ?>
		</div>
		<?php } if ( ShopFunctions::can('edit','product') ) { ?>
		<div class="row-fluid"><?php $this->panelButton('product', 'icon icon-camera', JText::_('COM_VIRTUEMART_PRODUCT_S')); ?>
		</div>
		<?php } if ( ShopFunctions::can('edit','category') ) { ?>
		<div class="row-fluid"><?php $this->panelButton('category', 'icon icon-folder', JText::_('COM_VIRTUEMART_CATEGORY_S')); ?>
		</div>
		<?php } if ( ShopFunctions::can('edit','orders') ) { ?>
		<div class="row-fluid"><?php $this->panelButton('orders', 'icon icon-stack', JText::_('COM_VIRTUEMART_ORDER_S')); ?>
		</div>
		<?php } if ( ShopFunctions::can('edit','paymentmethod') ) { ?>
		<div class="row-fluid"><?php $this->panelButton('paymentmethod', 'icon icon-credit', JText::_('COM_VIRTUEMART_PAYMENTMETHOD_S')); ?>
		</div>
		<?php } if ( ShopFunctions::can('edit','user') ) { ?>
		<div class="row-fluid"><?php $this->panelButton('user', 'icon icon-users', JText::_('COM_VIRTUEMART_USER_S')); ?>
		</div>
		<?php } if ($admin) { ?>
			<div class="row-fluid"><?php $this->panelButton('config', 'icon icon-cog', JText::_('COM_VIRTUEMART_CONFIG')); ?>
			</div>
		<?php } if ( ShopFunctions::can('editshop','user') ) { ?>
		<div class="row-fluid"><?php $this->panelButton('user&task=editshop', 'icon icon-home', JText::_('COM_VIRTUEMART_STORE')); ?>
		</div>
		<?php } ?>
		<div class="row-fluid"><?php $this->panelButton('http://docs.virtuemart.net/', 'icon icon-question-sign', JText::_('COM_VIRTUEMART_DOCUMENTATION'),false); ?>
		</div>
	</div>

</div>
<?php if ( ShopFunctions::can('edit','orders') ) { ?>
<div class="well well-small">	
	<div class="module-title nav-header"><?php echo JText::_('COM_VIRTUEMART_STATISTIC_NEW_ORDERS') ?></div>
	<div class="row-striped">
		<?php
		for ($i=0, $n=count($this->recentOrders); $i < $n; $i++) {
			$row = $this->recentOrders[$i];
			$link = JROUTE::_('index.php?option=com_virtuemart&view=orders&task=edit&virtuemart_order_id='.$row->virtuemart_order_id);
			$state = &$this->ordersByStatus[$row->order_status];
			if ($state->order_stock_handle === 'R')
				$badgeColor ='badge-info' ;//reserved
			else if ($state->order_stock_handle === 'O')
				$badgeColor ='badge-success' ;//Delivered most of time
			else $badgeColor ='badge-warning' ;//cancelled/removed

			?>
			<div class="row-fluid">
				<div class="span6"><span class="badge"><?php echo $row->order_total ?></span> 
					<a href="<?php echo $link; ?>"><?php echo $row->order_number; ?></a>
				</div>
				<div class="span5">
					<span class="label <?php echo $badgeColor ?>"><?php echo $this->ordersByStatus[$row->order_status]->order_status_name ?></span>
				</div>
			</div>
			<?php
		} ?>
	</div>
</div>
<?php } ?>