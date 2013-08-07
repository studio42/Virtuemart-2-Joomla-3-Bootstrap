<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Currency
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 6475 2012-09-21 11:54:21Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
	<div id="resultscounter"><?php echo $this->pagination->getResultsCounter(); ?></div>
	<table class="table table-striped">
	    <thead>
		<tr>
		    <th width="10">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
		    </th>
		    <th >
				<?php  echo $this->sort('currency_name','COM_VIRTUEMART_CURRENCY') ; ?>
		    </th>
		    <th width="80">
			<?php echo $this->sort('currency_exchange_rate') ?>
		    </th>
		    <th width="20">
			<?php echo JText::_('COM_VIRTUEMART_CURRENCY_SYMBOL'); ?>
		    </th>
<?php /*		    <th width="10">
			<?php echo JText::_('COM_VIRTUEMART_CURRENCY_LIST_CODE_2'); ?>
		    </th> */?>
		    <th width="20">
			<?php  echo $this->sort('currency_code_3') ?>
		    </th>
             <th width="20">
			<?php echo JText::_('COM_VIRTUEMART_CURRENCY_NUMERIC_CODE'); ?>
		    </th>
<?php /*		    <th >
				<?php echo JText::_('COM_VIRTUEMART_CURRENCY_START_DATE'); ?>
			</th>
			<th >
				<?php echo JText::_('COM_VIRTUEMART_CURRENCY_END_DATE'); ?>
			</th> */?>
			<th width="10">
				<?php echo JText::_('COM_VIRTUEMART_PUBLISHED'); ?>
			</th>
		<?php /*	<th width="10">
				<?php echo JText::_('COM_VIRTUEMART_SHARED'); ?>
			</th> */ ?>
		</tr>
	    </thead>
	    <?php

	    for ($i=0, $n=count( $this->currencies ); $i < $n; $i++) {
		$row = $this->currencies[$i];

		$checked = JHTML::_('grid.id', $i, $row->virtuemart_currency_id);
		$published = $this->toggle( $row->published, $i, 'published');
		?>
	    <tr >
		<td align="center">
			<?php echo $checked; ?>
		</td>
		<td align="left">
		    <?php echo $this->editLink($row->virtuemart_currency_id, $row->currency_name) ?>
		</td>
		<td align="left">
			<?php echo $row->currency_exchange_rate; ?>
		</td>
		<td align="left">
			<?php echo $row->currency_symbol; ?>
		</td>
<?php /*<td align="left">
			<?php echo $row->currency_code_2; ?>
		</td>  */ ?>
		<td align="left">
			<?php echo $row->currency_code_3; ?>
		</td>
        <td align="left">
			<?php echo $row->currency_numeric_code; ?>
		</td>
		<td align="center">
			<?php echo $published; ?>
		</td>
		<?php /*
		<td align="center">
			<?php echo $row->shared; ?>
		</td>	*/?>
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

	<?php echo $this->addStandardHiddenToForm(); ?>
