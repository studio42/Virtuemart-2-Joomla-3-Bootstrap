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
* @version $Id: default.php 6559 2012-10-18 13:22:30Z Milbo $
*/

AdminUIHelper::startAdminArea();

jimport('joomla.filesystem.file');

/* Get the component name */
$option = JRequest::getWord('option');

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div id="header">
		<div id="filterbox">
		<table>
		  <tr>
			 <td align="left" width="100%">
				<?php echo $this->displayDefaultViewSearch('COM_VIRTUEMART_NAME','searchMedia') .' '. $this->lists['search_type']; ?>
			 </td>
		  </tr>
		</table>
		</div>
		<div id="resultscounter"><?php echo $this->pagination->getResultsCounter();?></div>
	</div>
<?php
$productfileslist = $this->files;
//$roles = $this->productfilesroles;
?>
	<table class="adminlist" cellspacing="0" cellpadding="0">
	<thead>
	<tr>
		<th><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count($productfileslist ); ?>')" /></th>
		<th><?php echo JText::_('COM_VIRTUEMART_PRODUCT_NAME'); ?></th>
		<th><?php echo $this->sort('file_title', 'COM_VIRTUEMART_FILES_LIST_FILETITLE') ?></th>
		<th><?php echo $this->sort('file_type', 'COM_VIRTUEMART_FILES_LIST_ROLE') ?></th>
		<th><?php echo JText::_('COM_VIRTUEMART_VIEW'); ?></th>
		<th><?php echo JText::_('COM_VIRTUEMART_FILES_LIST_FILENAME'); ?></th>
		<th><?php echo JText::_('COM_VIRTUEMART_FILES_LIST_FILETYPE'); ?></th>
		<th><?php echo JText::_('COM_VIRTUEMART_PUBLISH'); ?></th>
	  <th><?php echo $this->sort('virtuemart_media_id', 'COM_VIRTUEMART_ID')  ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if (count($productfileslist) > 0) {
		$i = 0;
		$k = 0;
		foreach ($productfileslist as $key => $productfile) {

			$checked = JHTML::_('grid.id', $i , $productfile->virtuemart_media_id,null,'virtuemart_media_id');
			if (!is_null($productfile->virtuemart_media_id)) $published = JHTML::_('grid.published', $productfile, $i );
			else $published = '';
			?>
			<tr class="row<?php echo $k ; ?>">
				<!-- Checkbox -->
				<td><?php echo $checked;   ?></td>
				<!-- Product name -->
				<?php
				$link = ""; //"index.php?view=media&limitstart=".$pagination->limitstart."&keyword=".urlencode($keyword)."&option=".$option;
				?>
				<td><?php echo JHTML::_('link', JRoute::_($link), empty($productfile->product_name)? '': $productfile->product_name); ?></td>
				<!-- File name -->
				<?php
				$link = 'index.php?option='.$option.'&view=media&task=edit&virtuemart_media_id[]='.$productfile->virtuemart_media_id;
				?>
				<td><?php echo JHTML::_('link', JRoute::_($link), $productfile->file_title, array('title' => JText::_('COM_VIRTUEMART_EDIT').' '.$productfile->file_title)); ?></td>
				<!-- File role -->
				<td><?php
					//Just to have something, we could make this nicer with Icons
					if(!empty($productfile->file_is_product_image)) echo JText::_('COM_VIRTUEMART_'.$productfile->file_type.'_IMAGE') ;
					if(!empty($productfile->file_is_downloadable)) echo JText::_('COM_VIRTUEMART_DOWNLOADABLE') ;
					if(!empty($productfile->file_is_forSale)) echo  JText::_('COM_VIRTUEMART_FOR_SALE');

					?>
				</td>
				<!-- Preview -->
				<td>
				<?php
					echo $productfile->displayMediaThumb();

				?>
				</td>
				<!-- File title -->
				<td><?php echo $productfile->file_name; ?></td>
				<!-- File extension -->
				<td><span class="vmicon vmicon-16-ext_<?php echo $productfile->file_extension; ?>"></span><?php echo $productfile->file_extension; ?></td>
				<!-- published -->
				<td><?php echo $published; ?></td>
				<td><?php echo $productfile->virtuemart_media_id; ?></td>
			</tr>
		<?php
			$k = 1 - $k;
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
<?php if (JRequest::getInt('virtuemart_product_id', false)) { ?>
	<input type="hidden" name="virtuemart_product_id" value="<?php echo JRequest::getInt('virtuemart_product_id',0); ?>" />
<?php } ?>
	<?php echo $this->addStandardHiddenToForm(); ?>
</form>
<?php AdminUIHelper::endAdminArea(); ?>