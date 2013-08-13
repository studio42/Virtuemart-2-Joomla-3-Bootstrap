<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author KOHL Patrick
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
* @version $Id: default.php 2810 2011-03-02 19:08:24Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );

if(VmConfig::get('usefancy',0)){
	$onclick = 'parent.jQuery.fancybox.close();';
} else {
	$onclick = 'parent.jQuery.facebox.close();';
}
?>
<div class="productdetails-view">
	<?php echo JText::_('COM_VIRTUEMART_ASK_QUESTION_THANK_YOU'); ?>
	<button onclick="<?php echo $onclick ?>" type="button"><?php echo JText::_('COM_VIRTUEMART_CLOSE'); ?></button>
</div>
