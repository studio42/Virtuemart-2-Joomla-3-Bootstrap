<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage State
* @author RickG, Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: edit.php 5225 2012-01-06 01:50:19Z electrocity $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<div class="col50">
	<fieldset>
<?php /*	<legend><?php echo JText::_('COM_VIRTUEMART_STATE_DETAILS'); ?></legend> */?>
	<legend><?php echo JHTML::_('link','index.php?option=com_virtuemart&view=state&virtuemart_country_id='.$this->virtuemart_country_id,JText::sprintf('COM_VIRTUEMART_STATE_COUNTRY',$this->country_name).' '. JText::_('COM_VIRTUEMART_DETAILS') ); ?></legend>
	<table class="admintable">
		<tr>
			<td width="110" class="key">
				<label for="state_name">
					<?php echo JText::_('COM_VIRTUEMART_STATE_NAME'); ?>
				</label>
			</td>
			<td>
				<input class="inputbox" type="text" name="state_name" id="state_name" size="50" value="<?php echo $this->state->state_name; ?>" />
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="published">
					<?php echo JText::_('COM_VIRTUEMART_PUBLISHED'); ?>
				</label>
			</td>
			<td><fieldset class="radio">

				<?php echo JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $this->state->published); ?>

			</fieldset></td>
		</tr>
		<tr>
		<td width="110" class="key">
				<label for="virtuemart_worldzone_id">
					<?php echo JText::_('COM_VIRTUEMART_WORLDZONE'); ?>
				</label>
			</td>
			<td>
				<?php echo JHTML::_('Select.genericlist', $this->worldZones, 'virtuemart_worldzone_id', '', 'virtuemart_worldzone_id', 'zone_name', $this->state->virtuemart_worldzone_id); ?>
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="state_3_code">
					<?php echo JText::_('COM_VIRTUEMART_STATE_3_CODE'); ?>
				</label>
			</td>
			<td>
				<input class="inputbox" type="text" name="state_3_code" id="state_3_code" size="10" value="<?php echo $this->state->state_3_code; ?>" />
			</td>
		</tr>
		<tr>
			<td width="110" class="key">
				<label for="state_2_code">
					<?php echo JText::_('COM_VIRTUEMART_STATE_2_CODE'); ?>
				</label>
			</td>
			<td>
				<?php /* echo JHTML::_('Select.radiolist', $this->worldZones, 'virtuemart_worldzone_id', '', 'virtuemart_worldzone_id', 'zone_name', $this->country->virtuemart_worldzone_id);*/ ?>
				<input class="inputbox" type="text" name="state_2_code" id="state_2_code" size="10" value="<?php echo $this->state->state_2_code; ?>" />
			</td>
		</tr>
	</table>
	</fieldset>
</div>

	<input type="hidden" name="virtuemart_country_id" value="<?php echo $this->virtuemart_country_id; ?>" />
	<input type="hidden" name="virtuemart_state_id" value="<?php echo $this->state->virtuemart_state_id; ?>" />

	<?php echo $this->addStandardHiddenToForm(); ?>
</form>


<?php AdminUIHelper::endAdminArea(); ?>