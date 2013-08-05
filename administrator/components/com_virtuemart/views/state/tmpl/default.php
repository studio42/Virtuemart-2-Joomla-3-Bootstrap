<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage State
* @author RickG, Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 6048 2012-05-30 20:18:53Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php AdminUIHelper::startAdminArea(); ?>

    <div><?php echo JHTML::_('link','index.php?option=com_virtuemart&view=country&task=edit&cid[]='.$this->virtuemart_country_id,JText::sprintf('COM_VIRTUEMART_STATES_COUNTRY',$this->country_name)); ?></div>
	<div class="btn-toolbar" id="filter-bar">
		<?php echo $this->displayDefaultViewSearch() ?>
	</div>
	<div class="clearfix"> </div>
	<div id="results">
		<?php 
		// split to use ajax search
		echo $this->loadTemplate('results'); ?>
	</div>

	<?php AdminUIHelper::endAdminArea(true); ?>
</form>

