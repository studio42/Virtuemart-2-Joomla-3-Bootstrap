<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Category
* @author RickG, jseros
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: edit.php 3466 2011-06-08 22:37:28Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');


$mainframe = JFactory::getApplication();
 echo $this->langList ?>
<table class="adminform">
	<tr>
		<td valign="top" colspan="2">
		<fieldset>
			<legend><?php echo JText::_('COM_VIRTUEMART_FORM_GENERAL'); ?></legend>
			<table width="100%" border="0">
				<!-- Commented out for future use
				<tr>
					<td class="key">
						<label for="shared">
							<?php echo JText::_('COM_VIRTUEMART_CATEGORY_FORM_SHARED'); ?>:
						</label>
					</td>
					<td>
						<?php
							$categoryShared = isset($this->relationInfo->category_shared) ? $this->relationInfo->category_shared : 1;
							echo JHTML::_('select.booleanlist', 'shared', $categoryShared, $categoryShared);
						?>
					</td>
				</tr>
				-->
				<?php echo VmHTML::row('input','COM_VIRTUEMART_CATEGORY_NAME','category_name',$this->category->category_name); ?>
				<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_PUBLISH','published',$this->category->published); ?>
				<?php echo VmHTML::row('input','COM_VIRTUEMART_SLUG','slug',$this->category->slug); ?>
				<?php echo VmHTML::row('editor','COM_VIRTUEMART_DESCRIPTION','category_description',$this->category->category_description); ?>
			</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td valign="top" style="width: 50%;">
			<fieldset>
				<legend><?php echo JText::_('COM_VIRTUEMART_DETAILS'); ?></legend>
				<table>
					<?php echo VmHTML::row('raw','COM_VIRTUEMART_ORDERING', ShopFunctions::getEnumeratedCategories(true, true, $this->parent->virtuemart_category_id, 'ordering', '', 'ordering', 'category_name', $this->category->ordering) ); ?>
					<?php $categorylist = '
						<select name="category_parent_id" id="category_parent_id" class="inputbox">
							<option value="">'.JText::_('COM_VIRTUEMART_CATEGORY_FORM_TOP_LEVEL').'</option>
							'.$this->categorylist.'
						</select>';
						echo VmHTML::row('raw','COM_VIRTUEMART_CATEGORY_ORDERING', $categorylist ); ?>
					<?php echo VmHTML::row('input','COM_VIRTUEMART_CATEGORY_FORM_PRODUCTS_PER_ROW','products_per_row',$this->category->products_per_row); ?>
					<?php echo VmHTML::row('input','COM_VIRTUEMART_CATEGORY_FORM_LIMIT_LIST_START','limit_list_start',$this->category->limit_list_start); ?>
					<?php echo VmHTML::row('input','COM_VIRTUEMART_CATEGORY_FORM_LIMIT_LIST_STEP','limit_list_step',$this->category->limit_list_step); ?>
					<?php echo VmHTML::row('input','COM_VIRTUEMART_CATEGORY_FORM_LIMIT_LIST_MAX','limit_list_max',$this->category->limit_list_max); ?>
					<?php echo VmHTML::row('input','COM_VIRTUEMART_CATEGORY_FORM_INITIAL_DISPLAY_RECORDS','limit_list_initial',$this->category->limit_list_initial); ?>
					<?php echo VmHTML::row('select','COM_VIRTUEMART_CATEGORY_FORM_TEMPLATE', 'category_template', $this->jTemplateList ,$this->category->category_template,'','directory', 'name',false) ; ?>
					<?php echo VmHTML::row('select','COM_VIRTUEMART_CATEGORY_FORM_BROWSE_LAYOUT', 'category_layout', $this->categoryLayouts ,$this->category->category_layout,'','value', 'text',false) ; ?>
					<?php echo VmHTML::row('select','COM_VIRTUEMART_CATEGORY_FORM_FLYPAGE', 'category_product_layout', $this->productLayouts ,$this->category->category_product_layout,'','value', 'text',false) ; ?>
				</table>
			</fieldset>
		</td>
		<td valign="top" style="width: 50%;">
			<fieldset>
				<legend><?php echo JText::_('COM_VIRTUEMART_META_INFORMATION'); ?></legend>
				<table>
<?php echo VmHTML::row('input','COM_VIRTUEMART_CUSTOM_PAGE_TITLE','customtitle',$this->category->customtitle); ?>
					<?php echo VmHTML::row('textarea','COM_VIRTUEMART_META_DESC','metadesc',$this->category->metadesc); ?>
					<?php echo VmHTML::row('textarea','COM_VIRTUEMART_META_KEYWORDS','metakey',$this->category->metakey); ?>
					<?php echo VmHTML::row('input','COM_VIRTUEMART_META_ROBOTS','metarobot',$this->category->metarobot); ?>
					<?php echo VmHTML::row('input','COM_VIRTUEMART_METAAUTHOR','metaauthor',$this->category->metaauthor); ?>
				</table>
			</fieldset>
		</td>
		</tr>
		<tr>
		<?php if(Vmconfig::get('multix','none')!=='none' && $this->perms->check('admin') ){
			echo VmHTML::row('raw','COM_VIRTUEMART_VENDOR', $this->vendorList );
		} ?>
		</tr>
</table>
