<?php
/**
 *
 * Description
 *
 * @package    VirtueMart
 * @subpackage
 * @author
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: orders.php 6408 2012-09-08 11:23:40Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die();
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php AdminUIHelper::startAdminArea(); ?>
	<div id="filter-bar" class="btn-toolbar">
		<?php echo $this->displayDefaultViewSearch ('COM_VIRTUEMART_ORDER_PRINT_NAME'); ?>
		<?php echo JText::_ ('COM_VIRTUEMART_ORDERSTATUS') . ':' . $this->lists['state_list']; ?>
	</div>
	<div class="clearfix clear"> </div>
	<div id="results">
		<?php 
		// split to use ajax search
		echo $this->loadTemplate('results'); ?>
	</div>

	<?php AdminUIHelper::endAdminArea (true); ?>
</form>

