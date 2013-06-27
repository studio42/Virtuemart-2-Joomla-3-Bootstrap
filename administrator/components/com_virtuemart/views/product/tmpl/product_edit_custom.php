<?php
/**
*
* Handle the waitinglist
*
* @package	VirtueMart
* @subpackage Product
* @author RolandD
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
<table id="customfieldsTable" width="100%">
	<tr>
		<td valign="top" width="%100">

		<?php
			$i=0;
			$tables= array('categories'=>'','products'=>'','fields'=>'','customPlugins'=>'',);
			if (isset($this->product->customfields)) {
				foreach ($this->product->customfields as $customfield) {
					if ($customfield->is_cart_attribute) $cartIcone=  'default';
					else  $cartIcone= 'default-off';
					if ($customfield->field_type == 'Z') {

						$tables['categories'] .=  '
							<div class="vm_thumb_image">
								<span>'.$customfield->display.'</span>'.
								VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i)
							  .'<div class="vmicon vmicon-16-remove"></div>
							</div>';

					} elseif ($customfield->field_type == 'R') {

						$tables['products'] .=  '
							<div class="vm_thumb_image">
								<span>'.$customfield->display.'</span>'.
								VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i)
							  .'<div class="vmicon vmicon-16-remove"></div>
							</div>';

					} elseif ($customfield->field_type == 'G') {
						// no display (group of) child , handled by plugin;
					} elseif ($customfield->field_type == 'E'){
						$tables['fields'] .= '<tr class="removable">
							<td>'.JText::_($customfield->custom_title).'</td>
							<td>'.$customfield->custom_tip.'</td>
							<td>'.$customfield->display.'</td>'.
							VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i)
							.'</td>
							<td>'.JText::_('COM_VIRTUEMART_CUSTOM_EXTENSION').'</td>
							<td>
							<span class="vmicon vmicon-16-'.$cartIcone.'"></span>
							</td>
							<td><span class="vmicon vmicon-16-remove"></span><input class="ordering" type="hidden" value="'.$customfield->ordering.'" name="field['.$i .'][ordering]" /></td>
						 </tr>';
						/*$tables['fields'] .= '
							<tr class="removable">
								<td>'.JText::_($customfield->custom_title).'</td>
								<td colspan="3"><span>'.$customfield->display.$customfield->custom_tip.'</span>'.
								VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i)
							  .'</td><span class="vmicon icon-nofloat vmicon-16-'.$cartIcone.'"></span>
								<span class="vmicon vmicon-16-remove"></span>
							</tr>';*/
					} else {
						$tables['fields'] .= '<tr class="removable">
							<td>'.JText::_($customfield->custom_title).'</td>
							<td>'.$customfield->custom_tip.'</td>
							<td>'.$customfield->display.'</td>
							<td>'.JText::_($this->fieldTypes[$customfield->field_type]).
							VirtueMartModelCustomfields::setEditCustomHidden($customfield, $i)
							.'</td>
							<td>
							<span class="vmicon vmicon-16-'.$cartIcone.'"></span>
							</td>
							<td><span class="vmicon vmicon-16-remove"></span><input class="ordering" type="hidden" value="'.$customfield->ordering.'" name="field['.$i .'][ordering]" /></td>
						 </tr>';
						}

					$i++;
				}
			}

			 $emptyTable = '
				<tr>
					<td colspan="7">'.JText::_( 'COM_VIRTUEMART_CUSTOM_NO_TYPES').'</td>
				<tr>';
			?>
			<fieldset style="background-color:#F9F9F9;">
				<legend><?php echo JText::_('COM_VIRTUEMART_RELATED_CATEGORIES'); ?></legend>
				<?php echo JText::_('COM_VIRTUEMART_CATEGORIES_RELATED_SEARCH'); ?>
				<div class="jsonSuggestResults" style="width: auto;">
					<input type="text" size="40" name="search" id="relatedcategoriesSearch" value="" />
					<button class="reset-value"><?php echo JText::_('COM_VIRTUEMART_RESET') ?></button>
				</div>
				<div id="custom_categories"><?php echo  $tables['categories']; ?></div>
			</fieldset>
			<fieldset style="background-color:#F9F9F9;">
				<legend><?php echo JText::_('COM_VIRTUEMART_RELATED_PRODUCTS'); ?></legend>
				<?php echo JText::_('COM_VIRTUEMART_PRODUCT_RELATED_SEARCH'); ?>
				<div class="jsonSuggestResults" style="width: auto;">
					<input type="text" size="40" name="search" id="relatedproductsSearch" value="" />
					<button class="reset-value"><?php echo JText::_('COM_VIRTUEMART_RESET') ?></button>
				</div>
				<div id="custom_products"><?php echo  $tables['products']; ?></div>
			</fieldset>

			<fieldset style="background-color:#F9F9F9;">
				<legend><?php echo JText::_('COM_VIRTUEMART_CUSTOM_FIELD_TYPE' );?></legend>
				<div><?php echo  '<div class="inline">'.$this->customsList; ?></div>

				<table id="custom_fields" class="adminlist" cellspacing="0" cellpadding="0">
					<thead>
					<tr class="row1">
						<th><?php echo JText::_('COM_VIRTUEMART_TITLE');?></th>
						<th><?php echo JText::_('COM_VIRTUEMART_CUSTOM_TIP');?></th>
						<th><?php echo JText::_('COM_VIRTUEMART_VALUE');?></th>
						<th><?php echo JText::_('COM_VIRTUEMART_CART_PRICE');?></th>
						<th><?php echo JText::_('COM_VIRTUEMART_TYPE');?></th>
						<th><?php echo JText::_('COM_VIRTUEMART_CUSTOM_IS_CART_ATTRIBUTE');?></th>
						<th><?php echo JText::_('COM_VIRTUEMART_DELETE'); ?></th>
					</tr>
					</thead>
					<tbody id="custom_field">
						<?php
						if ($tables['fields']) echo $tables['fields'] ;
						else echo $emptyTable;
						?>
					</tbody>
				</table><!-- custom_fields -->
			</fieldset>
			<!--fieldset style="background-color:#F9F9F9;">
				<legend><?php echo JText::_('COM_VIRTUEMART_CUSTOM_EXTENSION'); ?></legend>
				<div id="custom_customPlugins"><?php echo  $tables['customPlugins']; ?></div>
			</fieldset-->
		</td>

	</tr>
