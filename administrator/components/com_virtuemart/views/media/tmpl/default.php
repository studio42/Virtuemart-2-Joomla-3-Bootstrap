<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author Max Milbers, Roland?
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 6559 2012-10-18 13:22:30Z Milbo $
*/
jimport('joomla.filesystem.file'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php AdminUIHelper::startAdminArea(); ?>
	<div class="btn-toolbar" id="filter-bar">
		<?php echo $this->displayDefaultViewSearch('COM_VIRTUEMART_NAME','searchMedia') .' ' ;
		// if link is set then we come from a direct link(product or category)
		if ( isset($this->link)) {
			echo $this->link;
		}
		else echo $this->lists['search_type']; ?>
		<?php echo $this->DisplayFilterPublish() ?>
	</div>
	<div class="clearfix"> </div>
	<div id="results">
		<?php 
		// split to use ajax search
		echo $this->loadTemplate('results'); ?>
	</div>
</form>
<?php AdminUIHelper::endAdminArea(true);