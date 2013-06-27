<?php
/**
 *
 * Modify user form view, User info
 *
 * @package	VirtueMart
 * @subpackage User
 * @author Oscar van Eijk
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: edit_shipto.php 5843 2012-04-09 17:29:17Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();

?>

<fieldset>
	<legend>
		<?php echo JText::_('COM_VIRTUEMART_SHOPPER_FORM_SHIPTO_LBL'); ?>
	</legend>
<?php
	$_k = 0;
	$_set = false;
	$_table = false;
	$_hiddenFields = '';
// 	vmdebug('shipToFields',$this->shipToFields);
	if (count($this->shipToFields['functions']) > 0) {
		echo '<script language="javascript">'."\n";
		echo join("\n", $this->shipToFields['functions']);
		echo '</script>'."\n";
	}
	for ($_i = 0, $_n = count($this->shipToFields['fields']); $_i < $_n; $_i++) {
		// Do this at the start of the loop, since we're using 'continue' below!
		if ($_i == 0) {
			$_field = current($this->shipToFields['fields']);
		} else {
			$_field = next($this->shipToFields['fields']);
		}

		if ($_field['hidden'] == true) {
			$_hiddenFields .= $_field['formcode']."\n";
			continue;
		}
		if ($_field['type'] == 'delimiter') {
			if ($_set) {
				// We're in Fieldset. Close this one and start a new
				if ($_table) {
					echo '	</table>'."\n";
					$_table = false;
				}
				echo '</fieldset>'."\n";
			}
			$_set = true;
			echo '<fieldset>'."\n";
			echo '	<legend>'."\n";
			echo '		' . $_field['title'];
			echo '	</legend>'."\n";
			continue;
		}

		if (!$_table) {
			// A table hasn't been opened as well. We need one here,
			echo '	<table class="adminform">'."\n";
			$_table = true;
		}
		echo '		<tr>'."\n";
		echo '			<td class="key">'."\n";
		echo '				<label for="'.$_field['name'].'_field">'."\n";
		echo '					'.$_field['title'] . ($_field['required']?' *': '')."\n";
		echo '				</label>'."\n";
		echo '			</td>'."\n";
		echo '			<td>'."\n";
		echo '				'.$_field['formcode']."\n";
		echo '			</td>'."\n";
		echo '		</tr>'."\n";
	}

	if ($_table) {
		echo '	</table>'."\n";
	}
	if ($_set) {
		echo '</fieldset>'."\n";
	}
	echo $_hiddenFields;

if(!empty($this->virtuemart_userinfo_id)){
	echo '<input type="hidden" name="shipto_virtuemart_userinfo_id" value="'.$this->shipToId.'" />';
}
?>

</fieldset>

<?php

	AdminUIHelper::endAdminArea();

?>
