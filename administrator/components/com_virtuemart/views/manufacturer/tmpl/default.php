<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Manufacturer
* @author Patrick Kohl
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 5814 2012-04-06 10:23:12Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div id="header">
<div id="filterbox">
	<table class="">
		<tr>
			<td align="left">
			<?php echo $this->displayDefaultViewSearch() ?>
			</td>

		</tr>
	</table>
	</div>
	<div id="resultscounter"><?php echo $this->pagination->getResultsCounter(); ?></div>

</div>
    <div id="editcell">
	<table class="adminlist" cellspacing="0" cellpadding="0">
	    <thead>
		<tr>
		    <th width="10">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->manufacturers); ?>);" />
		    </th>
		    <th>
				<?php echo $this->sort('mf_name', 'COM_VIRTUEMART_MANUFACTURER_NAME') ; ?>
		    </th>
		    <th>
				<?php echo $this->sort('mf_email', 'COM_VIRTUEMART_MANUFACTURER_EMAIL') ; ?>
		    </th>
		    <th>
				<?php echo $this->sort('mf_desc', 'COM_VIRTUEMART_MANUFACTURER_DESCRIPTION'); ?>
		    </th>
		    <th>
				<?php echo $this->sort('mf_category_name', 'COM_VIRTUEMART_MANUFACTURER_CATEGORY'); ?>
		    </th>
		    <th>
				<?php echo $this->sort('mf_url', 'COM_VIRTUEMART_MANUFACTURER_URL'); ?>
		    </th>
		    <th width="20">
				<?php echo JText::_('COM_VIRTUEMART_PUBLISH'); ?>
		    </th>
		      <th><?php echo $this->sort('m.virtuemart_manufacturer_id', 'COM_VIRTUEMART_ID')  ?></th>
		</tr>
	    </thead>
	    <?php
	    $k = 0;
	    for ($i=0, $n=count( $this->manufacturers ); $i < $n; $i++) {
		$row = $this->manufacturers[$i];

		$checked = JHTML::_('grid.id', $i, $row->virtuemart_manufacturer_id,null,'virtuemart_manufacturer_id');
		$published = JHTML::_('grid.published', $row, $i);
		$editlink = JROUTE::_('index.php?option=com_virtuemart&view=manufacturer&task=edit&virtuemart_manufacturer_id=' . $row->virtuemart_manufacturer_id);
		?>
	    <tr class="row<?php echo $k ; ?>">
		<td width="10">
			<?php echo $checked; ?>
		</td>
		<td align="left">
		    <a href="<?php echo $editlink; ?>"><?php echo $row->mf_name; ?></a>

		</td>
		<td align="left">
			<?php if (!empty($row->mf_email)) echo  '<a href="mailto:'.$row->mf_name.'<'.$row->mf_email.'>">'.$row->mf_email ; ?>
		</td>
		<td>
			<?php echo $row->mf_desc; ?>
		</td>
		<td>
			<?php echo $row->mf_category_name; ?>
		</td>
		<td>
			<?php if (!empty($row->mf_url)) echo '<a href="'. $row->mf_url.'">'. $row->mf_url ; ?>
		</td>
		<td align="center">
			<?php echo $published; ?>
		</td>
		<td align="right">
		    <?php echo $row->virtuemart_manufacturer_id; ?>
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

	<?php echo $this->addStandardHiddenToForm(); ?>
</form>


<?php AdminUIHelper::endAdminArea(); ?>