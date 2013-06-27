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

// Include ALU System
require_once JPATH_COMPONENT_ADMINISTRATOR.DS.'liveupdate'.DS.'liveupdate.php';

?> 

<div id="cpanel">

	<div class="icon"><?php VmImage::displayImageButton(JROUTE::_('index.php?option=com_virtuemart&view=product'), 'vm_shop_products_48', JText::_('COM_VIRTUEMART_PRODUCT_S')); ?></div>
	<div class="icon"><?php VmImage::displayImageButton(JROUTE::_('index.php?option=com_virtuemart&view=category'), 'vm_shop_categories_48', JText::_('COM_VIRTUEMART_CATEGORY_S')); ?></div>
	<div class="icon"><?php VmImage::displayImageButton(JROUTE::_('index.php?option=com_virtuemart&view=orders'), 'vm_shop_orders_48', JText::_('COM_VIRTUEMART_ORDER_S')); ?></div>
	<div class="icon"><?php VmImage::displayImageButton(JROUTE::_('index.php?option=com_virtuemart&view=paymentmethod'), 'vm_shop_payment_48', JText::_('COM_VIRTUEMART_PAYMENTMETHOD_S')); ?></div>
	<div class="icon"><?php VmImage::displayImageButton(JROUTE::_('index.php?option=com_virtuemart&view=user'), 'vm_shop_users_48', JText::_('COM_VIRTUEMART_USER_S')); ?></div>
	<div class="icon"><?php VmImage::displayImageButton(JROUTE::_('index.php?option=com_virtuemart&view=config'), 'vm_shop_configuration_48', JText::_('COM_VIRTUEMART_CONFIG')); ?></div>
	<div class="icon"><?php VmImage::displayImageButton(JROUTE::_('index.php?option=com_virtuemart&view=user&task=editshop'), 'vm_shop_mart_48', JText::_('COM_VIRTUEMART_STORE')); ?></div>
	<div class="icon"><?php VmImage::displayImageButton('http://virtuemart.org/index.php?option=com_content&amp;task=view&amp;id=248&amp;Itemid=125', 'vm_shop_help_48', JText::_('COM_VIRTUEMART_DOCUMENTATION')); ?></div>
	<div class="icon"><?php echo LiveUpdate::getIcon(array(),'url'); ?></div>

<div class="clear"></div>
</div>