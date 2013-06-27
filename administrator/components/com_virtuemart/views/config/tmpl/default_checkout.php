<?php
/**
 * Admin form for the checkout configuration settings
 *
 * @package	VirtueMart
 * @subpackage Config
 * @author Oscar van Eijk
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_checkout.php 6351 2012-08-15 20:20:31Z Milbo $
 */
defined('_JEXEC') or die('Restricted access');
/*
 <table width="100%">
<tr>
<td valign="top" width="50%"> */ ?>
<fieldset>
	<legend>

	<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CHECKOUT_SETTINGS') ?></legend>
	<table class="admintable">

		<tr>
			<td class="key"><span
				class="hasTip"
				title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_ADDTOCART_POPUP_EXPLAIN'); ?>">
					<label for="addtocart_popup"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_ADDTOCART_POPUP') ?>
				</label>
			</span>
			</td>
			<td>
			<?php echo VmHTML::checkbox('addtocart_popup', VmConfig::get('addtocart_popup',1)); ?>
			</td>
		</tr>
		<tr>
			<td class="key"><span
				class="hasTip"
				title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_LANGFIX_EXPLAIN'); ?>">
					<label for="addtocart_popup"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_LANGFIX') ?>
				</label>
			</span>
			</td>
			<td>
			<?php echo VmHTML::checkbox('vmlang_js', VmConfig::get('vmlang_js',0)); ?>
			</td>
		</tr>
		<tr>
			<td class="key"><span
				class="hasTip"
				title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_AUTOMATIC_SHIPMENT_EXPLAIN'); ?>">
					<label for="automatic_shipment"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_AUTOMATIC_SHIPMENT') ?>
				</label>
			</span>
			</td>
			<td>
			<?php echo VmHTML::checkbox('automatic_shipment', VmConfig::get('automatic_shipment',1)); ?>
			</td>
		</tr>
		<tr>
			<td class="key"><span
				class="hasTip"
				title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_AUTOMATIC_PAYMENT_EXPLAIN'); ?>">
					<label for="automatic_payment"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_AUTOMATIC_PAYMENT') ?>
				</label>
			</span>
			</td>
			<td>
			<?php echo VmHTML::checkbox('automatic_payment', VmConfig::get('automatic_payment',1)); ?>
			</td>
		</tr>
		<tr>
			<td class="key"><span
				class="hasTip"
				title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_AGREE_TERMS_ONORDER_EXPLAIN'); ?>">
					<label for="agree_to_tos_onorder"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_AGREE_TERMS_ONORDER') ?>
				</label>
			</span>
			</td>
			<td>
			<?php echo VmHTML::checkbox('agree_to_tos_onorder', VmConfig::get('agree_to_tos_onorder',1)); ?>
			</td>
		</tr>

		<tr>
			<td class="key"><span
				class="hasTip"
				title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_ONCHECKOUT_SHOW_LEGALINFO_TIP'); ?>">
					<label for="oncheckout_show_legal_info"><?php echo JText::_('COM_VIRTUEMART_ADMIN_ONCHECKOUT_SHOW_LEGALINFO') ?>
				</label> </span>
			</td>
			<td>
			<?php echo VmHTML::checkbox('oncheckout_show_legal_info', VmConfig::get('oncheckout_show_legal_info',1)); ?>
			</td>
		</tr>
		<tr>
			<td class="key"><span
				class="hasTip"
				title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_ONCHECKOUT_SHOW_REGISTER_TIP'); ?>">
					<label for="oncheckout_show_register"><?php echo JText::_('COM_VIRTUEMART_ADMIN_ONCHECKOUT_SHOW_REGISTER') ?>
				</label> </span>
			</td>
			<td>
			<?php echo VmHTML::checkbox('oncheckout_show_register', VmConfig::get('oncheckout_show_register',1)); ?>
			</td>
		</tr>
		<tr>
			<td class="key"><span
				class="hasTip"
				title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_ONCHECKOUT_ONLY_REGISTERED_TIP'); ?>">
					<label for="oncheckout_only_registered"><?php echo JText::_('COM_VIRTUEMART_ADMIN_ONCHECKOUT_ONLY_REGISTERED') ?>
				</label> </span>
			</td>
			<td>
			<?php echo VmHTML::checkbox('oncheckout_only_registered', VmConfig::get('oncheckout_only_registered',0)); ?>
			</td>
		</tr>
		<tr>
			<td class="key"><span
				class="hasTip"
				title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_ONCHECKOUT_SHOW_STEPS_TIP'); ?>">
					<label for="oncheckout_show_steps"><?php echo JText::_('COM_VIRTUEMART_ADMIN_ONCHECKOUT_SHOW_STEPS') ?>
				</label> </span>
			</td>
			<td>
			<?php echo VmHTML::checkbox('oncheckout_show_steps', VmConfig::get('oncheckout_show_steps',0)); ?>
			</td>
		</tr>
		<tr>
			<td class="key"><span
				class="hasTip"
				title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_ONCHECKOUT_SHOW_PRODUCTIMAGES_TIP'); ?>">
					<label for="oncheckout_show_images"><?php echo JText::_('COM_VIRTUEMART_ADMIN_ONCHECKOUT_SHOW_PRODUCTIMAGES') ?>
				</label> </span>
			</td>
			<td>
			<?php echo VmHTML::checkbox('oncheckout_show_images', VmConfig::get('oncheckout_show_images',0)); ?>
			</td>
		</tr>

		<tr>
			<td class="key"><span
				class="hasTip"
				title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_STATUS_PDF_INVOICES_EXPLAIN'); ?>">
					 <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_STATUS_PDF_INVOICES') ?>
				 </span>
			</td>
			<td>
					<?php
					echo $this->orderStatusModel->renderOrderStatusList(VmConfig::get('inv_os',array()),'inv_os[]');
					//echo VmHTML::selectList('inv_os',VmConfig::get('inv_os','C'),$this->orderStatusList);
					?>
			</td>
		</tr>
	</table>
</fieldset>


<?php /*	</td>
 <td valign="top">

<fieldset>
<legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_TITLES') ?></legend>
<table class="admintable">
<tr>
<td class="key">
<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_TITLES_LBL_TIP'); ?>">
<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_TITLES_LBL') ?>
</span>
</td>
<td><fieldset class="checkbox">
<?php echo $this->titlesFields ; ?>
</fieldset></td>
</tr>
</table>
</td>
</tr>
</table> */ ?>