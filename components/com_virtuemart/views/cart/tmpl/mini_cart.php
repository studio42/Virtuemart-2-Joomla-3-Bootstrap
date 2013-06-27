<?php
/**
*
* Layout for the shopping cart
*
* @package	VirtueMart
* @subpackage Cart
* @author Max Milbers
*
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: cart.php 2551 2010-09-30 18:52:40Z milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>
	<a href="<?php echo $this->continue_link; ?>"><?php echo JText::_('COM_VIRTUEMART_CONTINUE_SHOPPING') ?></a>
	<a style ="float:right;" href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart'); ?>"><?php echo JText::_('COM_VIRTUEMART_CART_SHOW') ?></a>
<br style="clear:both">
