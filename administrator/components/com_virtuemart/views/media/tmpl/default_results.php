<?php
if( !defined( '_JEXEC' ) ) die( );

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
* @version $Id: default.php 6559 2012-10-18 13:22:30Z Milbo $
*/

jimport('joomla.filesystem.file');
//$roles = $this->productfilesroles; 
?>
	<div id="resultscounter"><?php echo $this->pagination->getResultsCounter();?></div>
	<table class="table table-striped">
	<thead>
	<tr>
		<th>
			<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
		</th>
		<th><?php echo JText::_('COM_VIRTUEMART_VIEW'); ?></th>
		<th><?php echo $this->sort('file_title', 'COM_VIRTUEMART_FILES_LIST_FILETITLE') ?></th>
		<th><?php echo $this->sort('file_type', 'COM_VIRTUEMART_FILES_LIST_ROLE') ?></th>
		<th class="hidden-phone"><?php echo JText::_('COM_VIRTUEMART_FILES_LIST_FILENAME'); ?></th>
		<th class="hidden-phone"><?php echo JText::_('COM_VIRTUEMART_FILES_LIST_FILETYPE'); ?></th>
		<th><?php echo $this->sort('published','COM_VIRTUEMART_PUBLISHED'); ?></th>
	  <th  class="hidden-phone"><?php echo $this->sort('virtuemart_media_id', 'COM_VIRTUEMART_ID')  ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if (count($this->files) > 0) {
		$i = 0;

		foreach ($this->files as $key => $media) {

			$checked = JHTML::_('grid.id', $i , $media->virtuemart_media_id,null,'virtuemart_media_id');
			$canDo = $this->canChange($media->created_by);
			$published = $this->toggle( $media->published, $i, 'published',$canDo);
			
			?>
			<tr >
				<!-- Checkbox -->
				<td><?php echo $checked;   ?></td>
				<!-- Preview -->
				<td>
				<?php
					echo $media->displayMediaThumb('',true,'class="hasTooltip thumbnail modalbox" rel="group" ');

				?>
				</td>
				<!-- File name -->
				<td>
					<?php echo $this->editLink(	$media->virtuemart_media_id, $media->file_title, 'virtuemart_media_id[]',
						array('class'=> 'hasTooltip', 'title' => JText::_('COM_VIRTUEMART_EDIT').' '.$media->file_title) ) ?>
					<small class="visible-phone"><?php echo $media->file_name; ?> (<?php echo $media->file_extension; ?>)</small>
				</td>
				<!-- File role -->
				<td><?php
					//Just to have something, we could make this nicer with Icons
					if(!empty($media->file_is_product_image)) echo JText::_('COM_VIRTUEMART_'.$media->file_type.'_IMAGE') ;
					if(!empty($media->file_is_downloadable)) echo JText::_('COM_VIRTUEMART_DOWNLOADABLE') ;
					if(!empty($media->file_is_forSale)) echo  JText::_('COM_VIRTUEMART_FOR_SALE');

					?>
				</td>

				<!-- File title -->
				<td  class="hidden-phone autosize"><?php echo $media->file_name; ?></td>
				<!-- File extension -->
				<td  class="hidden-phone"><span class="vmicon vmicon-16-ext_<?php echo $media->file_extension; ?>"></span><?php echo $media->file_extension; ?></td>
				<!-- published -->
				<td><?php echo $published; ?></td>
				<td  class="hidden-phone"><?php echo $media->virtuemart_media_id; ?></td>
			</tr>
		<?php
	
			$i++;
		}
	}
	?>
	</tbody>
	<tfoot>
	<tr>
	<td colspan="15">
		<?php echo $this->pagination->getListFooter(); ?>
	</td>
	</tr>
	</tfoot>
	</table>
<!-- Hidden Fields -->
<?php if ($this->product_id) { ?>
	<input type="hidden" name="virtuemart_product_id" value="<?php echo $this->product_id; ?>" />
<?php } else if ($this->cat_id) { ?>
	<input type="hidden" name="virtuemart_category_id" value="<?php echo $this->cat_id; ?>" />
<?php }
	echo $this->addStandardHiddenToForm(); ?>
