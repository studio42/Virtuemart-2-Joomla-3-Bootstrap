<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage 	ratings
* @author
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: ratings_edit.php 2233 2010-01-21 21:21:29Z SimonHodgkiss $
*
* @todo decide to allow or not a JEditor here instead of a textarea
* @todo comment length check should also occur on the server side (model?)
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
vmJsApi::cssSite();
AdminUIHelper::startAdminArea();
AdminUIHelper::imitateTabs('start','COM_VIRTUEMART_REVIEW_DETAILS');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<div class="col50">
<fieldset>
<legend><?php echo JText::_('COM_VIRTUEMART_REVIEW_DETAILS'); ?></legend>
<table class="admintable" summary="<?php echo JText::_('COM_VIRTUEMART_RATING_EDIT_TITLE');?>">
	<tr>
		<td width="24%" align="left" valign="top">
			<?php echo JText::_('COM_VIRTUEMART_RATING_TITLE'); ?>
		</td>
		<td valign="top">
			<input type="text" value="<?php echo $this->rating->rating ?>" size="4" class="inputbox" name="rating" maxlength="4"/>
		</td>
	</tr>
	<tr>
		<!-- Show number of typed in characters -->
		<td width="24%" align="left" valign="top"><?php echo JText::_('COM_VIRTUEMART_TOTAL_VOTES') ?></td>
		<td width="76%" align="left">
			<div align="left">
				<input type="text" value="<?php echo $this->rating->ratingcount ?>" size="4" class="inputbox readonly" name="ratingcount" maxlength="4" readonly="readonly" />
			</div>
		</td>
	</tr>
	<tr>
		<?php echo VmHTML::row('booleanlist','COM_VIRTUEMART_PUBLISHED','published',$this->rating->published); ?>
	</tr>
</table>
</fieldset>
</div>
<!-- Hidden Fields -->
	<?php echo $this->addStandardHiddenToForm(); ?>
<input type="hidden" name="virtuemart_rating_id" value="<?php echo $this->rating->virtuemart_rating_id; ?>" />
<input type="hidden" name="virtuemart_product_id" value="<?php echo $this->rating->virtuemart_product_id; ?>" />
<input type="hidden" name="created_by" value="<?php echo $this->rating->created_by; ?>" />

</form>

<?php
AdminUIHelper::imitateTabs('end');
AdminUIHelper::endAdminArea(); ?>
<script type="text/javascript">
Joomla.submitbutton = function(pressbutton) {
	 if (pressbutton == 'cancel') {
		Joomla.submitform( pressbutton );
		return;
	}
	else {
		if (document.adminForm.rating.value > <?php echo $this->max_rating ; ?>) {
			alert('<?php echo addslashes( JText::_('COM_VIRTUEMART_MIN_RATING_JS').' : '.$this->max_rating ); ?>');
			return false;
		}
		else if (document.adminForm.rating.value < 0 ) {
			alert('<?php echo addslashes( JText::_('COM_VIRTUEMART_MAX_RATING_JS').' : 0' ); ?>');
			return false ;
		}
		else Joomla.submitform( pressbutton );
		return;
	}
}
</script>