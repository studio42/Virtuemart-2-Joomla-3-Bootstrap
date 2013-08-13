<?php
/**
 *
 * Define here the Header for order mail success !
 *
 * @package    VirtueMart
 * @subpackage Cart
 * @author Kohl Patrick
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 *
 */
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');


?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="html-email">
	<?php if ($this->vendor->vendor_letter_header>0) { ?>
	<tr>
		<?php if ($this->vendor->vendor_letter_header_image>0) { ?>
			<td class="vmdoc-header-image"><img src="<?php  echo JURI::root () . $this->vendor->images[0]->file_url ?>" style="width: <?php echo $this->vendor->vendor_letter_header_imagesize; ?>mm;" /></td>
			<td colspan=1 class="vmdoc-header-vendor">
		<?php } else { // no image ?>
			<td colspan=2 width="100%" class="vmdoc-header-vendor">
		<?php } ?>
			<div id="vmdoc-header" class="vmdoc-header" style="font-size: <?php echo $this->vendor->vendor_letter_header_font_size; ?>pt;">
			<?php echo VirtuemartViewInvoice::replaceVendorFields ($this->vendor->vendor_letter_header_html, $this->vendor); ?>
			</div>
		</td>
	</tr>
	<?php if ($this->vendor->vendor_letter_header_line == 1) { ?>
	<tr><td colspan=2 width="100%" class="vmdoc-header-separator"><hr/></td></tr>
	<?php } // END if header_line ?>
			
	<?php } // END if header ?>
	<tr>
		<td colspan="2">
			<strong><?php echo JText::sprintf ('COM_VIRTUEMART_MAIL_SHOPPER_NAME', $this->orderDetails['details']['BT']->title . ' ' . $this->orderDetails['details']['BT']->first_name . ' ' . $this->orderDetails['details']['BT']->last_name); ?></strong><br/>
		</td>
	</tr>
</table>
