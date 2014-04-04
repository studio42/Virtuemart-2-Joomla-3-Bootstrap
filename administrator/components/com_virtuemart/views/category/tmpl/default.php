<?php
/**
*
* Lists all the categories in the shop
*
* @package	VirtueMart
* @subpackage Category
* @author RickG, jseros, RolandD, Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 6477 2012-09-24 14:33:54Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if (!class_exists ('shopFunctionsF'))
	require(JPATH_VM_SITE . DS . 'helpers' . DS . 'shopfunctionsf.php');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php AdminUIHelper::startAdminArea(); ?>

	<div id="filter-bar" class="btn-toolbar">
		<?php echo $this->displayDefaultViewSearch() ?>
		<?php echo $this->DisplayFilterPublish() ?>
	</div>
	<div class="clearfix"> </div>
	<div id="results">
		<?php 
		// split to use ajax search
		echo $this->loadTemplate('results'); ?>
	</div>
	<?php AdminUIHelper::endAdminArea(true); ?>
</form>
<?php if ($this->task == 'massxref_cats') { ?>
<script type="text/javascript">
<!--
		Joomla.submitbutton = function(pressbutton) {
			if (pressbutton == 'massxref_cats_exe' || pressbutton == 'massxref_cats_add') {
				var text = "<?php echo jText::_('COM_VIRTUEMART_CATEGORY_S',true) ?>",
					f = document.adminForm,
					oldTask = f.task.value,
					url = jQuery('#adminForm').attr('action'), inputs ;	
					f.task.value = pressbutton;
					inputs = jQuery(f).serialize();
				jQuery.post( url, inputs+'&format=json',
					function(data, status) {
						// console.log(data);
						var $alert =jQuery('<div class="alert '+data.type+' fade in">'+
							'<button type="button" class="close" data-dismiss="alert">&times;</button>'+
							data.message+' ('+text+')'+
							'</div>');
						jQuery('#results').before($alert);
						$alert.alert().bind('closed', function () {
							clearTimeout(t);
						});
						var t=setTimeout(function(){$alert.alert('close')},5000);
						f.task.value = oldTask;

					}
					, "json" );
				return false;

			} else {
				Joomla.submitform( pressbutton );
				return;
			}
		}
-->
</script>
<?php } ?>