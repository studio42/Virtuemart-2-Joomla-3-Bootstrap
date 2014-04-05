<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Config
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default_shop.php 6147 2012-06-22 13:45:47Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<fieldset>
    <legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOP_SETTINGS') ?></legend>
    <table class="admintable">
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_SHOP_OFFLINE','shop_is_offline',VmConfig::get('shop_is_offline',0) ); ?>
		<tr>
			<td class="key"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOP_OFFLINE_MSG') ?></td>
			<td>
			<textarea rows="6" cols="50" name="offline_message" class="input-block"><?php echo VmConfig::get('offline_message','Our Shop is currently down for maintenance. Please check back again soon.'); ?></textarea>
			</td>
		</tr>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_USE_ONLY_AS_CATALOGUE','use_as_catalog',VmConfig::get('use_as_catalog',0) ); ?>
		<?php echo VmHTML::row('raw','COM_VIRTUEMART_CFG_CURRENCY_MODULE',
			JHTML::_('Select.genericlist', $this->currConverterList, 'currency_converter_module', 'size=1', 'value', 'text', VmConfig::get('currency_converter_module','convertECB.php'))
			); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_ENABLE_CONTENT_PLUGIN','enable_content_plugin',VmConfig::get('enable_content_plugin',0) ); ?>

	    <?php	/* <tr>
	<td class="key">
		<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_DATEFORMAT_EXPLAIN'); ?>">

<?php	/* <tr>
	<td class="key">
		<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_DATEFORMAT_EXPLAIN'); ?>">
		<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_DATEFORMAT') ?>
		</span>
		</td>
		<td>
		<input type="text" name="dateformat" class="inputbox input-mini" value="<?php echo VmConfig::get('dateformat') ?>" />
	</td>
	</tr> */ ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_SSL','useSSL',VmConfig::get('useSSL',0) ); ?>
	</table>
</fieldset>

<fieldset>
	<legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOP_LANGUAGES') ?></legend>
	<table class="admintable">
		<?php echo VmHTML::row('raw','COM_VIRTUEMART_ADMIN_CFG_MULTILANGUE',$this->activeLanguages) ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_ENABLE_ENGLISH','enableEnglish',VmConfig::get('enableEnglish',1) ); ?>

	</table>
</fieldset>

<fieldset>
	<legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOP_EMAILS') ?></legend>
	<table class="admintable">
		
		<?php 
		$options = array(
			'0'	=>	JText::_('COM_VIRTUEMART_ADMIN_CFG_MAIL_FORMAT_TEXT'),
			'1'	=>	JText::_('COM_VIRTUEMART_ADMIN_CFG_MAIL_FORMAT_HTML')
		);
			echo VmHTML::row('radioListGroup','COM_VIRTUEMART_ADMIN_CFG_MAIL_FORMAT','order_mail_html', VmConfig::get('order_mail_html','0'),$options ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_MAIL_USEVENDOR','useVendorEmail',VmConfig::get('useVendorEmail',0) ); ?>

		<!-- NOT YET -->
	    <!--tr>
		    <td class="key">
			<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MAIL_FROM_RECIPIENT_EXPLAIN'); ?>">
			<label for="mail_from_recipient"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MAIL_FROM_RECIPIENT') ?></span>
			    </span>
		    </td>
		    <td>
			    <?php echo VmHTML::checkbox('mail_from_recipient', VmConfig::get('mail_from_recipient',0)); ?>
		    </td>
	    </tr>
	    <tr>
		    <td class="key">
			<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MAIL_FROM_SETSENDER_EXPLAIN'); ?>">
			<label for="mail_from_setsender"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MAIL_FROM_SETSENDER') ?></span>
			    </span>
		    </td>
		    <td>
			    <?php echo VmHTML::checkbox('mail_from_setsender', VmConfig::get('mail_from_setsender',0)); ?>
		    </td>
	    </tr -->

	</table>
</fieldset>

<fieldset>
	<legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOP_ADVANCED') ?></legend>
	<table class="admintable">
		<?php 
				$options = array(
					'none'	=>	JText::_('COM_VIRTUEMART_ADMIN_CFG_ENABLE_DEBUG_NONE'),
					'admin'	=>	JText::_('COM_VIRTUEMART_ADMIN_CFG_ENABLE_DEBUG_ADMIN'),
					'all'	=> JText::_('COM_VIRTUEMART_ADMIN_CFG_ENABLE_DEBUG_ALL')
				);
			echo VmHTML::row('radioListGroup','COM_VIRTUEMART_ADMIN_CFG_ENABLE_DEBUG','debug_enable', VmConfig::get('debug_enable','none'),$options ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_DANGEROUS_TOOLS','dangeroustools',VmConfig::get('dangeroustools') ); ?>
		<?php 
					$options = array(
						'none'	=>	JText::_('COM_VIRTUEMART_ADMIN_CFG_ENABLE_MULTIX_NONE'),
						'admin'	=>	JText::_('COM_VIRTUEMART_ADMIN_CFG_ENABLE_MULTIX_ADMIN')
					);
			echo VmHTML::row('radioListGroup','COM_VIRTUEMART_ADMIN_CFG_ENABLE_MULTIX','multix', VmConfig::get('multix','none'),$options ); ?>

    </table>
</fieldset>
