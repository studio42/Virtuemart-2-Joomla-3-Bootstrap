<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Country
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 6326 2012-08-08 14:14:28Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

$states = JText::_('COM_VIRTUEMART_STATE_S');
?>
	<div id="resultscounter"><?php echo $this->pagination->getResultsCounter(); ?></div>
	<table class="table table-striped">
	    <thead>
		<tr>
			<th width="10">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
			</th>
			<th>
				<?php echo $this->sort('country_name') ?>
		    </th>
				<?php /* TODO not implemented				    <th>
				<?php echo JText::_('COM_VIRTUEMART_ZONE_ASSIGN_CURRENT_LBL'); ?>
				</th> */ ?>
		    <th class="hidden-phone">
				<?php echo $this->sort('country_2_code') ?>
		    </th>
		    <th class="hidden-phone">
				<?php echo $this->sort('country_3_code') ?>
		    </th>
		    <th width="20">
				<?php echo JText::_('COM_VIRTUEMART_PUBLISHED'); ?>
		    </th>
		</tr>
	    </thead>
	    <?php
	    for ($i=0, $n=count( $this->countries ); $i < $n; $i++) {
		$row = $this->countries[$i];

		$checked = JHTML::_('grid.id', $i, $row->virtuemart_country_id);
		$published = $this->toggle( $row->published, $i, 'published');
		$statelink	= JROUTE::_('index.php?option=com_virtuemart&view=state&view=state&virtuemart_country_id=' . $row->virtuemart_country_id);
		?>
	    <tr >
		<td width="10">
			<?php echo $checked; ?>
		</td>
		<td align="left">
			<?php
			$prefix="COM_VIRTUEMART_COUNTRY_";
			$country_string= Jtext::_($prefix.$row->country_3_code); ?>
		    <?php echo $this->editLink($row->virtuemart_country_id, $row->country_name) ?>&nbsp;
			<?php
			$lang =JFactory::getLanguage();
			if ($lang->hasKey($prefix.$row->country_3_code)) {
				echo "(".$country_string.") ";
			}
			?>

		    <a class="hasTooltip" title="<?php echo JText::sprintf('COM_VIRTUEMART_STATES_VIEW_LINK', $country_string ); ?>" href="<?php echo $statelink; ?>">[<?php echo $states ?>]</a>
		</td>
		<?php /* TODO not implemented				<td align="left">
			<?php echo $row->virtuemart_worldzone_id; ?>
		</td> */ ?>
		<td class="hidden-phone"> 
			<?php echo $row->country_2_code; ?>
		</td>
		<td class="hidden-phone">
			<?php echo $row->country_3_code ; ?>
		</td>
		<td align="center">
			<?php echo $published; ?>
		</td>
	    </tr>
		<?php
	    }
	    ?>
	    <tfoot>
		<tr>
		    <td colspan="10">
			<?php echo $this->pagination->getListFooter(); ?>
		    </td>
		</tr>
	    </tfoot>
	</table>
	<?php echo $this->addStandardHiddenToForm();  ?>
