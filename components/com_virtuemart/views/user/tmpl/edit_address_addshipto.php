<?php
/**
 *
 * Enter address data for the cart, when anonymous users checkout
 *
 * @package	VirtueMart
 * @subpackage User
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: edit_address_addshipto.php 5912 2012-04-16 14:39:11Z alatak $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>


<fieldset>
    <legend>
	<?php echo '<span class="userfields_info">' .JText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL').'</span>'; ?>
    </legend>
    <?php echo $this->lists['shipTo']; ?>

</fieldset>

