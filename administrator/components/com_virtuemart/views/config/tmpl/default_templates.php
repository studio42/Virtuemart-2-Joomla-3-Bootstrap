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
// list used 2 times
$feedDescriptionType = array(
	'product_s_desc' => JText::_('COM_VIRTUEMART_PRODUCT_FORM_S_DESC') ,
	'product_desc' => JText::_('COM_VIRTUEMART_PRODUCT_FORM_DESCRIPTION') );
?>
<div class="row-fluid">
	<div class="span6">
		<fieldset>
		<legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_SHOPFRONT_SETTINGS') ?></legend>
		<table width="100%">
		    <tr>
			<td colspan="2">
			    <div class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_SELECT_DEFAULT_SHOP_TEMPLATE_TIP'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_SELECT_DEFAULT_SHOP_TEMPLATE') ?>
			    </div>
			    <?php
			    echo JHTML::_('Select.genericlist', $this->jTemplateList, 'vmtemplate', 'size=1 width=200', 'value', 'name', VmConfig::get('vmtemplate','default'));
			    ?>
			</td>
		    </tr>

		    <tr>
			<td colspan="2">
			    <div class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CATEGORY_TEMPLATE_EXPLAIN'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CATEGORY_TEMPLATE') ?>
			    </div>
			    <?php
			    echo JHTML::_('Select.genericlist', $this->jTemplateList, 'categorytemplate', 'size=1', 'value', 'name', VmConfig::get('categorytemplate','default'));
			    ?>
			</td>
		    </tr>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_SHOW_CATEGORY','showCategory',VmConfig::get('showCategory',1) ); ?>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_SHOW_MANUFACTURERS','show_manufacturers',VmConfig::get('show_manufacturers',1) ); ?>

			<td colspan="2">
			    <div  class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CATEGORY_LAYOUT_EXPLAIN'); ?>">
					<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CATEGORY_LAYOUT') ?>
			    </div>
			    <?php
			    echo JHTML::_('Select.genericlist', $this->categoryLayoutList, 'categorylayout', 'size=1', 'value', 'text', VmConfig::get('categorylayout',0));
			    ?>
			</td>
		    </tr>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_CATEGORIES_PER_ROW','categories_per_row',VmConfig::get('categories_per_row',3),'class="inputbox input-mini"' ); ?>

		    <tr>
			<td colspan="2">
			    <div class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRODUCT_LAYOUT_EXPLAIN'); ?>">
					<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRODUCT_LAYOUT') ?>
			    </div>
			    <?php
			    echo JHTML::_('Select.genericlist', $this->productLayoutList, 'productlayout', 'size=1', 'value', 'text', VmConfig::get('productlayout',0));
			    ?>
			</td>
		    </tr>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_PRODUCTS_PER_ROW','products_per_row',VmConfig::get('products_per_row',3),'class="inputbox input-mini"' ); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_PRODUCTS_PER_ROW','manufacturer_per_row',VmConfig::get('manufacturer_per_row',3),'class="inputbox input-mini"' ); ?>
      </table>
    </fieldset>
	<fieldset>
		<legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PAGINATION_SEQUENCE'); ?></legend>
		<table class="admintable">
			<?php echo VmHTML::row('input','COM_VIRTUEMART_LIST_MEDIA','mediaLimit',VmConfig::get('mediaLimit',20) ,'class="inputbox input-mini"'); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_LLIMIT_INIT_BE','llimit_init_BE',VmConfig::get('llimit_init_BE',20) ,'class="inputbox input-mini"'); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_CFG_PAGSEQ_BE','pagseq',VmConfig::get('pagseq') ,'class="inputbox input-mini"'); ?>

			<?php echo VmHTML::row('input','COM_VIRTUEMART_LLIMIT_INIT_FE','llimit_init_FE',VmConfig::get('llimit_init_FE',20) ,'class="inputbox input-mini"'); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_CFG_PAGSEQ_1','pagseq_1',VmConfig::get('pagseq_1') ,'class="inputbox input-mini"'); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_CFG_PAGSEQ_2','pagseq_2',VmConfig::get('pagseq_2') ,'class="inputbox input-mini"'); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_CFG_PAGSEQ_3','pagseq_3',VmConfig::get('pagseq_3') ,'class="inputbox input-mini"'); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_CFG_PAGSEQ_4','pagseq_4',VmConfig::get('pagseq_4') ,'class="inputbox input-mini"'); ?>
			<?php echo VmHTML::row('input','COM_VIRTUEMART_CFG_PAGSEQ_5','pagseq_5',VmConfig::get('pagseq_5') ,'class="inputbox input-mini"'); ?>

		</table>
    </fieldset>
	<fieldset>
    <legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CAT_FEED_SETTINGS') ?></legend>
    <table  width="100%">
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_FEED_ENABLE','feed_cat_published',VmConfig::get('feed_cat_published',0) ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWIMAGES','feed_cat_show_images',VmConfig::get('feed_cat_show_images',0) ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWPRICES','feed_cat_show_prices',VmConfig::get('feed_cat_show_prices',0) ); ?>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWDESC','feed_cat_show_description',VmConfig::get('feed_cat_show_description',0) ); ?>
		<tr>
			<td colspan="2">
				<div class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_DESCRIPTION_TYPE_TIP'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_DESCRIPTION_TYPE') ?>
				</div>
				<?php echo VmHTML::radioListGroup('feed_cat_description_type', VmConfig::get('feed_cat_description_type','product_s_desc'),$feedDescriptionType ); ?>
			</td>
		</tr>
		<?php echo VmHTML::row('input','COM_VIRTUEMART_ADMIN_CFG_PRODUCTS_PER_ROW','feed_cat_max_text_length',VmConfig::get('feed_cat_max_text_length', 500),'class="inputbox input-mini"' ); ?>

    </table>
