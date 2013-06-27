<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Config
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default_seo.php 2387 2010-05-05 16:24:59Z oscar $
*/
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');  
?>
<br />
<fieldset>
    <legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SEO_SETTINGS') ?></legend>
    <table class="admintable">
	<tr>
	    <td class="key">		 
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SEO_DISABLE_TIP'); ?>">
		<label for="seo_disabled"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SEO_DISABLE') ?></label>
		</span>
	    </td>
	    <td>
		<?php echo VmHTML::checkbox('seo_disabled', VmConfig::get('seo_disabled',0)); ?>
	    </td>
	</tr>	<tr>
	    <td class="key">		 
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SEO_SUFIX_TIP'); ?>">
			<label for="seo_disabled"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SEO_SUFIX') ?></label>
		</span>
	    </td>
	    <td>
			<input type="text" name="seo_sufix" class="inputbox" value="<?php echo VmConfig::get('seo_sufix','-detail') ?>" />
	    </td>
	</tr>
	<tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SEO_TRANSLATE_TIP'); ?>">
		<label for="seo_translate"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SEO_TRANSLATE') ?></label>
		</span>
	    </td>
		<td>
			<?php echo VmHTML::checkbox('seo_translate', VmConfig::get('seo_translate',1)); ?>
		</td>
	</tr>
	<tr>
	    <td class="key">		
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SEO_USE_ID_TIP'); ?>">
		<label for="seo_use_id"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SEO_USE_ID') ?></label>
		</span>
	    </td>
		<td>
			<?php echo VmHTML::checkbox('seo_use_id', VmConfig::get('seo_use_id')); ?>
		</td>
	</tr>
 </table>
</fieldset>