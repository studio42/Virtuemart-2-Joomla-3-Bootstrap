<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage UpdatesMigration
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default_tools.php 4788 2011-11-22 11:28:11Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!VmConfig::get('dangeroustools', false)){
	$uri = JFactory::getURI();
	$link = $uri->root() . 'administrator/index.php?option=com_virtuemart&view=config';
	?>

	<div class="vmquote well">
	<span style="font-weight:bold;color:green;"> <?php echo JText::sprintf('COM_VIRTUEMART_SYSTEM_DANGEROUS_TOOL_ENABLED_JS',JText::_('COM_VIRTUEMART_ADMIN_CFG_DANGEROUS_TOOLS'),$link) ?></span>
	</div>

	<?php
}

?>
<div class="row-fluid">

<?php /*	<td align="center">
		<?php $link=JROUTE::_('index.php?option=com_virtuemart&view=updatesmigration&task=installSampleData&'.JSession::getFormToken().'=1'); ?>
	    <div class="icon"><a onclick="javascript:confirmation('<?php echo JText::_('COM_VIRTUEMART_UPDATE_INSTALLSAMPLE_CONFIRM'); ?>', '<?php echo $link; ?>');">
		<span class=" pull-left vmicon48 vm_install_48"></span>
	    <br /><?php echo JText::_('COM_VIRTUEMART_SAMPLE_DATA'); ?>
		</a></div>
	</td>
	<td align="center">
	    <a href="<?php echo JROUTE::_('index.php?option=com_virtuemart&view=updatesmigration&task=userSync&'.JSession::getFormToken().'=1'); ?>">
		<span class=" pull-left vmicon48 vm_shoppers_48"></span>
	    </a>
	    <br /><?php echo JText::_('COM_VIRTUEMART_SYNC_JOOMLA_USERS'); ?>
		</a>
	</td>*/ ?>
	<div class="span6">
		<h3> <?php echo JText::_('COM_VIRTUEMART_TOOLS_SYNC_MEDIA_FILES'); ?> </h3>
		<?php $link=JROUTE::_('index.php?option=com_virtuemart&view=updatesmigration&task=portMedia&'.JSession::getFormToken().'=1' ); ?>
		<a class="icon btn btn-block btn-primary btn-large" onclick="javascript:confirmation('<?php echo JText::sprintf('COM_VIRTUEMART_UPDATE_MIGRATION_STRING_CONFIRM', JText::_('COM_VIRTUEMART_MEDIA_S')); ?>', '<?php echo $link; ?>');">
				<i class="vmicon vmicon-16-media icon-nofloat"></i><i class="vmicon vmicon-16-refresh"></i>
				<?php echo JText::_('COM_VIRTUEMART_TOOLS_SYNC_MEDIA_FILES'); ?>
				
		</a>
		<div>
			<?php echo JText::sprintf('COM_VIRTUEMART_TOOLS_SYNC_MEDIAS_EXPLAIN',VmConfig::get('media_product_path') ,VmConfig::get('media_category_path') , VmConfig::get('media_manufacturer_path')); ?>
		</div>
	</div>
	<div class="span6">
		<h3> <?php echo JText::_('COM_VIRTUEMART_TOOLS_RENEW_CONFIG'); ?> </h3>
		<?php $link=JROUTE::_('index.php?option=com_virtuemart&view=updatesmigration&task=renewConfig&'.JSession::getFormToken().'=1' ); ?>
		<a class="icon btn btn-block btn-large" onclick="javascript:confirmation('<?php echo JText::_('COM_VIRTUEMART_TOOLS_RENEW_CONFIG_CONFIRM'); ?>', '<?php echo $link; ?>');">
			<i class="vmicon vmicon-16-config icon-nofloat"></i><i class="vmicon vmicon-16-refresh"></i>
			<?php echo Jtext::_('COM_VIRTUEMART_TOOLS_RENEW_CONFIG'); ?>
			
		</a>
		<div class="span12">
			<?php echo JText::sprintf('COM_VIRTUEMART_TOOLS_RENEW_CONFIG_EXPLAIN'); ?>
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="alert">
	<?php echo JText::_('COM_VIRTUEMART_UPDATE_MIGRATION_TOOLS_WARNING'); ?>
	</div>
