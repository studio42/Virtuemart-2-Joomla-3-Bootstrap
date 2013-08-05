<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Edit
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: edit.php 6053 2012-06-05 12:36:21Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
JHtml::_('formbehavior.chosen', 'select');
vmJsApi::JvalideForm();
AdminUIHelper::startAdminArea();
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

<?php // Loading Templates in Tabs
$tabarray = array();
$tabarray['edit'] = 'COM_VIRTUEMART_PRODUCT_CUSTOM_FIELD';
if ($this->custom->form) $tabarray['options'] = 'JGLOBAL_FIELDSET_OPTIONS';

AdminUIHelper::buildTabs ( $this, $tabarray,$this->custom->virtuemart_custom_id );
// Loading Templates in Tabs END ?>


</form>
    <?php AdminUIHelper::endAdminArea(); ?>
<script type="text/javascript">
function submitbutton(pressbutton) {
	if (pressbutton=='cancel') submitform(pressbutton);
	if (jQuery('#adminForm').validationEngine('validate')== true) submitform(pressbutton);
	else return false ;
}

</script>
