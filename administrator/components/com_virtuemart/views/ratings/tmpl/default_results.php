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
defined('_JEXEC') or die();
$option = JRequest::getWord('option');

?>
	<div id="resultscounter"><?php echo $this->pagination->getResultsCounter(); ?></div>

	<table class="table table-striped">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
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
	
			$keyword = JRequest::getWord('keyword');
			foreach ($this->ratingslist as $key => $review) {
				$checked = JHTML::_('grid.id', $i , $review->virtuemart_rating_id);
				$published = $this->toggle( $review->published, $i, 'published');
				?>
				<tr >
					<!-- Checkbox -->
					<td><?php echo $checked; ?></td>
					<!-- Username + time -->
					<td>
					<?php echo $this->editLink($review->virtuemart_product_id,vmJsApi::date($review->created_on,'LC2',true) ,'virtuemart_product_id',array( "title" => JText::_('COM_VIRTUEMART_RATING_EDIT_TITLE')),null,'listreviews'); ?>
					</td>
					<!-- Product edit link -->
					<td>
						<?php echo $this->editLink(	$review->virtuemart_product_id,	$review->product_name, 		'virtuemart_product_id',
							array('class'=> 'hasTooltip', 'title' => JText::_('COM_VIRTUEMART_EDIT').' '.$review->product_name,'product')	) ?>
					</td>
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
		
				$i++;
			}
		}
		?>
		</tbody>
		<tfoot>
			<tr>
			<td colspan="5">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
			</tr>
		</tfoot>
	</table>
	<?php echo $this->addStandardHiddenToForm(); ?>
