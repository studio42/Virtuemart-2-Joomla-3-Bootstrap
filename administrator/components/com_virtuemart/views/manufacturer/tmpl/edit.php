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
* @version $Id: edit.php 6350 2012-08-14 17:18:08Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();

?>

<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="adminForm">
<fieldset>
	<legend><?php echo JText::_('COM_VIRTUEMART_MANUFACTURER_DETAILS'); ?><div class="pull-right"><?php echo $this->langList ?></div></legend>
	<div class="row-fluid">
		<table class="span6">
			<?php echo VmHTML::row('input','COM_VIRTUEMART_MANUFACTURER_NAME','mf_name',$this->manufacturer->mf_name); ?>
			<?php echo VmHTML::row('input',$this->viewName.' '. JText::_('COM_VIRTUEMART_SLUG'),'slug',$this->manufacturer->slug); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_MANUFACTURER_URL','mf_url',$this->manufacturer->mf_url); ?>
		</table>
		<table class="span6">
	    	<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_PUBLISHED','published',$this->manufacturer->published); ?>
			<?php echo VmHTML::row('select','COM_VIRTUEMART_MANUFACTURER_CATEGORY_NAME','virtuemart_manufacturercategories_id',$this->manufacturerCategories,$this->manufacturer->virtuemart_manufacturercategories_id,'','virtuemart_manufacturercategories_id', 'mf_category_name',false); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_MANUFACTURER_EMAIL','mf_email',$this->manufacturer->mf_email); ?>
		</table>
	</div>
</fieldset>
<div class="accordion">
		<?php 
		$this->manufacturer->images[0]->addHidden('virtuemart_vendor_id',$this->virtuemart_vendor_id);

		echo $this->manufacturer->images[0]->displayFilesHandler($this->manufacturer->virtuemart_media_id,'manufacturer'); ?>
</div>
<fieldset>
	<legend><?php echo JText::_('COM_VIRTUEMART_DESCRIPTION'); ?></legend>
	<div>
		<?php echo VmHTML::editor('mf_desc',$this->manufacturer->mf_desc,'100%',null,null,false); ?>
	</div>
</fieldset>

	<input type="hidden" name="virtuemart_manufacturer_id" value="<?php echo $this->manufacturer->virtuemart_manufacturer_id; ?>" />
	<?php echo $this->addStandardHiddenToForm(); ?>
</form>
<script type="text/javascript">
function toggleDisable( elementOnChecked, elementDisable, disableOnChecked ) {
	try {
		if( !disableOnChecked ) {
			if(elementOnChecked.checked==true) {
				elementDisable.disabled=false;
			}
			else {
				elementDisable.disabled=true;
			}
		}
		else {
			if(elementOnChecked.checked==true) {
				elementDisable.disabled=true;
			}
			else {
				elementDisable.disabled=false;
			}
		}
	}
	catch( e ) {}
}

function toggleFullURL() {
	if( jQuery('#manufacturer_full_image_url').val().length>0) document.adminForm.manufacturer_full_image_action[1].checked=false;
	else document.adminForm.manufacturer_full_image_action[1].checked=true;
	toggleDisable( document.adminForm.manufacturer_full_image_action[1], document.adminForm.manufacturer_thumb_image_url, true );
	toggleDisable( document.adminForm.manufacturer_full_image_action[1], document.adminForm.manufacturer_thumb_image, true );
}
</script>
<?php AdminUIHelper::endAdminArea(); ?>