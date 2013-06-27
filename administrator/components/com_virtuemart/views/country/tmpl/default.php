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
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();
$states = JText::_('COM_VIRTUEMART_STATE_S');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div id="header">
	<div id="filterbox">
		<table>
			<tr>
				<td align="left" width="100%">
					<?php echo JText::_('COM_VIRTUEMART_FILTER') ?>:
					&nbsp;<input type="text" value="<?php echo JRequest::getVar('filter_country'); ?>" name="filter_country" size="25" />
					<button onclick="this.form.submit();"><?php echo JText::_('COM_VIRTUEMART_GO'); ?></button>
				</td>
			</tr>
		</table>
		</div>
		<div id="resultscounter"><?php echo $this->pagination->getResultsCounter();?></div>
	</div>
    <div id="editcell">
	<table class="adminlist" cellspacing="0" cellpadding="0">
	    <thead>
		<tr>
			<th width="10">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->countries); ?>);" />
			</th>
			<th>
				<?php echo $this->sort('country_name') ?>
		    </th>
				<?php /* TODO not implemented				    <th>
				<?php echo JText::_('COM_VIRTUEMART_ZONE_ASSIGN_CURRENT_LBL'); ?>
				</th> */ ?>
		    <th>
				<?php echo $this->sort('country_2_code') ?>
		    </th>
		    <th>
				<?php echo $this->sort('country_3_code') ?>
		    </th>
		    <th width="20">
				<?php echo JText::_('COM_VIRTUEMART_PUBLISHED'); ?>
		    </th>
		</tr>
	    </thead>
	    <?php
	    $k = 0;
	    for ($i=0, $n=count( $this->countries ); $i < $n; $i++) {
		$row = $this->countries[$i];

		$checked = JHTML::_('grid.id', $i, $row->virtuemart_country_id);
		$published = JHTML::_('grid.published', $row, $i);
		$editlink = JROUTE::_('index.php?option=com_virtuemart&view=country&task=edit&cid[]=' . $row->virtuemart_country_id);
		$statelink	= JROUTE::_('index.php?option=com_virtuemart&view=state&view=state&virtuemart_country_id=' . $row->virtuemart_country_id);
		?>
	    <tr class="row<?php echo $k ; ?>">
		<td width="10">
			<?php echo $checked; ?>
		</td>
		<td align="left">
			<?php
			$prefix="COM_VIRTUEMART_COUNTRY_";
			$country_string= Jtext::_($prefix.$row->country_3_code); ?>
		    <a href="<?php echo $editlink; ?>"><?php echo $row->country_name ?> </a>&nbsp;
			<?php
			$lang =JFactory::getLanguage();
			if ($lang->hasKey($prefix.$row->country_3_code)) {
				echo "(".$country_string.") ";
			}
			?>

		    <a title="<?php echo JText::sprintf('COM_VIRTUEMART_STATES_VIEW_LINK', $country_string ); ?>" href="<?php echo $statelink; ?>">[<?php echo $states ?>]</a>
		</td>
		<?php /* TODO not implemented				<td align="left">
			<?php echo $row->virtuemart_worldzone_id; ?>
		</td> */ ?>
		<td> 
			<?php echo $row->country_2_code; ?>
		</td>
		<td>
			<?php echo $row->country_3_code ; ?>
		</td>
		<td align="center">
			<?php echo $published; ?>
		</td>
	    </tr>
		<?php
		$k = 1 - $k;
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
    </div>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['filter_order_Dir']; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['filter_order']; ?>" />
    <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="controller" value="country" />
    <input type="hidden" name="view" value="country" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>


<?php AdminUIHelper::endAdminArea(); ?>