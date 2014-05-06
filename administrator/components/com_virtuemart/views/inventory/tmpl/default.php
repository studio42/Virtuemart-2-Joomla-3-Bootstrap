<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 6307 2012-08-07 07:39:45Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php AdminUIHelper::startAdminArea(); ?>

	<div id="filter-bar" class="btn-toolbar">
		<?php echo $this->displayDefaultViewSearch('COM_VIRTUEMART_NAME','filter_product') ?>
		<div class="btn-group pull-right"><?php echo $this->pagination->getLimitBox(); ?></div>
		<div class="btn-group pull-right">
			<?php echo $this->lists['stockfilter'] ?>
			<?php // echo $this->DisplayFilterPublish() ?>
			
		</div>
	</div>
	<div id="results">
		<?php 
		// split to use ajax search
		echo $this->loadTemplate('results'); ?>
	</div>
	<?php AdminUIHelper::endAdminArea(true); ?>
</form>
<!-- updateStockModal -->
<div id="updateStockModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
    <h3><?php echo jText::_('COM_VIRTUEMART_PRODUCT') ?> : <span></span></h3>
  </div>
  <div class="modal-body">
	
    <form class="form-horizontal">
		<label><?php echo jText::_('COM_VIRTUEMART_PRODUCT_FORM_IN_STOCK') ?></label>
		<input type="text" name="product_in_stock" id="in_stock" value="0">
		<input type="hidden" name="virtuemart_product_id" id="virtuemart_product_id" value="0">
		<?php echo $this->addStandardHiddenToForm(null,'updatestock');  ?>
	</form>
  </div>
  <div class="modal-footer">
    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><?php echo jText::_('COM_VIRTUEMART_CANCEL') ?></button>
    <button type="button" class="btn btn-primary" id="apply_stock"><?php echo jText::_('COM_VIRTUEMART_APPLY') ?></button>
  </div>
</div>
<script>
jQuery(function(){
	var $el,
		form = jQuery('#updateStockModal form');
	jQuery('#adminForm').on('click','.updateStock', function(){
		var $modal = jQuery('#updateStockModal');
		$el = jQuery(this);
		$modal.find('h3 span').text( $el.attr('data-title') );
		$modal.find('#virtuemart_product_id').val( $el.attr('data-id') );
		$modal.find('input#in_stock').val( $el.children().text() );
	});
	form.submit( function(e){
		e.preventDefault();
		return false;
	});
	jQuery('#apply_stock').click( function(){
		
		inputs = form.serialize();
		jQuery.post( 'index.php', inputs+'&format=json',
			function(data, status) {
				$el.children().text( jQuery('#in_stock').val() );
				jQuery('#updateStockModal').modal('hide');
				// console.log(data);
				var $alert =jQuery('<div class="alert '+data.type+' fade in">'+
					'<button type="button" class="close" data-dismiss="alert">&times;</button>'+
					data.message+
					'</div>');
				jQuery('#results').before($alert);
				$alert.alert().bind('closed', function () {
					clearTimeout(t);
				});
				var t=setTimeout(function(){$alert.alert('close')},3000);
				Joomla.ajaxSearch(this);
				// $el.data('task', task.replace(val,valNew) );
				// if (data.type !== 'alert-error')
				// $img.toggleClass('icon-'+text.img[val]+' icon-'+text.img[valNew]); //attr('src', src.replace(text.img[val],text.img[valNew]) );
				// f.task.value = oldTask;
			}
			, "json" )
			.fail(function() {
				location.reload();
			});
		return false;
	});
});
</script>
