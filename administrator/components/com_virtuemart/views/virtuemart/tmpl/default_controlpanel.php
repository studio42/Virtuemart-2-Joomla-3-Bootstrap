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
		<div class="row-fluid"><?php VmImage::displayImageButton(JROUTE::_('index.php?option=com_virtuemart&view=product&task=add'), 'vmicon vmicon-16-editadd icon-nofloat', JText::_('COM_VIRTUEMART_PRODUCT').'<small>('.JText::_('COM_VIRTUEMART_ADD').')</small>'); ?>
		</div>
		<div class="row-fluid"><?php VmImage::displayImageButton(JROUTE::_('index.php?option=com_virtuemart&view=product'), 'vmicon vmicon-16-camera icon-nofloat', JText::_('COM_VIRTUEMART_PRODUCT_S')); ?>
		</div>
		<div class="row-fluid"><?php VmImage::displayImageButton(JROUTE::_('index.php?option=com_virtuemart&view=category'), 'vmicon vmicon-16-folder_camera icon-nofloat', JText::_('COM_VIRTUEMART_CATEGORY_S')); ?>
		</div>
		<div class="row-fluid"><?php VmImage::displayImageButton(JROUTE::_('index.php?option=com_virtuemart&view=orders'), 'vmicon vmicon-16-page_white_stack icon-nofloat', JText::_('COM_VIRTUEMART_ORDER_S')); ?>
		</div>
		<div class="row-fluid"><?php VmImage::displayImageButton(JROUTE::_('index.php?option=com_virtuemart&view=paymentmethod'), 'vmicon vmicon-16-creditcards icon-nofloat', JText::_('COM_VIRTUEMART_PAYMENTMETHOD_S')); ?>
		</div>
		<div class="row-fluid"><?php VmImage::displayImageButton(JROUTE::_('index.php?option=com_virtuemart&view=user'), 'vmicon vmicon-16-user icon-nofloat', JText::_('COM_VIRTUEMART_USER_S')); ?>
		</div>
		<?php if ($admin) { ?>
			<div class="row-fluid"><?php VmImage::displayImageButton(JROUTE::_('index.php?option=com_virtuemart&view=config'), 'icon-cog', JText::_('COM_VIRTUEMART_CONFIG')); ?>
			</div>
		<?php } ?>
		<div class="row-fluid"><?php VmImage::displayImageButton(JROUTE::_('index.php?option=com_virtuemart&view=user&task=editshop'), 'vmicon vmicon-16-reseller_account_template icon-nofloat', JText::_('COM_VIRTUEMART_STORE')); ?>
		</div>
		<div class="row-fluid"><?php VmImage::displayImageButton('http://http://docs.virtuemart.net/', 'icon-question-sign', JText::_('COM_VIRTUEMART_DOCUMENTATION')); ?>
		</div>
	</div>

</div>
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