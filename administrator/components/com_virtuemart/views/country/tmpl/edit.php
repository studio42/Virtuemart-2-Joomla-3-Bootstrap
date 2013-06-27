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
* @version $Id: edit.php 6326 2012-08-08 14:14:28Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();
AdminUIHelper::imitateTabs('start','COM_VIRTUEMART_COUNTRY_DETAILS');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">


<div class="col50">
	<fieldset>
	<legend><?php echo JText::_('COM_VIRTUEMART_COUNTRY_DETAILS'); ?></legend>
	<table class="admintable">
		<?php
		$lang = JFactory::getLanguage();
		$prefix="COM_VIRTUEMART_COUNTRY_";
		$country_string = $lang->hasKey($prefix.$this->country->country_3_code) ? ' (' . JText::_($prefix.$this->country->country_3_code) . ')' : ' ';
        ?>
		<?php echo VmHTML::row('input','COM_VIRTUEMART_COUNTRY_REFERENCE_NAME','country_name',$this->country->country_name, 'class="inputbox"', '', 50, 50, $country_string); ?>

		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_PUBLISH','published',$this->country->published); ?>
<?php /* TODO not implemented		<tr>
			<td width="110" class="key">
				<label for="title">
					<?php echo JText::_('COM_VIRTUEMART_WORLDZONE'); ?>:
				</label>
			</td>
			<td>
				<?php echo JHTML::_('Select.genericlist', $this->worldZones, 'virtuemart_worldzone_id', '', 'virtuemart_worldzone_id', 'zone_name', $this->country->virtuemart_worldzone_id); ?>
			</td>
		</tr>*/ ?>
		<?php echo VmHTML::row('input','COM_VIRTUEMART_COUNTRY_3_CODE','country_3_code',$this->country->country_3_code); ?>
		<?php echo VmHTML::row('input','COM_VIRTUEMART_COUNTRY_2_CODE','country_2_code',$this->country->country_2_code); ?>
	</table>
	</fieldset>
</div>

	<input type="hidden" name="virtuemart_country_id" value="<?php echo $this->country->virtuemart_country_id; ?>" />

	<?php echo $this->addStandardHiddenToForm(); ?>
</form>

<?php 
AdminUIHelper::imitateTabs('end');
AdminUIHelper::endAdminArea(); ?>