</fieldset>
	  <fieldset>
		<legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_TITLE') ?></legend>
		<table  width="100%">
			<tr>
				<td class="key">
				<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_ASSETS_GENERAL_PATH_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_ASSETS_GENERAL_PATH') ?>
				</span>
				</td>
				<td>
					<input type="text" name="assets_general_path"  size="60" class="input-block" value="<?php echo VmConfig::get('assets_general_path') ?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
				<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_CATEGORY_PATH_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_CATEGORY_PATH') ?>
				</span>
				</td>
				<td>
					<input type="text" name="media_category_path"  size="60" class="input-block" value="<?php echo VmConfig::get('media_category_path') ?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
				<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_PRODUCT_PATH_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_PRODUCT_PATH') ?>
				</span>
				</td>
				<td>
					<input type="text" name="media_product_path"  size="60" class="input-block" value="<?php echo VmConfig::get('media_product_path') ?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
				<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_MANUFACTURER_PATH_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_MANUFACTURER_PATH') ?>
				</span>
				</td>
				<td>
					<input type="text" name="media_manufacturer_path"  size="60" class="input-block" value="<?php echo VmConfig::get('media_manufacturer_path') ?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
				<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_VENDOR_PATH_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_VENDOR_PATH') ?>
				</span>
				</td>
				<td>
					<input type="text" name="media_vendor_path"  size="60" class="input-block" value="<?php echo VmConfig::get('media_vendor_path') ?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
				<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_FORSALE_PATH_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_FORSALE_PATH') ?>
				</span>
			</td>
			<td>
				<input type="text" name="forSale_path"  size="60" class="input-block" value="<?php echo VmConfig::get('forSale_path') ?>" />
			</td>
			</tr>
			<tr>
			<td class="key">
				<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_FORSALE_PATH_THUMB_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MEDIA_FORSALE_PATH_THUMB') ?>
				</span>
			</td>
			<td>
				<input type="text" name="forSale_path_thumb"  size="60" class="input-block" value="<?php echo VmConfig::get('forSale_path_thumb') ?>" />
			</td>
			</tr>
			<?php
			if( function_exists('imagecreatefromjpeg') ) {
				?>
				<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_DYNAMIC_THUMBNAIL_RESIZING','img_resize_enable',VmConfig::get('img_resize_enable',0) ); ?>
				<tr>
					<td class="key">
						<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_THUMBNAIL_WIDTH_TIP'); ?>">
						<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_THUMBNAIL_WIDTH') ?>
						</span>
					</td>
					<td>
						<input type="text" name="img_width" class="inputbox input-mini" value="<?php echo VmConfig::get('img_width') ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_THUMBNAIL_HEIGHT_TIP'); ?>">
						<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_THUMBNAIL_HEIGHT') ?>
						</span>
					</td>
					<td>
						<input type="text" name="img_height" class="inputbox input-mini" value="<?php echo VmConfig::get('img_height') ?>" />
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
				<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_NOIMAGEPAGE_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_NOIMAGEPAGE') ?>
				</span>
			</td>
			<td>
				<?php
				echo JHTML::_('Select.genericlist', $this->noimagelist, 'no_image_set', 'size=1 class="input-medium"', 'value', 'text', VmConfig::get('no_image_set'));
				?>
			</td>
			</tr>
			<tr>
			<td class="key">
				<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_NOIMAGEFOUND_EXPLAIN'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_NOIMAGEFOUND') ?>
				</span>
			</td>
			<td>
				<?php
				echo JHTML::_('Select.genericlist', $this->noimagelist, 'no_image_found', 'size=1 class="input-medium"', 'value', 'text', VmConfig::get('no_image_found'));
				?>
			</td>
			</tr>
		</table>
		</fieldset>
		
	</div>
	<div class="span6">
      <fieldset>
		<legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_HOMEPAGE_SETTINGS') ?></legend>
                    <table  width="100%">
                           <tr>
			<td class="key">
			    <span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MAIN_LAYOUT_TIP'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MAIN_LAYOUT') ?>
			    </span>
			</td>
			<td>
			    <?php
			    echo JHTML::_('Select.genericlist', $this->vmLayoutList, 'vmlayout', 'size=1 class="input-medium"', 'value', 'text', VmConfig::get('vmlayout',0));
			    ?>
			</td>
		    </tr>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_SHOW_STORE_DESC','show_store_desc',VmConfig::get('show_store_desc') ); ?>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_SHOW_CATEGORIES','show_categories',VmConfig::get('show_categories') ); ?>
			
			<tr>
			<td class="key">
			    <span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CATEGORIES_PER_ROW_EXPLAIN'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_CATEGORIES_PER_ROW') ?>
			    </span>
			</td>
			<td>
			    <input type="text" name="homepage_categories_per_row" size="4" class="inputbox input-mini" value="<?php echo VmConfig::get('homepage_categories_per_row', 3) ?>" />
			</td>
		    </tr>
			
			<tr>
			<td class="key">
			    <span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRODUCTS_PER_ROW_EXPLAIN'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_PRODUCTS_PER_ROW') ?>
			    </span>
			</td>
			<td>
			    <input type="text" name="homepage_products_per_row" size="4" class="inputbox input-mini" value="<?php echo VmConfig::get('homepage_products_per_row', 3) ?>" />
			</td>
		    </tr>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_SHOW_FEATURED','show_featured',VmConfig::get('show_featured') ); ?>

		    <tr>
			<td class="key">
			    <span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEAT_PROD_ROWS_EXPL'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEAT_PROD_ROWS') ?>
			    </span>
			</td>
			<td>
			    <input type="text" name="featured_products_rows" size="4" class="inputbox input-mini" value="<?php echo VmConfig::get('featured_products_rows', 1) ?>" />
			</td>
		    </tr>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_SHOW_TOPTEN','show_topTen',VmConfig::get('show_topTen') ); ?>

		    <tr>
			<td class="key">
			    <span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_TOPTEN_PROD_ROWS_EXPL'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_TOPTEN_PROD_ROWS') ?>
			    </span>
			</td>
			<td>
			    <input type="text" name="topTen_products_rows" size="4" class="inputbox input-mini" value="<?php echo VmConfig::get('topTen_products_rows', 1) ?>" />
			</td>
		    </tr>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_SHOW_RECENT','show_recent',VmConfig::get('show_recent') ); ?>

			<tr>
			<td class="key">
			    <span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_REC_PROD_ROWS_EXPL'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_REC_PROD_ROWS') ?>
			    </span>
			</td>
			<td>
			    <input type="text" name="recent_products_rows" size="4" class="inputbox input-mini" value="<?php echo VmConfig::get('recent_products_rows', 1) ?>" />
			</td>
		    </tr>

			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_SHOW_LATEST','show_latest',VmConfig::get('show_latest') ); ?>

			<tr>
			<td class="key">
			    <span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_LAT_PROD_ROWS_EXPL'); ?>">
			    <?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_LAT_PROD_ROWS') ?>
			    </span>
			</td>
			<td>
			    <input type="text" name="latest_products_rows" size="4" class="inputbox input-mini" value="<?php echo VmConfig::get('latest_products_rows', 1) ?>" />
			</td>
		    </tr>
		</table>
	</fieldset>
	<fieldset>
		<legend><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_HOME_FEED_SETTINGS') ?></legend>
		<table  width="100%">
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_FEED_LATEST_ENABLE','feed_latest_published',VmConfig::get('feed_latest_published') ); ?>

			<tr>
				<td class="key">
				<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_LATEST_NB_TIP'); ?>">
				<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_LATEST_NB') ?>
				</span>
				</td>
				<td>
				<input type="text" class="input-mini" size="10" value="<?php echo VmConfig::get('feed_latest_nb', '5'); ?>" name="feed_latest_nb" id="feed_latest_nb" />
				</td>
			</tr>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_FEED_TOPTEN_ENABLE','feed_topten_published',VmConfig::get('feed_topten_published') ); ?>

	<tr>
	    <td class="key">
		<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_TOPTEN_NB_TIP'); ?>">
		<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_TOPTEN_NB') ?>
		</span>
	    </td>
	    <td>
		<input type="text" class="input-mini" size="10" value="<?php echo VmConfig::get('feed_topten_nb', '5'); ?>" name="feed_topten_nb" id="feed_topten_nb" />
	    </td>
	</tr>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_FEED_FEATURED_ENABLE','feed_featured_published',VmConfig::get('feed_featured_published') ); ?>
	<tr>
	    <td class="key">
		<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_FEATURED_NB_TIP'); ?>">
		<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_FEATURED_NB') ?>
		</span>
	    </td>
	    <td>
		<input type="text" class="input-mini" size="10" value="<?php echo VmConfig::get('feed_featured_nb', '5'); ?>" name="feed_featured_nb" id="feed_featured_nb" />
	    </td>
	</tr>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWIMAGES','feed_home_show_images',VmConfig::get('feed_home_show_images') ); ?>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWPRICES','feed_home_show_prices',VmConfig::get('feed_home_show_prices') ); ?>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_FEED_SHOWDESC','feed_home_show_description',VmConfig::get('feed_home_show_description') ); ?>

	<tr>
	    <td colspan="2">
			<div class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_DESCRIPTION_TYPE_TIP'); ?>">
			<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_DESCRIPTION_TYPE') ?>
			</div>
			<?php echo VmHTML::radioListGroup('feed_home_description_type', VmConfig::get('feed_home_description_type','product_s_desc'),$feedDescriptionType ); ?>
	    </td>
	</tr>

	<tr>
	    <td class="key">
		<span class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_MAX_TEXT_LENGTH_TIP'); ?>">
		<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FEED_MAX_TEXT_LENGTH') ?>
		</span>
	    </td>
	    <td>
		<input type="text" class="input-mini" size="10" value="<?php echo VmConfig::get('feed_home_max_text_length', '500'); ?>" name="feed_home_max_text_length" id="feed_home_max_text_length" />
	    </td>
	</tr>
    </table>
</fieldset>
	<fieldset>
	<legend class="hasTooltip" title="<?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FRONT_CSS_JS_SETTINGS_TIP'); ?>"><?php echo JText::_('COM_VIRTUEMART_ADMIN_CFG_FRONT_CSS_JS_SETTINGS') ?></legend>
		<table  width="100%">
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_CFG_FANCY','usefancy',VmConfig::get('usefancy') ); ?>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_FRONT_CSS','css',VmConfig::get('css',1) ); ?>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_FRONT_JQUERY','jquery',VmConfig::get('jquery',1) ); ?>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_FRONT_JPRICE','jprice',VmConfig::get('jprice',1) ); ?>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_FRONT_JSITE','jsite',VmConfig::get('jsite',1) ); ?>
			<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_ADMIN_CFG_ENABLE_GOOGLE_JQUERY','google_jquery',VmConfig::get('google_jquery',1) ); ?>
		</table>
	</fieldset>
</div>
</div>