<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Config
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default_templates.php 4115 2011-09-15 $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>
<br />
<table width="100%">
   <tr>
	<td valign="top" width="50%">
		<fieldset>
		<legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOPFRONT_SETTINGS') ?></legend>
		<table class="admintable">
		    <tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_SELECT_DEFAULT_SHOP_TEMPLATE_TIP'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_SELECT_DEFAULT_SHOP_TEMPLATE') ?>
			    </span>
			</td>
			<td>
			    <?php
			    echo JHTML::_('Select.genericlist', $this->jTemplateList, 'vmtemplate', 'size=1 width=200', 'value', 'name', VmConfig::get('vmtemplate','default'));
			    ?>
			</td>
		    </tr>

		    <tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CATEGORY_TEMPLATE_EXPLAIN'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CATEGORY_TEMPLATE') ?>
			    </span>
			</td>
			<td>
			    <?php
			    echo JHTML::_('Select.genericlist', $this->jTemplateList, 'categorytemplate', 'size=1', 'value', 'name', VmConfig::get('categorytemplate','default'));
			    ?>
			</td>
		    </tr>

		    <tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_CATEGORY_EXPLAIN'); ?>">
			    <label for="showCategory"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_CATEGORY') ?></label>
			    </span>
			</td>
			<td>
			   <?php echo VmHTML::checkbox('showCategory', VmConfig::get('showCategory',1)); ?>
			</td>
		    </tr>
		    <tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_MANUFACTURERS_EXPLAIN'); ?>">
			    <label for="show_manufacturers"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_MANUFACTURERS') ?></label>
			    </span>
			</td>
			<td>
			   <?php echo VmHTML::checkbox('show_manufacturers', VmConfig::get('show_manufacturers', 1)); ?>
			</td>
		    </tr>
		    <tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CATEGORY_LAYOUT_EXPLAIN'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CATEGORY_LAYOUT') ?>
			    </span>
			</td>
			<td>
			    <?php
			    echo JHTML::_('Select.genericlist', $this->categoryLayoutList, 'categorylayout', 'size=1', 'value', 'text', VmConfig::get('categorylayout',0));
			    ?>
			</td>
		    </tr>
		    <tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CATEGORIES_PER_ROW_EXPLAIN'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CATEGORIES_PER_ROW') ?>
			    </span>
			</td>
			<td>
			    <input type="text" name="categories_per_row" size="4" class="inputbox" value="<?php echo VmConfig::get('categories_per_row',3) ?>" />
			</td>
		    </tr>
		    <tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRODUCT_LAYOUT_EXPLAIN'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRODUCT_LAYOUT') ?>
			    </span>
			</td>
			<td>
			    <?php
			    echo JHTML::_('Select.genericlist', $this->productLayoutList, 'productlayout', 'size=1', 'value', 'text', VmConfig::get('productlayout',0));
			    ?>
			</td>
		    </tr>
		    <tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRODUCTS_PER_ROW_EXPLAIN'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRODUCTS_PER_ROW') ?>
			    </span>
			</td>
			<td>
			    <input type="text" name="products_per_row" size="4" class="inputbox" value="<?php echo VmConfig::get('products_per_row',3) ?>" />
			</td>
		    </tr>

	 	<tr>
		<td class="key">
		    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MANUFACTURER_PER_ROW_EXPLAIN'); ?>">
		    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MANUFACTURER_PER_ROW') ?>
		    </span>
		</td>
		<td>
		    <input type="text" name="manufacturer_per_row" size="4" class="inputbox" value="<?php echo VmConfig::get('manufacturer_per_row',3) ?>" />
		</td>
	    </tr>
	    <tr>
		<td class="key">
		    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PAGINATION_SEQUENCE_EXPLAIN'); ?>">
		    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PAGINATION_SEQUENCE') ?>
		    </span>
		</td>
		<td>
		    <input type="text" name="pagination_sequence" class="inputbox" value="<?php echo VmConfig::get('pagination_sequence') ?>" />
		</td>
	    </tr>
      </table>
      </fieldset>
	  		<fieldset>
    <legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CAT_FEED_SETTINGS') ?></legend>
    <table class="admintable">
	<tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_ENABLE_TIP'); ?>">
		<label for="feed_cat_published"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_ENABLE') ?></label>
		</span>
	    </td>
	    <td>
		<?php echo VmHTML::checkbox('feed_cat_published', VmConfig::get('feed_cat_published',0)); ?>
	    </td>
	</tr>

	<tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWIMAGES_TIP'); ?>">
		<label for="feed_cat_show_images"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWIMAGES') ?></span>
		</span>
	    </td>
	    <td>
		<?php echo VmHTML::checkbox('feed_cat_show_images', VmConfig::get('feed_cat_show_images')); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWPRICES_TIP'); ?>">
		<label for="feed_cat_show_prices"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWPRICES') ?></span>
		</span>
	    </td>
	    <td>
		<?php echo VmHTML::checkbox('feed_cat_show_prices', VmConfig::get('feed_cat_show_prices')); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWDESC_TIP'); ?>">
		<label for="feed_cat_show_description"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWDESC') ?></span>
		</span>
	    </td>
	    <td>
		<?php echo VmHTML::checkbox('feed_cat_show_description', VmConfig::get('feed_cat_show_description')); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_DESCRIPTION_TYPE_TIP'); ?>">
		<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_DESCRIPTION_TYPE') ?>
		</span>
	    </td>
	    <td>
		<?php
		$options = array();
		$options[] = JHTML::_('select.option', 'product_s_desc', JText::_('COM_VIRTUEMART_PRODUCT_FORM_S_DESC'));
		$options[] = JHTML::_('select.option', 'product_desc', JText::_('COM_VIRTUEMART_PRODUCT_FORM_DESCRIPTION'));
		echo JHTML::_('Select.genericlist', $options, 'feed_cat_description_type', 'size=1', 'value', 'text', VmConfig::get('feed_cat_description_type'));
		?>
	    </td>
	</tr>

	<tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MAX_TEXT_LENGTH_TIP'); ?>">
		<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_MAX_TEXT_LENGTH') ?>
		</span>
	    </td>
	    <td>
		<input type="text" size="10" value="<?php echo VmConfig::get('feed_cat_max_text_length', '500'); ?>" name="feed_cat_max_text_length" id="feed_cat_max_text_length" />
	    </td>
	</tr>
    </table>
