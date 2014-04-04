<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Manufacturer
* @author Patrick Kohl
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 5814 2012-04-06 10:23:12Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access'); ?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php AdminUIHelper::startAdminArea(); ?>

	<div id="filter-bar" class="btn-toolbar">
		<div class="btn-group pull-left">
			<?php echo $this->displayDefaultViewSearch() ?>
		</div>
		<div class="btn-group pull-left">
			<?php echo JText::_('COM_VIRTUEMART_MANUFACTURERCATEGORIES') . $this->lists['virtuemart_manufacturercategories_id']; ?>
			<?php echo $this->DisplayFilterPublish() ?>
		</div>
	</div>
	<div class="clearfix"> </div>
	<div id="results">
		<?php 
		// split to use ajax search
		echo $this->loadTemplate('results'); ?>
	</div>
	<?php AdminUIHelper::endAdminArea(true); ?>
</form>