</table>


<div style="clear:both;"></div>


<script type="text/javascript">
	nextCustom = <?php echo $i ?>;

	jQuery(document).ready(function(){
		jQuery('#custom_field').sortable();
		// Need to declare the update routine outside the sortable() function so
		// that it can be called when adding new customfields
		jQuery('#custom_field').bind('sortupdate', function(event, ui) {
			jQuery(this).find('.ordering').each(function(index,element) {
				jQuery(element).val(index);
				//console.log(index+' ');

			});
		});
	});
	jQuery('select#customlist').chosen().change(function() {
		selected = jQuery(this).find( 'option:selected').val() ;
		jQuery.getJSON('index.php?option=com_virtuemart&view=product&task=getData&format=json&type=fields&id='+selected+'&row='+nextCustom+'&virtuemart_product_id=<?php echo $this->product->virtuemart_product_id; ?>',
		function(data) {
			jQuery.each(data.value, function(index, value){
				jQuery("#custom_field").append(value);
				jQuery('#custom_field').trigger('sortupdate');
			});
		});
		nextCustom++;
	});

		jQuery('input#relatedproductsSearch').autocomplete({

		source: 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedproducts&row='+nextCustom,
		select: function(event, ui){
			jQuery("#custom_products").append(ui.item.label);
			nextCustom++;
			jQuery(this).autocomplete( "option" , 'source' , 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedproducts&row='+nextCustom )
			jQuery('input#relatedproductsSearch').autocomplete( "option" , 'source' , 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedproducts&row='+nextCustom )
		},
		minLength:1,
		html: true
	});
	jQuery('input#relatedcategoriesSearch').autocomplete({

		source: 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedcategories&row='+nextCustom,
		select: function(event, ui){
			jQuery("#custom_categories").append(ui.item.label);
			nextCustom++;
			jQuery(this).autocomplete( "option" , 'source' , 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedcategories&row='+nextCustom )
			jQuery('input#relatedcategoriesSearch').autocomplete( "option" , 'source' , 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedcategories&row='+nextCustom )
		},
		minLength:1,
		html: true
	});
	// jQuery('#customfieldsTable').delegate('td','click', function() {
		// jQuery('#customfieldsParent').remove();
		// jQuery(this).undelegate('td','click');
	// });
	// jQuery.each(jQuery('#customfieldsTable').filter(":input").data('events'), function(i, event) {
		// jQuery.each(event, function(i, handler){
		// console.log(handler);
	  // });
	// });


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