<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage 	ratings
* @author
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: ratings_edit.php 2233 2010-01-21 21:21:29Z SimonHodgkiss $
*
* @todo decide to allow or not a JEditor here instead of a textarea
* @todo comment length check should also occur on the server side (model?)
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();
/* Get the component name */
$option = JRequest::getWord('option');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div id="header">
	<div id="filterbox">
	<table>
	  <tr>
		 <td align="left" width="100%">
			<?php echo JText::_('COM_VIRTUEMART_FILTER'); ?>:
			<input type="text" name="filter_ratings" value="<?php echo JRequest::getVar('filter_ratings', ''); ?>" />
			<button onclick="this.form.submit();"><?php echo JText::_('COM_VIRTUEMART_GO'); ?></button>
			<button onclick="document.adminForm.filter_ratings.value='';"><?php echo JText::_('COM_VIRTUEMART_RESET'); ?></button>
		 </td>
	  </tr>
	</table>
	</div>
	<div id="resultscounter"><?php echo $this->pagination->getResultsCounter();?></div>
</div>


<div style="text-align: left;">
	<table class="adminlist" cellspacing="0" cellpadding="0">
	<thead>
	<tr>
		<th width="20"><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count($this->reviewslist); ?>')" /></th>
		<th><?php echo $this->sort('pr.created_on', 'COM_VIRTUEMART_DATE') ; ?></th>
		<th><?php echo $this->sort('product_name') ; ?></th>
		<th><?php echo $this->sort('vote', 'COM_VIRTUEMART_RATE_NOM') ; ?></th>
		<th width="20"><?php echo $this->sort('published') ; ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if (count($this->reviewslist) > 0) {
		$i = 2;
		//$k = 0;
		$keyword = JRequest::getWord('keyword');
		foreach ($this->reviewslist as $key => $review) {
			//vmdebug('my review ',$review);
			$checked = JHTML::_('grid.id', $i , $review->virtuemart_rating_review_id ,null, 'virtuemart_rating_review_id');
			$published = JHTML::_('grid.published', $review, $i);
			?>
			<tr class="row<?php echo ($i)%2 ; ?>">
				<!-- Checkbox -->
				<td><?php echo $checked; ?></td>
				<!-- Username + time -->
				<?php $link = 'index.php?option='.$option.'&view=ratings&task=edit_review&virtuemart_rating_review_id='.$review->virtuemart_rating_review_id; ?>
				<td><?php echo JHTML::_('link', $link, $review->customer.' ('.vmJsApi::date($review->created_on,'LC2',true).')', array("title" => JText::_('COM_VIRTUEMART_RATING_EDIT_TITLE'))); ?></td>
				<!-- Product name TODO Add paren_id in LINK ? not existing here -->
				<?php $link = 'index.php?option='.$option.'&view=product&task=edit&virtuemart_product_id='.$review->virtuemart_product_id ?>
				<td><?php echo JHTML::_('link', JRoute::_($link), $review->product_name, array('title' => JText::_('COM_VIRTUEMART_EDIT').' '.$review->product_name)); ?></td>
				<!-- Stars rating -->
				<td align="center">
					
					<?php // echo JHTML::_('image', JURI::root().'/components/com_virtuemart/assets/images/stars/'.round($review->vote).'.gif',$review->vote,array("title" => (JText::_('COM_VIRTUEMART_RATING_TITLE').' : '. $review->vote . ' :: ' . $this->max_rating)));
					$maxrating = VmConfig::get('vm_maximum_rating_scale', 5);
				    $ratingwidth = round($review->review_rating) * 24;
				    ?>
	
				    <span title="<?php echo (JText::_("COM_VIRTUEMART_RATING_TITLE").' '. round($review->review_rating) . '/' . $maxrating) ?>" class="ratingbox" style="display:inline-block;">
						<span class="stars-orange" style="width:<?php echo $ratingwidth.'px'; ?>">
						</span>
				    </span>
				
				</td>
				<!-- published -->
				<td><?php echo $published; ?></td>
			</tr>
		<?php
			//$k = 1 - $k;
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
</div>
<!-- Hidden Fields -->
	<input type="hidden" name="layout" value="list_reviews" />
	<input type="hidden" name="virtuemart_product_id" value="<?php echo JRequest::getVar('virtuemart_product_id', 0); ?>" />
	<?php echo $this->addStandardHiddenToForm(null,'listreviews'); ?>
</form>
<?php AdminUIHelper::endAdminArea(); ?>

