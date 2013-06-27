<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Manufacturer Category
* @author Patrick Kohl
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: edit.php 5225 2012-01-06 01:50:19Z electrocity $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();
AdminUIHelper::imitateTabs('start','COM_VIRTUEMART_MANUFACTURER_CATEGORY_DETAILS');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<?php echo $this->langList; ?>
<div class="col50">
	<fieldset>
	<legend><?php echo JText::_('COM_VIRTUEMART_MANUFACTURER_CATEGORY_DETAILS'); ?></legend>
	<table class="admintable">
		<?php echo VmHTML::row('input','COM_VIRTUEMART_MANUFACTURER_CATEGORY_NAME','mf_category_name',$this->manufacturerCategory->mf_category_name); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_PUBLISH','published',$this->manufacturerCategory->published); ?>
		<?php echo VmHTML::row('textarea','COM_VIRTUEMART_MANUFACTURER_CATEGORY_DESCRIPTION','mf_category_desc',$this->manufacturerCategory->mf_category_desc); ?>

	</table>
	</fieldset>
</div>


	<input type="hidden" name="virtuemart_manufacturercategories_id" value="<?php echo $this->manufacturerCategory->virtuemart_manufacturercategories_id; ?>" />
	<?php echo $this->addStandardHiddenToForm(); ?>
</form>

<?php
AdminUIHelper::imitateTabs('end');
AdminUIHelper::endAdminArea(); ?>