</fieldset>
	  <fieldset>
		<legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_TITLE') ?></legend>
		<table class="admintable">
			<tr>
				<td class="key">
				<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_ASSETS_GENERAL_PATH_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_ASSETS_GENERAL_PATH') ?>
				</span>
				</td>
				<td>
					<input type="text" name="assets_general_path"  size="60" class="inputbox" value="<?php echo VmConfig::get('assets_general_path') ?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
				<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_CATEGORY_PATH_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_CATEGORY_PATH') ?>
				</span>
				</td>
				<td>
					<input type="text" name="media_category_path"  size="60" class="inputbox" value="<?php echo VmConfig::get('media_category_path') ?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
				<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_PRODUCT_PATH_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_PRODUCT_PATH') ?>
				</span>
				</td>
				<td>
					<input type="text" name="media_product_path"  size="60" class="inputbox" value="<?php echo VmConfig::get('media_product_path') ?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
				<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_MANUFACTURER_PATH_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_MANUFACTURER_PATH') ?>
				</span>
				</td>
				<td>
					<input type="text" name="media_manufacturer_path"  size="60" class="inputbox" value="<?php echo VmConfig::get('media_manufacturer_path') ?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
				<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_VENDOR_PATH_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_VENDOR_PATH') ?>
				</span>
				</td>
				<td>
					<input type="text" name="media_vendor_path"  size="60" class="inputbox" value="<?php echo VmConfig::get('media_vendor_path') ?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
				<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_FORSALE_PATH_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_FORSALE_PATH') ?>
				</span>
			</td>
			<td>
				<input type="text" name="forSale_path"  size="60" class="inputbox" value="<?php echo VmConfig::get('forSale_path') ?>" />
			</td>
			</tr>
			<tr>
			<td class="key">
				<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_FORSALE_PATH_THUMB_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_FORSALE_PATH_THUMB') ?>
				</span>
			</td>
			<td>
				<input type="text" name="forSale_path_thumb"  size="60" class="inputbox" value="<?php echo VmConfig::get('forSale_path_thumb') ?>" />
			</td>
			</tr>
			<?php
			if( function_exists('imagecreatefromjpeg') ) {
				?>
				<tr>
					<td class="key">
						<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_DYNAMIC_THUMBNAIL_RESIZING_TIP'); ?>">
						<label for="img_resize_enable"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_DYNAMIC_THUMBNAIL_RESIZING') ?></label>
						</span>
					</td>
					<td>
						<?php echo VmHTML::checkbox('img_resize_enable', VmConfig::get('img_resize_enable')); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_THUMBNAIL_WIDTH_TIP'); ?>">
						<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_THUMBNAIL_WIDTH') ?>
						</span>
					</td>
					<td>
						<input type="text" name="img_width" class="inputbox" value="<?php echo VmConfig::get('img_width') ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_THUMBNAIL_HEIGHT_TIP'); ?>">
						<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_THUMBNAIL_HEIGHT') ?>
						</span>
					</td>
					<td>
						<input type="text" name="img_height" class="inputbox" value="<?php echo VmConfig::get('img_height') ?>" />
					</td>
				</tr>
				<?php
			}
			else { ?>
				<tr>
					<td colspan="2"><strong><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_GD_MISSING') ?></strong>
						<input type="hidden" name="img_resize_enable" value="0" />
						<input type="hidden" name="img_width" value="<?php echo  VmConfig::get('img_width',90) ?>" />
						<input type="hidden" name="img_height" value="<?php echo  VmConfig::get('img_height',90) ?>" />
					</td>
				</tr>
			<?php }
			?>
			<tr>
			<td class="key">
				<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_NOIMAGEPAGE_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_NOIMAGEPAGE') ?>
				</span>
			</td>
			<td>
				<?php
				echo JHTML::_('Select.genericlist', $this->noimagelist, 'no_image_set', 'size=1', 'value', 'text', VmConfig::get('no_image_set'));
				?>
			</td>
			</tr>
			<tr>
			<td class="key">
				<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_NOIMAGEFOUND_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_NOIMAGEFOUND') ?>
				</span>
			</td>
			<td>
				<?php
				echo JHTML::_('Select.genericlist', $this->noimagelist, 'no_image_found', 'size=1', 'value', 'text', VmConfig::get('no_image_found'));
				?>
			</td>
			</tr>
		</table>
		</fieldset>
		
	</td>
	<td valign="top">
      <fieldset>
		<legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_HOMEPAGE_SETTINGS') ?></legend>
                    <table class="admintable">
                           <tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MAIN_LAYOUT_TIP'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MAIN_LAYOUT') ?>
			    </span>
			</td>
			<td>
			    <?php
			    echo JHTML::_('Select.genericlist', $this->vmLayoutList, 'vmlayout', 'size=1', 'value', 'text', VmConfig::get('vmlayout',0));
			    ?>
			</td>
		    </tr>
			
			<tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_STORE_DESC_TIP'); ?>" >
			    <label for="show_store_desc"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_STORE_DESC') ?></label>
			    </span>
			</td>
			<td>
			    <?php echo VmHTML::checkbox('show_store_desc', VmConfig::get('show_store_desc')); ?>
			</td>
		    </tr>
			
			<tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_CATEGORIES_TIP'); ?>" >
			    <label for="show_categories"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_CATEGORIES') ?></label>
			    </span>
			</td>
			<td>
			    <?php echo VmHTML::checkbox('show_categories', VmConfig::get('show_categories',1)); ?>
			</td>
		    </tr>
			
			<tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CATEGORIES_PER_ROW_EXPLAIN'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CATEGORIES_PER_ROW') ?>
			    </span>
			</td>
			<td>
			    <input type="text" name="homepage_categories_per_row" size="4" class="inputbox" value="<?php echo VmConfig::get('homepage_categories_per_row', 3) ?>" />
			</td>
		    </tr>
			
			<tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRODUCTS_PER_ROW_EXPLAIN'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRODUCTS_PER_ROW') ?>
			    </span>
			</td>
			<td>
			    <input type="text" name="homepage_products_per_row" size="4" class="inputbox" value="<?php echo VmConfig::get('homepage_products_per_row', 3) ?>" />
			</td>
		    </tr>
			
		    <tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_FEATURED_TIP'); ?>" >
			    <label for="show_featured"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_FEATURED') ?></label>
			    </span>
			</td>
			<td>
			    <?php echo VmHTML::checkbox('show_featured', VmConfig::get('show_featured')); ?>
			</td>
		    </tr>
		    <tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEAT_PROD_ROWS_EXPL'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEAT_PROD_ROWS') ?>
			    </span>
			</td>
			<td>
			    <input type="text" name="featured_products_rows" size="4" class="inputbox" value="<?php echo VmConfig::get('featured_products_rows', 1) ?>" />
			</td>
		    </tr>
			
			<tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_TOPTEN_TIP'); ?>" >
			    <label for="show_topTen"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_TOPTEN') ?></label>
			    </span>
			</td>
			<td>
			    <?php echo VmHTML::checkbox('show_topTen', VmConfig::get('show_topTen')); ?>
			</td>
		    </tr>
		    <tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_TOPTEN_PROD_ROWS_EXPL'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_TOPTEN_PROD_ROWS') ?>
			    </span>
			</td>
			<td>
			    <input type="text" name="topTen_products_rows" size="4" class="inputbox" value="<?php echo VmConfig::get('topTen_products_rows', 1) ?>" />
			</td>
		    </tr>
			
			<tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_RECENT_TIP'); ?>" >
			    <label for="show_recent"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_RECENT') ?></label>
			    </span>
			</td>
			<td>
			    <?php echo VmHTML::checkbox('show_recent', VmConfig::get('show_recent')); ?>
			</td>
		    </tr>
			<tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_REC_PROD_ROWS_EXPL'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_REC_PROD_ROWS') ?>
			    </span>
			</td>
			<td>
			    <input type="text" name="recent_products_rows" size="4" class="inputbox" value="<?php echo VmConfig::get('recent_products_rows', 1) ?>" />
			</td>
		    </tr>

		    <tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_LATEST_TIP'); ?>" >
			    <label for="show_latest"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOW_LATEST') ?></label>
			    </span>
			</td>
			<td>
			    <?php echo VmHTML::checkbox('show_latest', VmConfig::get('show_latest')); ?>
			</td>
		    </tr>
			<tr>
			<td class="key">
			    <span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_LAT_PROD_ROWS_EXPL'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_LAT_PROD_ROWS') ?>
			    </span>
			</td>
			<td>
			    <input type="text" name="latest_products_rows" size="4" class="inputbox" value="<?php echo VmConfig::get('latest_products_rows', 1) ?>" />
			</td>
		    </tr>
		</table>
	    </fieldset>
		<fieldset>
    <legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_HOME_FEED_SETTINGS') ?></legend>
    <table class="admintable">
	<tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_LATEST_ENABLE_TIP'); ?>">
		<label for="feed_latest_published"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_LATEST_ENABLE') ?></label>
		</span>
	    </td>
	    <td>
		<?php echo VmHTML::checkbox('feed_latest_published', VmConfig::get('feed_latest_published',0)); ?>
	    </td>
	</tr>
	    <tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_LATEST_NB_TIP'); ?>">
		<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_LATEST_NB') ?>
		</span>
	    </td>
	    <td>
		<input type="text" size="10" value="<?php echo VmConfig::get('feed_latest_nb', '5'); ?>" name="feed_latest_nb" id="feed_latest_nb" />
	    </td>
	</tr>
<tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_TOPTEN_ENABLE_TIP'); ?>">
		<label for="feed_topten_published"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_TOPTEN_ENABLE') ?></label>
		</span>
	    </td>
	    <td>
		<?php echo VmHTML::checkbox('feed_topten_published', VmConfig::get('feed_topten_published',0)); ?>
	    </td>
	  <tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_TOPTEN_NB_TIP'); ?>">
		<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_TOPTEN_NB') ?>
		</span>
	    </td>
	    <td>
		<input type="text" size="10" value="<?php echo VmConfig::get('feed_topten_nb', '5'); ?>" name="feed_topten_nb" id="feed_topten_nb" />
	    </td>
	</tr>
	</tr>
	    <tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_FEATURED_ENABLE_TIP'); ?>">
		<label for="feed_featured_published"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_FEATURED_ENABLE') ?></label>
		</span>
	    </td>
	    <td>
		<?php echo VmHTML::checkbox('feed_featured_published', VmConfig::get('feed_featured_published',0)); ?>
	    </td>
	</tr>
	      <tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_FEATURED_NB_TIP'); ?>">
		<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_FEATURED_NB') ?>
		</span>
	    </td>
	    <td>
		<input type="text" size="10" value="<?php echo VmConfig::get('feed_featured_nb', '5'); ?>" name="feed_featured_nb" id="feed_featured_nb" />
	    </td>
	</tr>
	<tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWIMAGES_TIP'); ?>">
		<label for="feed_home_show_images"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWIMAGES') ?></span>
		</span>
	    </td>
	    <td>
		<?php echo VmHTML::checkbox('feed_home_show_images', VmConfig::get('feed_home_show_images')); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWPRICES_TIP'); ?>">
		<label for="feed_home_show_prices"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWPRICES') ?></span>
		</span>
	    </td>
	    <td>
		<?php echo VmHTML::checkbox('feed_home_show_prices', VmConfig::get('feed_home_show_prices')); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWDESC_TIP'); ?>">
		<label for="feed_home_show_description"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWDESC') ?></span>
		</span>
	    </td>
	    <td>
		<?php echo VmHTML::checkbox('feed_home_show_description', VmConfig::get('feed_home_show_description')); ?>
	    </td>
	</tr>
	<tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_DESCRIPTION_TYPE_TIP'); ?>">
		<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_DESCRIPTION_TYPE') ?>
		</span>
	    </td>
	    <td>
		<?php
		$options = array();
		$options[] = JHTML::_('select.option', 'product_s_desc', JText::_('COM_VIRTUEMART_PRODUCT_FORM_S_DESC'));
		$options[] = JHTML::_('select.option', 'product_desc', JText::_('COM_VIRTUEMART_PRODUCT_FORM_DESCRIPTION'));
		echo JHTML::_('Select.genericlist', $options, 'feed_home_description_type', 'size=1', 'value', 'text', VmConfig::get('feed_home_description_type'));
		?>
	    </td>
	</tr>

	<tr>
	    <td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MAX_TEXT_LENGTH_TIP'); ?>">
		<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_MAX_TEXT_LENGTH') ?>
		</span>
	    </td>
	    <td>
		<input type="text" size="10" value="<?php echo VmConfig::get('feed_home_max_text_length', '500'); ?>" name="feed_home_max_text_length" id="feed_home_max_text_length" />
	    </td>
	</tr>
    </table>
