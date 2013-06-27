<?php
defined('_JEXEC') or die('');


/**
 * Renders the email for the vendor send in the registration process
 * @package	VirtueMart
 * @subpackage User
 * @author Max Milbers
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: view.html.php 2459 2010-07-02 17:30:23Z milbo $
 */
$li = "\n";
?>


<?php echo JText::sprintf('COM_VIRTUEMART_WELCOME_VENDOR', $this->vendor->vendor_store_name) . $li. $li ?>
<?php echo JText::_('COM_VIRTUEMART_VENDOR_REGISTRATION_DATA') . " " . $li; ?>
<?php echo JText::_('COM_VIRTUEMART_LOGINAME')   . $this->user->username . $li; ?>
<?php echo JText::_('COM_VIRTUEMART_DISPLAYED_NAME')   . $this->user->name . $li. $li; ?>
<?php echo JText::_('COM_VIRTUEMART_ENTERED_ADDRESS')   . $li ?>


<?php

foreach ($this->userFields['fields'] as $userField) {
    if (!empty($userField['value']) && $userField['type'] != 'delimiter' && $userField['type'] != 'BT') {
	echo $userField['title'] . ' ' . $userField['value'] . $li;
    }
}

echo $li;

echo JURI::root() . 'index.php?option=com_virtuemart&view=user' . $li;

echo $li;
//echo JURI::root() . 'index.php?option=com_virtuemart&view=user&virtuemart_user_id=' . $this->_models['user']->_id . ' ' . $li;
//echo JURI::root() . 'index.php?option=com_virtuemart&view=vendor&virtuemart_vendor_id=' . $this->vendor->virtuemart_vendor_id . ' ' . $li;
?>
