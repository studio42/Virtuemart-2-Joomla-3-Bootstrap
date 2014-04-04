<?php
/**
*
* Handle the Product Custom Fields
*
* @package	VirtueMart
* @subpackage Product
* @author RolandD, Patrick khol, Valérie Isaksen
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: product_edit_waitinglist.php 2978 2011-04-06 14:21:19Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
if (isset($this->product->customfields_fromParent)) { ?>
	<label><?php echo JText::_('COM_VIRTUEMART_CUSTOM_SAVE_FROM_CHILD');?><input type="checkbox" name="save_customfields" value="1" /></label>
<?php } else {
	?> <input type="hidden" name="save_customfields" value="1" />
<?php }  ?>
<div id="customfieldsTable" width="100%">
	<?php
			$i=0;
			$tables= array('categories'=>'','products'=>'','fields'=>'','cart'=>'');
			if (isset($this->product->customfields)) {
				foreach ($this->product->customfields as $customfield) {
					// if ($customfield->is_cart_attribute) $cartIcone=  'default';
					// else  $cartIcone= 'default-off';
					if ($customfield->field_type == 'Z') {
						// R: related categories
						$tables['categories'] .=  '
							<div class="vm_thumb_image">
								<span>'.$customfield->display.'</span>'.
								VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i)
							  .'<div class="icon-remove"></div>
							</div>';

					} elseif ($customfield->field_type == 'R') {
					// R: related products
						$tables['products'] .=  '
							<div class="vm_thumb_image">
								<span>'.$customfield->display.'</span>'.
								VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i)
							  .'<div class="icon-remove"></div>
							</div>';

					} elseif ($customfield->field_type == 'G') {
						// no display (group of) child , handled by plugin;
					} else {
						if ($customfield->custom_tip) $tip = ' class="hasTooltip" title="'.$customfield->custom_tip.'"';
						else $tip ='';
						// make 2 table. Cart options and datas
						$tbName = $customfield->is_cart_attribute ? 'cart' : 'fields' ;
						$tables[$tbName] .= '<tr class="removable">
							<td><div '.$tip.'>'.JText::_($customfield->custom_title).'<div>'.
								($customfield->custom_field_desc ? '<small>'.$customfield->custom_field_desc.'</small>' : '').'
							</td>
							<td>'.$customfield->display.'</td>
							<td>
							'.JText::_($this->fieldTypes[$customfield->field_type]).'
							'.VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i).'
							</td>
							<td><span class="icon-remove"></span><input class="ordering" type="hidden" value="'.$customfield->ordering.'" name="field['.$i .'][ordering]" /></td>
							<td ><span class="icon-move"></span></td>
						 </tr>';
						}

					$i++;
				}
			}

			 $emptyTable = '
				<tr class="custom-empty">
					<td colspan="7">'.JText::_( 'COM_VIRTUEMART_CUSTOM_NO_TYPES').'</td>
				<tr>';
			?>
			<fieldset>
				<legend><?php echo JText::_('COM_VIRTUEMART_RELATED_CATEGORIES'); ?></legend>
				<?php echo JText::_('COM_VIRTUEMART_CATEGORIES_RELATED_SEARCH'); ?>
				<div class="jsonSuggestResults input-append" style="width: 100%" id="relatedcategories-div">
					<input type="text" size="40" name="search" id="relatedcategoriesSearch" value="" />
					<button class="reset-value btn"><?php echo JText::_('COM_VIRTUEMART_RESET') ?></button>
				</div>
				<div id="custom_categories"><?php echo  $tables['categories']; ?></div>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('COM_VIRTUEMART_RELATED_PRODUCTS'); ?></legend>
				<?php echo JText::_('COM_VIRTUEMART_PRODUCT_RELATED_SEARCH'); ?>
				<div class="jsonSuggestResults input-append" style="width: 100%" id="relatedproducts-div">
					<input type="text" size="40" name="search" id="relatedproductsSearch" value="" />
					<button class="reset-value btn"><?php echo JText::_('COM_VIRTUEMART_RESET') ?></button>
				</div>
				<div id="custom_products"><?php echo  $tables['products']; ?></div>
			</fieldset>

			<fieldset >
				<legend><?php echo JText::_('COM_VIRTUEMART_CUSTOM_FIELD_TYPE' );?></legend>
				<div class="inline"><?php echo  $this->customsList; ?></div>
				<h3><?php echo JText::_('COM_VIRTUEMART_CUSTOM' );?></h3>
				<table id="custom_fields" class="adminlist table table-striped" cellspacing="0" cellpadding="0">
					<thead>
					<tr>
						<th><?php echo JText::_('COM_VIRTUEMART_TITLE');?></th>
						<th colspan="2"><?php echo JText::_('COM_VIRTUEMART_VALUE');?></th>
						<th><?php echo JText::_('COM_VIRTUEMART_TYPE');?></th>
						<th width="1%"><span class="hidden-phone"><?php echo JText::_('COM_VIRTUEMART_DELETE'); ?></span></th>
						<th width="1%"><?php echo JText::_('COM_VIRTUEMART_MOVE'); ?></th>
					</tr>
					</thead>
					<tbody id="custom_field">
						<?php
						if ($tables['fields']) echo $tables['fields'] ;
						else echo $emptyTable;
						?>
					</tbody>
				</table>
				<!-- custom_fields cart-->
				<h3><?php echo JText::_('COM_VIRTUEMART_CUSTOM_IS_CART_ATTRIBUTE');?></h3>
				<table id="cart_attributes" class="adminlist table table-striped" cellspacing="0" cellpadding="0">
					<thead>
					<tr>
						<th><?php echo JText::_('COM_VIRTUEMART_TITLE');?></th>
						<th><?php echo JText::_('COM_VIRTUEMART_VALUE');?></th>
						<th><?php echo JText::_('COM_VIRTUEMART_CART_PRICE');?></th>
						<th><?php echo JText::_('COM_VIRTUEMART_TYPE');?></th>
						<th width="1%"><span class="hidden-phone"><?php echo JText::_('COM_VIRTUEMART_DELETE'); ?></span></th>
						<th width="1%"><?php echo JText::_('COM_VIRTUEMART_MOVE'); ?></th>
					</tr>
					</thead>
					<tbody id="cart_attribute">
						<?php
						if ($tables['cart']) echo $tables['cart'] ;
						else echo $emptyTable;
						?>
					</tbody>
				</table><!-- custom_fields -->
			</fieldset>

<div style="clear:both;"></div>
</div>

<script type="text/javascript">
	nextCustom = <?php echo $i ?>;

	jQuery(document).ready(function(){
		jQuery('#custom_field,#cart_attribute').sortable({handle: ".icon-move"});
		// Need to declare the update routine outside the sortable() function so
		// that it can be called when adding new customfields
		jQuery('#custom_field,#cart_attribute').bind('sortupdate', function(event, ui) {
			jQuery(this).find('.ordering').each(function(index,element) {
				jQuery(element).val(index);
				//console.log(index+' ');

			});
		});
	});
	jQuery('select#customlist').chosen().change(function() {
		selected = jQuery(this).find( 'option:selected').val() ;
		jQuery.getJSON('<?php echo $this->jsonPath ?>index.php?option=com_virtuemart&tmpl=component&view=product&task=getData&format=json&type=fields&id='+selected+'&row='+nextCustom+'&virtuemart_product_id=<?php echo $this->product->virtuemart_product_id; ?>',
		function(data) {
			
			jQuery.each(data.value, function(index, value){
				jQuery("#"+index+' .custom-empty').remove();
				jQuery("#"+index).append(value).find('.hasTooltip').tooltip();
				jQuery('#'+index).trigger('sortupdate');
			});
		});
		nextCustom++;
	});

		jQuery('input#relatedproductsSearch').autocomplete({

		source: 'index.php?option=com_virtuemart&view=product&task=getData&tmpl=component&format=json&type=relatedproducts&row='+nextCustom,
		select: function(event, ui){
			jQuery("#custom_products").append(ui.item.label);
			nextCustom++;
			return false;
		},
		appendTo: "#relatedproducts-div",
		minLength:1,
		html: true
	});
	jQuery('input#relatedcategoriesSearch').autocomplete({

		source: 'index.php?option=com_virtuemart&view=product&task=getData&tmpl=component&format=json&type=relatedcategories&row='+nextCustom,
		select: function(event, ui){
			jQuery("#custom_categories").append(ui.item.label);
			nextCustom++;
			return false;
		},
		appendTo: "#relatedcategories-div",
		minLength:1,
		html: true
	});
	jQuery('#relatedproducts-div,#relatedcategories-div').delegate('a','click',function() { return false });
	// jQuery('#customfieldsTable').delegate('td','click', function() {
		// jQuery('#customfieldsParent').remove();
		// jQuery(this).undelegate('td','click');
	// });
	// jQuery.each(jQuery('#customfieldsTable').filter(":input").data('events'), function(i, event) {
		// jQuery.each(event, function(i, handler){
		// console.log(handler);
	  // });
	// });
	jQuery('#adminForm').on('click','.removable .icon-remove',function() {
		var toRemove = jQuery(this).closest('.removable'); main = toRemove.parent();
		if (main.attr('id') == 'pricesort' && main.children('.removable').length == 1 ) return;
		else console.log(main.attr('id'));
		jQuery(this).closest('.removable').fadeOut(  function() {
			// Animation complete.
			$(this).remove();
		});
	});

eventNames = "click.remove keydown.remove change.remove focus.remove"; // all events you wish to bind to

function removeParent() {jQuery('#customfieldsParent').remove();console.log($(this));//jQuery('#customfieldsTable input').unbind(eventNames, removeParent)
 }

// jQuery('#customfieldsTable input').bind(eventNames, removeParent);

  // jQuery('#customfieldsTable').delegate('*',eventNames,function(event) {
    // var $thisCell, $tgt = jQuery(event.target);
	// console.log (event);
	// });
		jQuery('#customfieldsTable').find('input').each(function(i){
			current = jQuery(this);
        // var dEvents = curent.data('events');
        // if (!dEvents) {return;}

		current.click(function(){
				jQuery('#customfieldsParent').remove();
			});
		//console.log (curent);
        // jQuery.each(dEvents, function(name, handler){
            // if((new RegExp('^(' + (events === '*' ? '.+' : events.replace(',','|').replace(/^on/i,'')) + ')$' ,'i')).test(name)) {
               // jQuery.each(handler, function(i,handler){
                   // outputFunction(elem, '\n' + i + ': [' + name + '] : ' + handler );


               // });
           // }
        // });
    });


	//onsole.log(jQuery('#customfieldsTable').data('events'));

</script>