</fieldset>
		<fieldset>
		<legend class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FRONT_CSS_JS_SETTINGS_TIP'); ?>"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FRONT_CSS_JS_SETTINGS') ?></legend>
		<table class="admintable">
			<tr>
			<td class="key">
				<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FRONT_CSS_TIP'); ?>">
				<label for="css"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FRONT_CSS') ?></label>
				</span>
			</td>
			<td>
				<?php echo VmHTML::checkbox('css', VmConfig::get('css',1)); ?>
			</td>
			</tr>
			<tr>
			<td class="key">
				<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FRONT_JQUERY_TIP'); ?>">
				<label for="jquery"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FRONT_JQUERY') ?></label>
				</span>
			</td>
			<td>
				<?php echo VmHTML::checkbox('jquery', VmConfig::get('jquery',1)); ?>
			</td>
			</tr>
			<tr>
			<td class="key">
				<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FRONT_JPRICE_TIP'); ?>">
				<label for="jprice"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FRONT_JPRICE') ?></label>
				</span>
			</td>
			<td>
				<?php echo VmHTML::checkbox('jprice', VmConfig::get('jprice',1)); ?>
			</td>
			</tr>
			<tr>
			<td class="key">
				<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FRONT_JSITE_TIP'); ?>">
				<label for="jsite"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FRONT_JSITE') ?></label>
				</span>
			</td>
			<td>
				<?php echo VmHTML::checkbox('jsite', VmConfig::get('jsite',1)); ?>
			</td>
			</tr>

			<tr>
				<td class="key">
		<span class="hasTip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_ENABLE_GOOGLE_JQUERY_EXPLAIN'); ?>">
		<label for="google_jquery"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_ENABLE_GOOGLE_JQUERY') ?>
		</span>
				</td>
				<td>
					<?php
					echo VmHTML::checkbox('google_jquery', VmConfig::get('google_jquery','1'));
					?>
				</td>
			</tr>
		</table>
	    </fieldset>
		
	</td>
</tr>

</table>
