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
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_SEO_DISABLE','seo_disabled',VmConfig::get('seo_disabled',0) ); ?>
		<?php echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_SEO_SUFIX','seo_sufix',VmConfig::get('seo_sufix','-detail'),'class="input-mini"' ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_SEO_TRANSLATE','seo_translate',VmConfig::get('seo_translate',1) ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_SEO_USE_ID','seo_use_id',VmConfig::get('seo_use_id',1) ); ?>
	</table>
</fieldset>