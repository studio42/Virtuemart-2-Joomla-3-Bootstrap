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
 * @version $Id: default_checkout.php 7026 2013-06-24 22:07:10Z Milbo $
 */
defined('_JEXEC') or die('Restricted access');
/*
 <table width="100%">
<tr>
<td valign="top" width="50%"> */ ?>
<fieldset>
	<legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CHECKOUT_SETTINGS'); ?></legend>
	<table class="admintable">
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_ADDTOCART_POPUP','addtocart_popup',VmConfig::get('addtocart_popup',1) ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_CFG_POPUP_REL','popup_rel',VmConfig::get('popup_rel',1) ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_LANGFIX','vmlang_js',VmConfig::get('vmlang_js') ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_AUTOMATIC_SHIPMENT','automatic_shipment',VmConfig::get('automatic_shipment',1) ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_AUTOMATIC_PAYMENT','automatic_payment',VmConfig::get('automatic_payment',1) ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_AGREE_TERMS_ONORDER','agree_to_tos_onorder',VmConfig::get('agree_to_tos_onorder',1) ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_ONCHECKOUT_SHOW_LEGALINFO','oncheckout_show_legal_info',VmConfig::get('oncheckout_show_legal_info',1) ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_ONCHECKOUT_SHOW_REGISTER','oncheckout_show_register',VmConfig::get('oncheckout_show_register',1) ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_ONCHECKOUT_ONLY_REGISTERED','oncheckout_only_registered',VmConfig::get('oncheckout_only_registered',1) ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_ONCHECKOUT_SHOW_STEPS','oncheckout_show_steps',VmConfig::get('oncheckout_show_steps') ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_ONCHECKOUT_SHOW_PRODUCTIMAGES','oncheckout_show_images',VmConfig::get('oncheckout_show_images') ); ?>

		<?php echo VmHTML::row('raw','COM_VIRTUEMART_ADMIN_CFG_STATUS_PDF_INVOICES',
			$this->orderStatusModel->renderOSList(VmConfig::get('inv_os',array('C')),'inv_os',TRUE) ); ?>

		<?php echo VmHTML::row('raw','COM_VIRTUEMART_CFG_OSTATUS_EMAILS_SHOPPER',
			 $this->orderStatusModel->renderOSList(VmConfig::get('email_os_s',array('U','C','S','R','X')),'email_os_s',TRUE) ); ?>

		<?php echo VmHTML::row('raw','COM_VIRTUEMART_CFG_OSTATUS_EMAILS_VENDOR',
			$this->orderStatusModel->renderOSList(VmConfig::get('email_os_v',array('U','C','R','X')),'email_os_v',TRUE) ); ?>

	</table>
</fieldset>


<?php /*	</td>
 <td valign="top">

<fieldset>
<legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_TITLES') ?></legend>
<table class="admintable">
<tr>
<td class="key">
<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_TITLES_LBL_TIP'); ?>">
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