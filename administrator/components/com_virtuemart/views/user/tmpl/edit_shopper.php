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
 * @version $Id: edit_shopper.php 6477 2012-09-24 14:33:54Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>

<fieldset>
	<legend>
		<?php echo JText::_('COM_VIRTUEMART_SHOPPER_FORM_LBL') ?>
	</legend>
	<table class="adminform">
		<tr>
			<td class="key">
				<label for="virtuemart_vendor_id">
					<?php echo JText::_('COM_VIRTUEMART_VENDOR') ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['vendors']; ?>
			</td>
		</tr>

		<tr>
			<td class="key">
				<label for="perms">
					<?php echo JText::_('COM_VIRTUEMART_USER_FORM_PERMS') ?>:
				</label>
			</td>
			<td>
				<?php
				echo $this->userDetails->perms;

				?>
			</td>
		</tr>

		<tr>
			<td class="key">
				<label for="customer_number">
					<?php echo JText::_('COM_VIRTUEMART_USER_FORM_CUSTOMER_NUMBER') ?>:
				</label>
			</td>
			<td>
				<input type="text" class="inputbox" name="customer_number" size="40" value="<?php echo  $this->lists['custnumber']; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="virtuemart_shoppergroup_id">
					<?php echo JText::_('COM_VIRTUEMART_SHOPPER_FORM_GROUP') ?>:
				</label>
			</td>
			<td>
				<?php echo $this->lists['shoppergroups']; ?>
			</td>
		</tr>
	</table>
</fieldset>


<?php if ($this->userDetails->JUser->get('id') ) { ?>
<fieldset>
	<legend>
		<?php echo JText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); ?>
	</legend>

	<?php echo $this->lists['shipTo']; ?>

</fieldset>
<?php } ?>

<fieldset>
	<legend>
		<?php echo JText::_('COM_VIRTUEMART_USERFIELDS_FORM_LBL'); ?>
	</legend>
<?php
	$_k = 0;
	$_set = false;
	$_table = false;
	$_hiddenFields = '';

	if (count($this->userFieldsBT['functions']) > 0) {
		echo '<script language="javascript">'."\n";
		echo join("\n", $this->userFieldsBT['functions']);
		echo '</script>'."\n";
	}
	for ($_i = 0, $_n = count($this->userFieldsBT['fields']); $_i < $_n; $_i++) {
		// Do this at the start of the loop, since we're using 'continue' below!
		if ($_i == 0) {
			$_field = current($this->userFieldsBT['fields']);
		} else {
			$_field = next($this->userFieldsBT['fields']);
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
?>
<input type="hidden" name="virtuemart_userinfo_id" value="<?php echo $this->userInfoID; ?>" />
<input type="hidden" name="address_type" value="BT" />
</fieldset>


<script language="javascript" type="text/javascript">
function gotocontact(id) {
	var form = document.adminForm;
	form.target = "_parent";
	form.contact_id.value = id;
	form.option.value = 'com_users';
	submitform('contact');
}
</script>


<fieldset>
	<legend>
		<?php echo JText::_('COM_VIRTUEMART_USER_FORM_LEGEND_USERDETAILS'); ?>
	</legend>
	<table class="admintable" cellspacing="1">


		<?php if ($this->lists['canBlock']) : ?>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_VIRTUEMART_USER_FORM_BLOCKUSER'); ?>
			</td>
			<td><fieldset class="radio">
				<?php echo $this->lists['block']; ?>
			</fieldset></td>
		</tr>
		<?php endif; ?>

		<?php if ($this->lists['canSetMailopt']) : ?>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_VIRTUEMART_USER_FORM_RECEIVESYSTEMEMAILS'); ?>
			</td>
			<td>
				<fieldset class="radio">
				<?php echo $this->lists['sendEmail']; ?>
				</fieldset>
			</td>
		</tr>

		<?php else : ?>
			<input type="hidden" name="sendEmail" value="0" />
		<?php endif; ?>

		<?php if( $this->userDetails->JUser ) : ?>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_VIRTUEMART_USER_FORM_REGISTERDATE'); ?>
			</td>
			<td>
				<?php echo $this->userDetails->JUser->get('registerDate');?>
			</td>
		</tr>

		<tr>
			<td class="key">
				<?php echo JText::_('COM_VIRTUEMART_USER_FORM_LASTVISITDATE'); ?>
			</td>
			<td>
				<?php echo $this->userDetails->JUser->get('lastvisitDate'); ?>
			</td>
		</tr>
		<?php endif; ?>
	</table>
</fieldset>


<fieldset>
	<legend>
		<?php echo JText::_('COM_VIRTUEMART_USER_FORM_LEGEND_PARAMETERS'); ?>
		</legend>
	<table class="admintable" cellspacing="1">
		<tr>
			<td>
			<?php
				if (is_callable(array($this->lists['params'], 'render'))) {
					echo $this->lists['params']->render('params');
				}
			?>
			</td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<legend>
		<?php echo JText::_('COM_VIRTUEMART_USER_FORM_LEGEND_CONTACTINFO'); ?>
	</legend>
	<?php if ( !$this->contactDetails ) { ?>
	<table class="admintable" cellspacing="1">
		<tr>
			<td>
				<br />
				<?php echo JText::_('COM_VIRTUEMART_USER_FORM_NOCONTACTDETAILS_1'); ?>
				<br />
				<?php echo JText::_('COM_VIRTUEMART_USER_FORM_NOCONTACTDETAILS_2'); ?>
				<br /><br />
			</td>
		</tr>
	</table>
	<?php  } else { ?>
	<table class="admintable" cellspacing="1">
		<tr>
			<td width="15%">
				<?php echo JText::_('COM_VIRTUEMART_USER_FORM_CONTACTDETAILS_NAME'); ?>:
			</td>
			<td>
				<strong><?php echo $this->contactDetails->name;?></strong>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JText::_('COM_VIRTUEMART_USER_FORM_CONTACTDETAILS_POSITION'); ?>:
			</td>
			<td >
				<strong><?php echo $this->contactDetails->con_position;?></strong>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JText::_('COM_VIRTUEMART_USER_FORM_CONTACTDETAILS_TELEPHONE'); ?>:
			</td>
			<td >
				<strong><?php echo $this->contactDetails->telephone;?></strong>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo JText::_('COM_VIRTUEMART_USER_FORM_CONTACTDETAILS_FAX'); ?>:
			</td>
			<td >
				<strong><?php echo $this->contactDetails->fax;?></strong>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<strong><?php echo $this->contactDetails->misc;?></strong>
			</td>
		</tr>
		<?php if ($this->contactDetails->image) { ?>
			<tr>
				<td></td>
				<td valign="top">
					<img src="<?php echo $mosConfig_live_site;?>/images/stories/<?php echo $this->contactDetails->image; ?>" align="middle" alt="Contact" />
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="2">
				<br />
				<input class="button" type="button" value="<?php echo JText::_('COM_VIRTUEMART_USER_FORM_CONTACTDETAILS_CHANGEBUTTON'); ?>" onclick="javascript: gotocontact( '<?php echo $this->contactDetails->id; ?>' )">
			</td>
		</tr>
	</table>
	<?php } ?>
</fieldset>

<input type="hidden" name="virtuemart_user_id" value="<?php echo $this->userDetails->JUser->get('id'); ?>" />
<input type="hidden" name="virtuemart_user_id[]" value="<?php echo $this->userDetails->JUser->get('id'); ?>" />
<input type="hidden" name="contact_id" value="" />
<input type="hidden" name="layout" value="<?php echo $this->_layout; ?>" />


