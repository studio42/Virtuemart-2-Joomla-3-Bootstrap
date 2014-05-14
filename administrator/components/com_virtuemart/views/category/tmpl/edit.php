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
* @version $Id: edit.php 6350 2012-08-14 17:18:08Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();
// $editor = JFactory::getEditor();
// autocomplet missing in Front-end edit
JHtml::_('jquery.ui');
vmJsApi::js ('jquery.ui.autocomplete.html');
?>

<form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">

	
	<fieldset>
		<legend><?php echo JText::_('COM_VIRTUEMART_CATEGORY'); ?><div class="pull-right"><?php echo $this->langList; ?></div></legend>
		<div class="row-fluid">
			<table class="span6">
				<?php echo VmHTML::row('input','COM_VIRTUEMART_CATEGORY_NAME','category_name',$this->category->category_name); ?>
				<?php echo VmHTML::row('input','COM_VIRTUEMART_SLUG','slug',$this->category->slug); ?>
				<?php $categorylist = '
					<select name="category_parent_id" id="category_parent_id" class="inputbox">
						<option value="">'.JText::_('COM_VIRTUEMART_CATEGORY_FORM_TOP_LEVEL').'</option>
						'.$this->categorylist.'
					</select>';
					echo VmHTML::row('raw','COM_VIRTUEMART_CATEGORY_FORM_PARENT', $categorylist ); ?>
			</table>
			<table class="span6">
				<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_PUBLISHED','published',$this->category->published); ?>
				<?php
				$categoryShared = isset($this->relationInfo->category_shared) ? $this->relationInfo->category_shared : 1;
				echo VmHTML::row('booleanlist','COM_VIRTUEMART_CATEGORY_FORM_SHARED','shared', $categoryShared);
				?>
				<?php if(Vmconfig::get('multix','none')!=='none' && $this->perms->check('admin') ){
					echo VmHTML::row('raw','COM_VIRTUEMART_VENDOR', $this->vendorList );
				} ?>
			</table>
		</div>

	</fieldset>
	<div class="accordion">
		<?php
			//echo $this->category->images[0]->displayFilesHandler($this->category->virtuemart_media_id);
			if(empty($this->category->images[0]->virtuemart_media_id)) $this->category->images[0]->addHidden('file_is_category_image','1');
			if ($this->category->virtuemart_media_id) echo $this->category->images[0]->displayFilesHandler($this->category->virtuemart_media_id,'category');
			else echo $this->category->images[0]->displayFilesHandler(null,'category');
		?>
	</div>
	<fieldset>
		<legend><?php echo JText::_('COM_VIRTUEMART_DESCRIPTION'); ?></legend>
		<div>
			<?php echo VmHTML::editor('category_description',$this->category->category_description); ?>
		</div>
	</fieldset>
	<div class="row-fluid">
	<fieldset class="span6">
		<legend><?php echo JText::_('COM_VIRTUEMART_DETAILS'); ?></legend>
		<table>
			<?php echo VmHTML::row('raw','COM_VIRTUEMART_ORDERING', ShopFunctions::getEnumeratedCategories(true, true, $this->parent->virtuemart_category_id, 'ordering', '', 'ordering', 'category_name', $this->category->ordering) ); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_CATEGORY_FORM_PRODUCTS_PER_ROW','products_per_row',$this->category->products_per_row); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_CATEGORY_FORM_LIMIT_LIST_STEP','limit_list_step',$this->category->limit_list_step); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_CATEGORY_FORM_INITIAL_DISPLAY_RECORDS','limit_list_initial',$this->category->limit_list_initial); ?>
			<?php echo VmHTML::row('select','COM_VIRTUEMART_CATEGORY_FORM_TEMPLATE', 'category_template', $this->jTemplateList ,$this->category->category_template,'','directory', 'name',false) ; ?>
			<?php echo VmHTML::row('raw','COM_VIRTUEMART_CATEGORY_FORM_BROWSE_LAYOUT', $this->categoryLayouts) ; ?>
			<?php echo VmHTML::row('raw','COM_VIRTUEMART_CATEGORY_FORM_FLYPAGE', $this->productLayouts ,$this->category->category_product_layout,'','value', 'text',false) ; ?>
		</table>
	</fieldset>
	<fieldset class="span6">
		<legend><?php echo JText::_('COM_VIRTUEMART_METAINFO'); ?></legend>

		<?php echo shopFunctions::renderMetaEdit($this->category); ?>
	</fieldset>
	</div>
	<input type="hidden" name="virtuemart_category_id" value="<?php echo $this->category->virtuemart_category_id; ?>" />

	<?php echo $this->addStandardHiddenToForm(); ?>

</form>

<?php AdminUIHelper::endAdminArea(); ?>