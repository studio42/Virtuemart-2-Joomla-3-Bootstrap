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

//$roles = $this->customlistsroles;

?>
<div id="resultscounter"><?php echo $this->pagination->getResultsCounter(); ?></div>
<table class="table table-striped">
	<thead>
	<tr>
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
		<th>
		<?php echo $this->sort('ordering') ?>
		<?php echo JHTML::_('grid.order',  $customs ); ?>
		</th>
		<th class="autosize"><?php echo $this->sort('published', 'COM_VIRTUEMART_PUBLISHED'); ?></th>
		  <th class="hidden-phone"><?php echo $this->sort('virtuemart_custom_id', 'COM_VIRTUEMART_ID')  ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if ($n = count($customs)) {

		$i = 0;

		foreach ($customs as $key => $custom) {

			$checked = JHTML::_('grid.id', $i , $custom->virtuemart_custom_id,false,'virtuemart_custom_id');
			if (!is_null($custom->virtuemart_custom_id)) $published = $this->toggle( $custom->published, $i, 'published');
			else $published = '';
			?>
			<tr >
				<!-- Checkbox -->
				<td><?php echo $checked; ?></td>
				<td>
					<?php
					if ($custom->custom_parent_id) {
						$link = "index.php?view=custom&keyword=".urlencode($keyword)."&custom_parent_id=".$custom->custom_parent_id."&option=".$option;
						$text = $lang->hasKey($custom->custom_parent_title) ? JText::_($custom->custom_parent_title) : $custom->custom_parent_title;
						echo JHTML::_('link', JRoute::_($link),'<div class="small">'.$text.'</div>', array('title' => JText::_('COM_VIRTUEMART_FILTER_BY').' '.$text));
					}
					?>
				</td>
				<!-- Product name -->
				<?php
				$link = "index.php?option=com_virtuemart&view=custom&task=edit&virtuemart_custom_id=".$custom->virtuemart_custom_id;
				if ($custom->is_cart_attribute) $cartIcon=  'default';
							 else  $cartIcon= 'default-off';
				?>
				<td><?php echo JHTML::_('link', JRoute::_($link), $custom->custom_title, array('title' => JText::_('COM_VIRTUEMART_EDIT').' '.$custom->custom_title)); ?>
					<?php if ($custom->custom_field_desc) echo '<div class="small">'.$custom->custom_field_desc.'</div>' ?>
				</td>
				<td><?php echo $custom->field_type_display; ?></td>
				<td><span class="vmicon vmicon-16-<?php echo $cartIcon ?>"></span></td>
				<td class="hidden-phone">
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','toggle.admin_only')" title="<?php echo ($custom->admin_only ) ? JText::_('COM_VIRTUEMART_YES') : JText::_('COM_VIRTUEMART_NO');?>">
					<span class="vmicon <?php echo ( $custom->admin_only  ? 'vmicon-16-checkin' : 'vmicon-16-bug' );?>"></span></a></td>
				<td class="hidden-phone"><a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','toggle.is_hidden')" title="<?php echo ($custom->is_hidden ) ? JText::_('COM_VIRTUEMART_YES') : JText::_('COM_VIRTUEMART_NO');?>">
					<span class="vmicon <?php echo ( $custom->is_hidden  ? 'vmicon-16-checkin' : 'vmicon-16-bug' );?>"></span></a></td>
				<td align="center" class="order">
					<span><?php echo $this->pagination->orderUpIcon($i, true, 'orderUp', JText::_('COM_VIRTUEMART_MOVE_UP')); ?></span>
					<span><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'orderDown', JText::_('COM_VIRTUEMART_MOVE_DOWN')); ?></span>
					<input class="ordering input-mini" type="text" name="order[<?php echo $i?>]" id="order[<?php echo $i?>]" size="5" value="<?php echo $custom->ordering; ?>" style="text-align: center" />
				</td>
				<td><?php echo $published; ?></td>
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
