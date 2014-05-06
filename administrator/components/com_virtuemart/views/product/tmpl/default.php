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
* @version $Id: product.php 3304 2011-05-20 06:57:27Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/* Load some variables */
$search_date = JRequest::getVar('search_date', null); // Changed search by date
$now = getdate();
$nowstring = $now["hours"].":".substr('0'.$now["minutes"], -2).' '.$now["mday"].".".$now["mon"].".".$now["year"];
$search_order = JRequest::getVar('search_order', '>');
$search_type = JRequest::getVar('search_type', 'product');
// OSP in view.html.php $virtuemart_category_id = JRequest::getInt('virtuemart_category_id', false);
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php AdminUIHelper::startAdminArea(); ?>
	<div id="filter-bar" class="btn-toolbar">
		<?php echo $this->displayDefaultViewSearch ('COM_VIRTUEMART_PRODUCT_LIST_SEARCH_BY_DATE', 'filter_product') ?>
		<div class="btn-group pull-right"><?php echo $this->pagination->getLimitBox(); ?></div>
		<span class="searchbydate" style="display: inline-block;"><div class="btn-group pull-right form-horizontal"><?php echo vmJsApi::jDate(JRequest::getVar('search_date', $nowstring), 'search_date'); ?></div>
		<div class="btn-group pull-right"><?php echo $this->lists['search_order']; ?></div></span>
		
		<div class="clearfix clear"> </div>
		<div class="btn-group pull-left">
			<select id="virtuemart_category_id" name="virtuemart_category_id" onchange="Joomla.ajaxSearch(this); return false;">
				<option value=""><?php echo JText::sprintf( 'COM_VIRTUEMART_SELECT' ,  JText::_('COM_VIRTUEMART_CATEGORY')) ; ?></option>
				<?php echo $this->category_tree; ?>
			</select>
		</div>
		<div class="btn-group pull-left">
			 <?php echo JHTML::_('select.genericlist', $this->manufacturers, 'virtuemart_manufacturer_id', 'class="inputbox" onchange="Joomla.ajaxSearch(this); return false;"', 'value', 'text',
				$this->model->virtuemart_manufacturer_id );
			?>
		</div>
		<div class="btn-group pull-left"><?php echo $this->lists['search_type']; ?></div>

	</div>
	<div class="clearfix clear"> </div>
	<div id="results">
		<?php 
		// split to use ajax search
		echo $this->loadTemplate('results'); ?>
	</div>


<?php AdminUIHelper::endAdminArea(true); ?>
<script type="text/javascript">
    <!--
<?php 
	$jsons = array (
			1	=> array(JText::_('COM_VIRTUEMART_DISABLED'),JText::_('COM_VIRTUEMART_ENABLE_ITEM') ),
			0	=> array(JText::_('COM_VIRTUEMART_FEATURED'),JText::_('COM_VIRTUEMART_DISABLE_ITEM') )
		);
?>
	Joomla.featuredJson = function(el, id) {

		var text = <?php echo json_encode($jsons) ?>,
			$el = jQuery(el),
			task = $el.data('task');
			f = document.adminForm,
			cb = f[id],
			$btn = $el.children('i'),
			form = jQuery('#adminForm'),
			url = form.attr('action'),
			val = task.charAt( task.length-1 ),
			valNew = 0;
		//console.log(text);
		//get the toggle value
		if (val == 0 ) valNew = 1;
		if (cb) {
			for (var i = 0; true; i++) {
				var cbx = f['cb'+i];
				if (!cbx)
					break;
				cbx.checked = false;
			} // for
			cb.checked = true;
			f.boxchecked.value = 1;
		}
		$el.tooltip('destroy');
		$el.attr('data-original-title', text[0][val] ).tooltip();
		f.task.value= task;

		inputs = form.serialize();
		jQuery.post( url, inputs+'&format=json',
			function(data, status) {
				// console.log(data);
				var $alert =jQuery('<div class="alert '+data.type+' fade in">'+
					'<button type="button" class="close" data-dismiss="alert">&times;</button>'+
					data.message+' ('+text[1][val]+')'+
					'</div>');
				jQuery('#results').before($alert);
				$alert.alert().bind('closed', function () {
					// do something…
					clearTimeout(t);
				});
				var t=setTimeout(function(){$alert.alert('close')},5000);
				$el.data('task', task.replace(val,valNew) );
				$btn.toggleClass('icon-star-empty icon-star');
			}, "json" )
			.fail(function() {
			location.reload();
		});
		return false;
	}
	jQuery(function($){
		$('#search_type').change(function(){
			var selected = $(this).val() ;
			if ( selected === 'product' || selected ==='price') 
				 $('.searchbydate').show();
			else $('.searchbydate').hide();
			
		
		});
	});
 -->
</script>
</form>
  <style type="text/css">
.thumbnail {
    width: 48px;
}

  </style>

<?php 
// DONE BY stephanbais
/// DRAG AND DROP PRODUCT ORDER HACK
if ($this->virtuemart_category_id ) { ?>
	<script>
		jQuery(function() {

			jQuery( ".adminlist" ).sortable({
				handle: ".vmicon-16-move",
				items: 'tr:not(:first,:last)',
				opacity: 0.8,
				update: function() {
					var i = 1;
					jQuery(function updatenr(){
						jQuery('input.ordering').each(function(idx) {
							jQuery(this).val(idx);
						});
					});

					jQuery(function updaterows() {
						jQuery(".order").each(function(index){
							var row = jQuery(this).parent('td').parent('tr').prevAll().length;
							jQuery(this).val(row);
							i++;
						});

					});
				}

			});
		});

		//jQuery('input.ordering').css({'color': '#666666', 'background-color': 'transparent','border': 'none' }).attr('readonly', true);
</script>

<?php }


/// END PRODUCT ORDER HACK
?>