<?php
/**
*
* Modify user form view, User info
*
* @package	VirtueMart
* @subpackage User
* @author Oscar van Eijk
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: edit_vendor.php 6303 2012-08-01 07:42:16Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access'); ?>
<div class="row-fluid">
	<fieldset class="span6">
		<legend>
			<?php echo JText::_('COM_VIRTUEMART_VENDOR') ?>
			<div class="pull-right"><?php echo $this->langList; ?></div>
		</legend>
		<table class="admintable">
			<?php echo VmHTML::row('input','COM_VIRTUEMART_STORE_FORM_STORE_NAME','vendor_store_name',$this->vendor->vendor_store_name); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_STORE_FORM_COMPANY_NAME','vendor_name',$this->vendor->vendor_name); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_PRODUCT_FORM_URL','vendor_url',$this->vendor->vendor_url); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_STORE_FORM_MPOV','vendor_min_pov',$this->vendor->vendor_min_pov); ?>
		</table>
	</fieldset>
	<fieldset  class="span6">
		<legend>
			<?php echo JText::_('COM_VIRTUEMART_STORE_CURRENCY_DISPLAY') ?>
		</legend>
		<table class="admintable">
			<tr>
				<td class="key">
					<?php echo JText::_('COM_VIRTUEMART_CURRENCY'); ?>:
				</td>
				<td>
					<?php echo JHTML::_('Select.genericlist', $this->currencies, 'vendor_currency', '', 'virtuemart_currency_id', 'currency_name', $this->vendor->vendor_currency); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo JText::_('COM_VIRTUEMART_STORE_FORM_ACCEPTED_CURRENCIES'); ?>:
				</td>
				<td>
					<?php echo JHTML::_('Select.genericlist', $this->currencies, 'vendor_accepted_currencies[]', 'size=10 multiple="multiple"', 'virtuemart_currency_id', 'currency_name', $this->vendor->vendor_accepted_currencies); ?>
				</td>
			</tr>
		</table>
	</fieldset>
</div>
<div class="row-fluid">
	<?php
		echo $this->vendor->images[0]->displayFilesHandler($this->vendor->virtuemart_media_id,'vendor');
	?>
</div>
	<fieldset>
		<legend>
			<?php echo JText::_('COM_VIRTUEMART_STORE_FORM_DESCRIPTION');?>
		</legend>
		<?php echo $this->editor->display('vendor_store_desc', $this->vendor->vendor_store_desc, '100%', 450, 70, 15,false)?>
	</fieldset>
	<fieldset>
		<legend>
			<?php echo JText::_('COM_VIRTUEMART_STORE_FORM_TOS');?>
		</legend>
		<?php echo $this->editor->display('vendor_terms_of_service', $this->vendor->vendor_terms_of_service, '100%', 450, 70, 15,false)?>
	</fieldset>
	<fieldset>
		<legend>
			<?php echo JText::_('COM_VIRTUEMART_STORE_FORM_LEGAL');?>
		</legend>
		<?php echo $this->editor->display('vendor_legal_info', $this->vendor->vendor_legal_info, '100%', 400, 70, 15,false)?>
	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('COM_VIRTUEMART_METAINFO'); ?></legend>
		<?php echo shopFunctions::renderMetaEdit($this->vendor); ?>
	</fieldset>
<input type="hidden" name="user_is_vendor" value="1" />
<input type="hidden" name="virtuemart_vendor_id" value="<?php echo $this->vendor->virtuemart_vendor_id; ?>" />
<input type="hidden" name="last_task" value="<?php echo JRequest::getCmd('task'); ?>" />
