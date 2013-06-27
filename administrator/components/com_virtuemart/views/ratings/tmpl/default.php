<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage   ratings
* @author
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: ratings.php 2233 2010-01-21 21:21:29Z SimonHodgkiss $
*/

// @todo a link or tooltip to show the details of shop user who posted comment
// @todo more flexible templating, theming, etc..

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
	<div id="resultscounter" ><?php echo $this->pagination->getResultsCounter();?></div>
</div>

<div style="text-align: left;">
	<table class="adminlist" cellspacing="0" cellpadding="0">
	<thead>
	<tr>
		<th><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count($this->ratingslist); ?>')" /></th>
		<th><?php echo $this->sort('created_on', 'COM_VIRTUEMART_DATE') ; ?></th>
		<th><?php echo $this->sort('product_name') ; ?></th>
		<th><?php echo $this->sort('rating', 'COM_VIRTUEMART_RATE_NOM') ; ?></th>
		<th width="20"><?php echo $this->sort('published') ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if (count($this->ratingslist) > 0) {
		$i = 0;
		$k = 0;
		$keyword = JRequest::getWord('keyword');
		foreach ($this->ratingslist as $key => $review) {
			$checked = JHTML::_('grid.id', $i , $review->virtuemart_rating_id);
			$published = JHTML::_('grid.published', $review, $i );
			?>
			<tr class="row<?php echo $k ; ?>">
				<!-- Checkbox -->
				<td><?php echo $checked; ?></td>
				<!-- Username + time -->
				<?php $link = 'index.php?option='.$option.'&view=ratings&task=listreviews&virtuemart_product_id='.$review->virtuemart_product_id; ?>
				<td><?php echo JHTML::_('link', $link,vmJsApi::date($review->created_on,'LC2',true) , array("title" => JText::_('COM_VIRTUEMART_RATING_EDIT_TITLE'))); ?></td>
				<!-- Product name -->
				<?php $link = 'index.php?option='.$option.'&view=product&task=edit&virtuemart_product_id='.$review->virtuemart_product_id ; ?>
				<td><?php echo JHTML::_('link', JRoute::_($link), $review->product_name, array('title' => JText::_('COM_VIRTUEMART_EDIT').' '.$review->product_name)); ?></td>
				<!-- Stars rating -->
				<td align="center">
					
					<?php // Rating Stars output
					$maxrating = VmConfig::get('vm_maximum_rating_scale', 5);
				    $ratingwidth = round($review->rating) * 24;
				    ?>
	
				    <span title="<?php echo (JText::_("COM_VIRTUEMART_RATING_TITLE").' '. round($review->rating) . '/' . $maxrating) ?>" class="ratingbox" style="display:inline-block;">
						<span class="stars-orange" style="width:<?php echo $ratingwidth.'px'; ?>">
						</span>
				    </span>

				</td>
				<!-- published -->
				<td><?php echo $published; ?></td>
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
		<td colspan="16">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
		</tr>
	</tfoot>
	</table>
</div>
<!-- Hidden Fields -->
	<?php echo $this->addStandardHiddenToForm(); ?>
</form>
<?php AdminUIHelper::endAdminArea(); ?>

