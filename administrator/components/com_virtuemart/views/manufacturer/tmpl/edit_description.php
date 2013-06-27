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
* @version $Id: edit.php 3617 2011-07-05 12:55:12Z enytheme $
*/


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>
<?php echo $this->langList; ?>
<div class="col50">
	<fieldset>
	<legend><?php echo JText::_('COM_VIRTUEMART_MANUFACTURER_DETAILS'); ?></legend>
	<table class="admintable">

		<?php echo VmHTML::row('input','COM_VIRTUEMART_MANUFACTURER_NAME','mf_name',$this->manufacturer->mf_name); ?>
	    	<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_PUBLISH','published',$this->manufacturer->published); ?>
		<?php echo VmHTML::row('input',$this->viewName.' '. JText::_('COM_VIRTUEMART_SLUG'),'slug',$this->manufacturer->slug); ?>
		<?php echo VmHTML::row('select','COM_VIRTUEMART_MANUFACTURER_CATEGORY_NAME','virtuemart_manufacturercategories_id',$this->manufacturerCategories,$this->manufacturer->virtuemart_manufacturercategories_id,'','virtuemart_manufacturercategories_id', 'mf_category_name',false); ?>
		<?php echo VmHTML::row('input','COM_VIRTUEMART_MANUFACTURER_URL','mf_url',$this->manufacturer->mf_url); ?>
		<?php echo VmHTML::row('input','COM_VIRTUEMART_MANUFACTURER_EMAIL','mf_email',$this->manufacturer->mf_email); ?>
		<?php echo VmHTML::row('editor','COM_VIRTUEMART_MANUFACTURER_DESCRIPTION','mf_desc',$this->manufacturer->mf_desc); ?>


	</table>
	</fieldset>
</div>