<?php
/**
*
* Modify user form view
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
* @version $Id: edit.php 6285 2012-07-16 16:11:17Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

AdminUIHelper::startAdminArea();

// Implement Joomla's form validation
JHTML::_('behavior.formvalidation')
?>
<style type="text/css">
.invalid {
	border-color: #f00;
	background-color: #ffd;
	color: #000;
}
label.invalid {
	background-color: #fff;
	color: #f00;
}
</style>


<form method="post" id="adminForm" name="adminForm" action="index.php" enctype="multipart/form-data" class="form-validate" onSubmit="return myValidator(this);">
<?php

$tabarray = array();
if($this->userDetails->user_is_vendor){
	$tabarray['vendor'] = 'COM_VIRTUEMART_VENDOR';
}
$tabarray['shopper'] = 'COM_VIRTUEMART_SHOPPER_FORM_LBL';
//$tabarray['user'] = 'COM_VIRTUEMART_USER_FORM_TAB_GENERALINFO';
if ($this->shipToId != 0 || $this->new) {
	$tabarray['shipto'] = 'COM_VIRTUEMART_USER_FORM_SHIPTO_LBL';
}
if (($_ordcnt = count($this->orderlist)) > 0) {
	$tabarray['orderlist'] = 'COM_VIRTUEMART_ORDER_LIST_LBL';
}


AdminUIHelper::buildTabs ( $this, $tabarray,'vm-user');

?>

<?php echo $this->addStandardHiddenToForm(); ?>
</form>
<script language="javascript">
function myValidator(f) {
	if (f.task.value=='cancel') {
		return true;
	}
	if (document.formvalidator.isValid(f)) {
		f.submit();
		return true;
	} else {
		var msg = '<div><dl id="system-message" style="display: block;"><dt class="message">Message</dt><dd class="message message"><ul><li>';
		 msg += "<?php echo JText::sprintf("COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED_OTHER_TAB",JText::_("COM_VIRTUEMART_SHOPPER_FORM_LBL") ) ?>";
		 msg += '</li></ul></dd></dl><div>';
		jQuery('#element-box').before(msg);
	}
	//Funny, works for chrome etc, but throws error on FF, but the error stops the script, so the effect is the same
    jQuery().event.preventDefault();
}
</script>
<?php AdminUIHelper::endAdminArea(); ?>
