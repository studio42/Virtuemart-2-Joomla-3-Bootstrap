<?php
defined('_JEXEC') or die(); 
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
?>
	<div id="resultscounter"><?php echo $this->pagination->getResultsCounter(); ?></div>
	<table class="table table-striped">
	    <thead>
		<tr>
		    <th width="10">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
		    </th>
		    <th>
				<?php echo $this->sort('mf_name', 'COM_VIRTUEMART_MANUFACTURER_NAME') ; ?>
		    </th>
		    <th class="autosize">
				<?php echo $this->sort('mf_email', 'JGLOBAL_EMAIL') ; ?>
		    </th>
		    <!--<th class="hidden-phone">
				<?php echo $this->sort('mf_desc', 'COM_VIRTUEMART_MANUFACTURER_DESCRIPTION'); ?>
		    </th>-->
		    <th class="hidden-phone">
				<?php echo $this->sort('mf_category_name', 'COM_VIRTUEMART_MANUFACTURER_CATEGORY'); ?>
		    </th>
		    <th class="autosize">
				<?php echo $this->sort('mf_url', 'COM_VIRTUEMART_URL'); ?>
		    </th>
		    <th width="20" class="autosize">
				<?php echo JText::_('COM_VIRTUEMART_PUBLISHED'); ?>
		    </th>
		      <th class="hidden-phone"><?php echo $this->sort('m.virtuemart_manufacturer_id', 'COM_VIRTUEMART_ID')  ?></th>
		</tr>
	    </thead>
		<tbody>
	    <?php

	    for ($i=0, $n=count( $this->manufacturers ); $i < $n; $i++) {
		$row = $this->manufacturers[$i];

		$checked = JHTML::_('grid.id', $i, $row->virtuemart_manufacturer_id,null,'virtuemart_manufacturer_id');
		$canDo = $this->canChange($row->created_by);
		$published = $this->toggle( $row->published, $i, 'published' ,$canDo );
		$categoryLink = $this->editLink($row->virtuemart_manufacturercategories_id, $row->mf_category_name, 'virtuemart_manufacturercategories_id','','manufacturercategories');
		?>
	    <tr >
		<td width="10">
			<?php echo $checked; ?>
		</td>
		<td align="left">
		    <?php echo $this->editLink($row->virtuemart_manufacturer_id, $row->mf_name, 'virtuemart_manufacturer_id') ?>
			<div class="small visible-phone">
				<?php echo $categoryLink ?>
			</div>
		</td>
		<td align="left" class="autosize">
			<?php if (!empty($row->mf_email)) echo  '<a href="mailto:'.$row->mf_name.'<'.$row->mf_email.'>">'.$row->mf_email ; ?>
		</td>
		<!--<td class="hidden-phone">
			<?php echo $row->mf_desc; ?>
		</td>-->
		<td class="hidden-phone">
			<?php echo $categoryLink ?>
		</td>
		<td class="autosize">
			<?php if (!empty($row->mf_url)) echo '<a href="'. $row->mf_url.'">'. $row->mf_url ; ?>
		</td>
		<td align="center">
			<?php echo $published; ?>
		</td>
		<td align="right" class="hidden-phone">
		    <?php echo $row->virtuemart_manufacturer_id; ?>
		</td>
	    </tr>
		<?php

	    }
	    ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="8">
				<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<?php echo $this->addStandardHiddenToForm(); ?>
