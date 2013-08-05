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
* @version $Id: default_tools.php 4007 2011-08-31 07:31:35Z alatak $
*/

$session = JFactory::getSession();

?>
<form action="index.php" class="form-horizontal" method="post" name="adminForm" enctype="multipart/form-data" >
<input type="hidden" name="task" value="" />

		<h3> <?php echo JText::_('COM_VIRTUEMART_UPDATE_MIGRATION_TITLE'); ?> </h3>
		<?php if (!class_exists('ShopFunctions')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');

		$max_execution_time = ini_get('max_execution_time');
		// echo 'max_execution_time '.$max_execution_time;
		// echo '<br />';
		@ini_set( 'max_execution_time', $max_execution_time+1 );
		$new_max_execution_time = ini_get('max_execution_time');
		if($max_execution_time===$new_max_execution_time){
			echo 'Server settings do not allow changes of your max_execution_time in the php.ini file, you may get problems migrating a big shop';
		} else { ?>
			<div class="control-group">
				<label class="control-label" for="max_execution_time">
					<?php echo JText::_('COM_VIRTUEMART_UPDATE_MIGRATION_CHANGE_MAX_EXECUTION_TIME') ?>
				</label>
				<input id="max_execution_time" class="inputbox" type="text" name="max_execution_time" size="15" value="<?php echo $max_execution_time ?>" />
			<div>
			<?php
		}
		@ini_set( 'max_execution_time', $max_execution_time );

		echo '<br />';
		$memory_limit = ini_get('memory_limit');
		// echo 'memory_limit '.$memory_limit;
		// echo '<br />';
		if($memory_limit!=='128MB'){

// 			@ini_set( 'memory_limit', '128MB' );
// 			$new_memory_limit = ini_get('memory_limit');
// 			if($memory_limit===$new_memory_limit){
// 				echo 'Server settings do not allow changes of your memory_limit in the php.ini file, you may get problems migrating a big shop';
// 			}else {
			?>
			<div class="control-group">
				<label class="control-label" for="memory_limit">
					<?php echo JText::_('COM_VIRTUEMART_UPDATE_MIGRATION_CHANGE_MEMORY_LIMIT') ?>
				</label>
				<input id="memory_limit" class="inputbox" type="text" name="memory_limit" size="15" value="<?php echo $memory_limit ?>" />
			<div>
			<?php
// 			}
// 			@ini_set( 'max_execution_time', $memory_limit );
		}
	?>
	<div class="row-fluid">
		<h4 class="span4"><?php echo JText::_('COM_VIRTUEMART_UPDATE_MIGRATION_STRING'); ?></h4>
		<div class="span8">
		<?php
		$options = array(
			'migrateGeneralFromVmOne'	=>	JText::_('COM_VIRTUEMART_UPDATE_GENERAL'),
			'migrateUsersFromVmOne'	=>	JText::_('COM_VIRTUEMART_UPDATE_USERS'),
			'migrateProductsFromVmOne'	=> JText::_('COM_VIRTUEMART_UPDATE_PRODUCTS'),
			'migrateOrdersFromVmOne'	=> JText::_('COM_VIRTUEMART_UPDATE_ORDERS'),
			'migrateAllInOne'	=> JText::_('COM_VIRTUEMART_UPDATE_ALL'),
		//	'setStoreOwner'	=> JText::_('COM_VIRTUEMART_SETSTOREOWNER')
		);
		echo VmHTML::radioList('task', $session->get('migration_task', 'migrateAllInOne', 'vm'), $options);
		?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span4">
		<div class="control-group">
			<label class="control-label" ><?php echo JText::_('COM_VIRTUEMART_MIGRATION_REWRITE_ORDER_NUMBER'); ?></label>
			<?php echo VmHTML::checkbox('reWriteOrderNumber', $session->get('reWriteOrderNumber', 1, 'vm')); ?>
		</div>
		</div>
		<div class="span8">
		<div class="control-group">
			<label class="control-label" ><?php echo JText::_('COM_VIRTUEMART_MIGRATION_USER_ORDER_ID'); ?></label>
			<?php echo VmHTML::checkbox('userOrderId', $session->get('userOrderId', 0, 'vm')); ?>
		</div>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" ><?php echo JText::_('COM_VIRTUEMART_MIGRATION_DCAT_BROWSE'); ?></label>
		<input class="inputbox" type="text" name="migration_default_category_browse" size="15" value="<?php echo $session->get('migration_default_category_browse', '', 'vm') ?>" />
	</div>
	<div class="control-group">
		<label class="control-label" ><?php echo JText::_('COM_VIRTUEMART_MIGRATION_DCAT_FLY'); ?></label>
		<input class="inputbox" type="text" name="migration_default_category_fly" size="" value="<?php echo $session->get('migration_default_category_fly', '', 'vm') ?>" />
	</div>
	<button class="default btn btn-block" type="submit" ><?php echo JText::_('COM_VIRTUEMART_MIGRATE'); ?></button>
    <!-- Hidden Fields -->
    <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="view" value="updatesmigration" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

<form class="form-inline" action="index.php" method="post" name="adminForm" enctype="multipart/form-data" >
<input type="hidden" name="task" value="setStoreOwner" />

<h3><?php echo JText::_('COM_VIRTUEMART_SETSTOREOWNER'); ?></h3>
	<label class="control-label" >
		<?php echo JText::_('COM_VIRTUEMART_MIGRATION_STOREOWNERID'); ?>
	</label>
	<input class="inputbox" type="text" name="storeOwnerId" size="15" value="" />
	<button class="default btn" type="submit" ><?php echo JText::_('COM_VIRTUEMART_SETSTOREOWNER'); ?></button>

<!-- Hidden Fields -->
<input type="hidden" name="option" value="com_virtuemart" />
<input type="hidden" name="view" value="updatesmigration" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php /*
<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data" >
    <input type="hidden" name="task" value="reOrderChilds" />

    <table>
        <tr>
            <td align="left" colspan="5" >
                <h3> <?php echo JText::_('COM_VIRTUEMART_UPDATE_CHILD_ORDERING'); ?> </h3>
            </td>
        </tr>
        <td>
            <button class="default" type="submit" ><?php echo JText::_('COM_VIRTUEMART_GO'); ?></button>
        </td>
    </table>
    <!-- Hidden Fields -->
    <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="view" value="updatesmigration" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form> */ ?>