</div>
<div class="row-fluid">
	<?php $link=JROUTE::_('index.php?option=com_virtuemart&view=updatesmigration&task=refreshCompleteInstall&'.JSession::getFormToken().'=1' ); ?>
	<div class="span6">
		<?php $link=JROUTE::_('index.php?option=com_virtuemart&view=updatesmigration&task=updateDatabase&'.JSession::getFormToken().'=1' ); ?>
	    <a class="icon btn btn-block btn-large btn-warning"  onclick="javascript:confirmation('<?php echo addslashes( JText::_('COM_VIRTUEMART_UPDATEDATABASE_CONFIRM_JS') ); ?>', '<?php echo $link; ?>');">
			<i class="vmicon vmicon-16-database_gear icon-nofloat"></i>
            <?php echo Jtext::_('COM_VIRTUEMART_UPDATEDATABASE'); ?>
		</a>
		<a class="icon btn btn-block btn-large btn-warning btn-danger"  onclick="javascript:confirmation('<?php echo addslashes( JText::_('COM_VIRTUEMART_DELETES_ALL_VM_TABLES_AND_FRESH_CONFIRM_JS') ); ?>', '<?php echo $link; ?>');">
			<i class="vmicon vmicon-16-database_gear icon-nofloat"></i>
	            <?php echo Jtext::_('COM_VIRTUEMART_DELETES_ALL_VM_TABLES_AND_FRESH'); ?>
		</a>

			<?php $link=JROUTE::_('index.php?option=com_virtuemart&view=updatesmigration&task=refreshCompleteInstallAndSample&'.JSession::getFormToken().'=1' ); ?>
	    <a class="icon btn btn-block btn-large btn-danger"  onclick="javascript:confirmation('<?php echo addslashes( JText::_('COM_VIRTUEMART_DELETES_ALL_VM_TABLES_AND_SAMPLE_CONFIRM_JS') ); ?>', '<?php echo $link; ?>');">
		<i class="vmicon vmicon-16-database_gear icon-nofloat"></i>
            <?php echo Jtext::_('COM_VIRTUEMART_DELETES_ALL_VM_TABLES_AND_SAMPLE'); ?>
		</a>
</br>

	</div>
	<div class="span6">
		<?php $link=JROUTE::_('index.php?option=com_virtuemart&view=updatesmigration&task=restoreSystemDefaults&'.JSession::getFormToken().'=1'); ?>
	    <a class="icon btn btn-block btn-large btn-danger"  onclick="javascript:confirmation('<?php echo addslashes( JText::_('COM_VIRTUEMART_UPDATE_RESTOREDEFAULTS_CONFIRM_JS') ); ?>', '<?php echo $link; ?>');">
			<i class="vmicon vmicon-16-cog icon-nofloat"></i>
			<?php echo JText::_('COM_VIRTUEMART_UPDATE_RESTOREDEFAULTS'); ?>
		</a>
		<?php $link=JROUTE::_('index.php?option=com_virtuemart&view=updatesmigration&task=deleteVmData&'.JSession::getFormToken().'=1'); ?>
	    <a class="icon btn btn-block btn-large btn-danger"  onclick="javascript:confirmation('<?php echo addslashes( JText::_('COM_VIRTUEMART_UPDATE_REMOVEDATA_CONFIRM_JS') ); ?>', '<?php echo $link; ?>');">
			<i class="vmicon vmicon-16-database_gear icon-nofloat"></i>
			<?php echo Jtext::_('COM_VIRTUEMART_UPDATE_REMOVEDATA'); ?>
		</a>
		<?php $link=JROUTE::_('index.php?option=com_virtuemart&view=updatesmigration&task=deleteVmTables&'.JSession::getFormToken().'=1' ); ?>
	    <a class="icon btn btn-block btn-large btn-danger"  onclick="javascript:confirmation('<?php echo addslashes( JText::_('COM_VIRTUEMART_UPDATE_REMOVETABLES_CONFIRM_JS') ); ?>', '<?php echo $link; ?>');">
			<i class="vmicon vmicon-16-database_gear icon-nofloat"></i>
            <?php echo Jtext::_('COM_VIRTUEMART_UPDATE_REMOVETABLES'); ?>
		</a>
	</div>
</div>
<?php
 if ( $this->analyse ) {
?>
<div>
<?php
 echo $this->analyse;
?>
</div>
<?php } ?>
<script type="text/javascript">
<!--
function confirmation(message, destnUrl) {
	var answer = confirm(message);
	if (answer) {
		window.location = destnUrl;
	}
}
//-->
</script>