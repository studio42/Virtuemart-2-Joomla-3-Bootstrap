<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author Max Milbers, Roland?
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 2978 2011-04-06 14:21:19Z alatak $
*/
jimport('joomla.filesystem.file');

/* Get the component name */
$option = JRequest::getWord('option');

/* Load some variables */
$keyword = JRequest::getWord('keyword', null);
$lang = JFactory::getLanguage();
$customs = $this->customs->items;
$multivendor = Vmconfig::get('multix','none');
$multiX = $multivendor !=='none' && $multivendor !='' ? true : false ;
$multiXadmin = Vmconfig::get('multix','none')!=='none' and $this->adminVendor == 1;
//$roles = $this->customlistsroles;
$listDirn = strtolower($this->lists['filter_order_Dir']);
$saveOrder = ($this->lists['filter_order'] == 'ordering' && $listDirn =="asc");
?>
<div id="resultscounter"><?php echo $this->pagination->getResultsCounter(); ?></div>
<table class="table table-striped"<?php if ($saveOrder) echo ' id="customList"' ?>>
	<thead>
	<tr>
		<th width="1%" class="nowrap center hidden-phone">
		<?php echo $this->sort('ordering') ?>
		</th>
		<th><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
		</th>
		<th><?php echo JText::_('COM_VIRTUEMART_CUSTOM_PARENT'); ?></th>
		<th><?php echo JText::_('COM_VIRTUEMART_TITLE'); ?></th>
		<!--<th><?php echo JText::_('COM_VIRTUEMART_CUSTOM_FIELD_DESCRIPTION'); ?></th>-->
		<th><?php echo JText::_('COM_VIRTUEMART_TYPE'); ?></th>
		<th>
			<?php echo $this->sort('is_cart_attribute','<div class="small">'.JText::_('COM_VIRTUEMART_CUSTOM_IS_CART_ATTRIBUTE').'</div>' ) ?>
		</th>
		<th class="hidden-phone"><div class="small"><?php echo JText::_('COM_VIRTUEMART_CUSTOM_ADMIN_ONLY'); ?></div></th>
		<th class="hidden-phone"><div class="small"><?php echo JText::_('COM_VIRTUEMART_CUSTOM_IS_HIDDEN'); ?></div></th>

		<th class="autosize"><?php echo $this->sort('published', 'COM_VIRTUEMART_PUBLISHED'); ?></th>
		<?php if ($multiX ){ ?>
		<th width="10"  class="autosize">
			<?php echo JText::_('COM_VIRTUEMART_SHARED'); ?>
		</th>
		<?php } ?>
		<th class="hidden-phone"><?php echo $this->sort('virtuemart_custom_id', 'COM_VIRTUEMART_ID')  ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if ($customs) {

		$i = 0;

		foreach ($customs as $key => $custom) {

			$checked = JHTML::_('grid.id', $i , $custom->virtuemart_custom_id,false);
			$canDo = $this->canChange($custom->created_by) && ShopFunctions::can('publish');
			$published = $this->toggle( $custom->published, $i, 'published',$canDo);
			?>
			<tr sortable-group-id="<?php echo $custom->custom_parent_id; ?>">
				<td class="order nowrap center hidden-phone">
					<?php
					$iconClass = '';
					if (!$canDo)
					{
						$iconClass = ' inactive';
					}
					elseif (!$saveOrder)
					{
						$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
					}
					?>
					<span class="sortable-handler<?php echo $iconClass ?>">
						<i class="icon-menu"></i>
					</span>
					<?php if ($canDo && $saveOrder) : ?>
						<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $custom->ordering; ?>" class="width-20 text-area-order " />
					<?php endif; ?>
				</td>
				<!-- Checkbox -->
				<td><?php echo $checked; ?></td>
				<td>
					<?php
					if ($custom->custom_parent_id) {
						$link = "index.php?view=custom&keyword=".urlencode($keyword)."&custom_parent_id=".$custom->custom_parent_id."&option=".$option;
						if ($this->frontEdit) $link .= "&tmpl=component";
						$text = $lang->hasKey($custom->custom_parent_title) ? JText::_($custom->custom_parent_title) : $custom->custom_parent_title;
						echo JHTML::_('link', JRoute::_($link),'<div class="small">'.$text.'</div>', array('class'=> 'hasTooltip', 'title' => JText::_('COM_VIRTUEMART_FILTER_BY').' '.$text));
					}
					?>
				</td>
				<!-- Product name -->
				<?php
				if ($custom->is_cart_attribute) $cartIcon=  'default';
							 else  $cartIcon= 'default-off';
				?>
				<td>
					<?php echo $this->editLink($custom->virtuemart_custom_id, $custom->custom_title ) ?>
					<?php if ($custom->custom_field_desc) echo '<div class="small">'.$custom->custom_field_desc.'</div>' ?>
				</td>
				<td><?php echo $custom->field_type_display; ?></td>
				<td><span class="vmicon vmicon-16-<?php echo $cartIcon ?>"></span></td>
				<td class="hidden-phone">
					<?php echo $this->toggle($custom->admin_only , $i, 'toggle.admin_only',$canDo); ?>
				</td>
				<td class="hidden-phone">
					<?php echo $this->toggle($custom->is_hidden , $i, 'toggle.is_hidden',$canDo); ?>
				</td>
				<?php /* <td align="center" class="order">
					<span><?php echo $this->pagination->orderUpIcon($i, $canDo, 'orderUp', JText::_('COM_VIRTUEMART_MOVE_UP')); ?></span>
					<span><?php echo $this->pagination->orderDownIcon( $i, $n, $canDo, 'orderDown', JText::_('COM_VIRTUEMART_MOVE_DOWN')); ?></span>
					<input class="ordering input-mini" <?php echo $canDo ? '' : 'disabled="disabled"' ?>" type="text" name="order[<?php echo $i?>]" id="order[<?php echo $i?>]" size="5" value="<?php echo $custom->ordering; ?>" style="text-align: center" />
				</td> */ ?>
				<td><?php echo $published; ?></td>
				<?php if( $multiXadmin ) {
					$shared = $this->toggle($custom->shared, $i, 'toggle.shared',$canDo);
					?><td align="center">
						<?php echo $shared; ?>
                    </td>
					<?php
				} ?>
				<td class="hidden-phone"><?php echo $custom->virtuemart_custom_id; ?></td>
			</tr>
		<?php
			$i++;
		}
	}
	?>
	</tbody>
	<tfoot>
	<tr>
	<td colspan="16">
		<?php echo $this->pagination->getListFooter(); ?>
	</td>
	</tr>
	</tfoot>
	</table>
<?php if (JRequest::getInt('virtuemart_product_id', false)) { ?>
	<input type="hidden" name="virtuemart_product_id" value="<?php echo JRequest::getInt('virtuemart_product_id',0); ?>" />
<?php } ?>
<?php echo $this->addStandardHiddenToForm();  ?>
	<?php //echo $this->addStandardHiddenToForm($this->_name,$this->task);  
// if ($saveOrder)
// {
	$saveOrderingUrl = 'index.php?option=com_virtuemart&view=custom&task=saveorder&format=json';
	// only recall if script is loaded
	if (jRequest::getword('format') == 'raw') { ?>
	<script>	
		sortableList = new jQuery.JSortableList('#customList tbody','adminForm','desc' , 'index.php?option=com_virtuemart&view=category&task=saveorder&format=json','','');
	</script>
	<?php
	}
	else
		JHtml::_('sortablelist.sortable', 'customList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
// }
?>
