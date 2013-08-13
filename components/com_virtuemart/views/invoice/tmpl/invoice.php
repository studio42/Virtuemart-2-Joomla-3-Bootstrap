<?php
/**
 *
 * Order detail view
 *
 * @package    VirtueMart
 * @subpackage Orders
 * @author Max Milbers, Valerie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: details.php 5412 2012-02-09 19:27:55Z alatak $
 */
//index.php?option=com_virtuemart&view=invoice&layout=invoice&format=pdf&tmpl=component&order_number=xx&order_pass=p_yy
//
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('vmpanels.css', JURI::root() . 'components/com_virtuemart/assets/css/');
if ($this->_layout == "invoice") {
    $document = JFactory::getDocument();
    $document->setTitle(JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_NUMBER') . ' ' . $this->orderDetails['details']['BT']->order_number . ' ' . $this->vendor->vendor_store_name);
//$document->setName( JText::_('COM_VIRTUEMART_ACC_ORDER_INFO').' '.$this->orderDetails['details']['BT']->order_number);
//$document->setDescription( JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_NUMBER').' '.$this->orderDetails['details']['BT']->order_number);
}

if ($this->headFooter) {
    ?>
<style><?php echo $this->vendor->vendor_letter_css; ?></style>
<div class="vendor-details-view">
<?php echo ($this->format=="html")?$this->replaceVendorFields($this->vendor->vendor_letter_header_html, $this->vendor):$this->vendor->vendor_letter_header_html; ?>
</div>

<div class="vendor-description">
<?php //echo $this->vendor->vendor_store_desc.'<br>';


    /*	foreach($this->vendorAddress as $userfields){

         foreach($userfields['fields'] as $item){
             if(!empty($item['value'])){
                 if($item['name']==='agreed'){
                     $item['value'] =  ($item['value']===0) ? JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_TOS_NO'):JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_TOS_YES');
                 }
             ?><!-- span class="titles"><?php echo $item['title'] ?></span -->
                         <span class="values vm2<?php echo '-'.$item['name'] ?>" ><?php echo $this->escape($item['value']) ?></span>
                     <?php if ($item['name'] != 'title' and $item['name'] != 'first_name' and $item['name'] != 'middle_name' and $item['name'] != 'zip') { ?>
                         <br class="clear" />
                     <?php
                 }
             }
         }
     }*/
?></div> <?php
}


if ($this->print) {
    ?>

<body onload="javascript:print();">

<div class='spaceStyle'>
    <?php
// 			echo require(__DIR__.'/mail_html_shopper.php');
    ?>
</div>
<div class='spaceStyle'>
    <?php
    echo $this->loadTemplate('order');
    ?>
</div>

<div class='spaceStyle'>
    <?php
    echo $this->loadTemplate('items');
    ?>
</div>
    <?php    
if ($this->headFooter) {
    echo ($this->format=="html")?$this->replaceVendorFields($this->vendor->vendor_letter_footer_html, $this->vendor):$this->vendor->vendor_letter_footer_html;
}

if ($this->vendor->vendor_letter_add_tos) {?>
<div class="invoice_tos" <?php if ($this->vendor->vendor_letter_add_tos_newpage) { ?> style="page-break-before: always"<?php } ?>>
    <?php echo $this->vendor->vendor_terms_of_service; ?>
</div>
<?php }
?>
</body>
<?php
} else {

// NOT in print mode, full HTML view for a browser:

    ?>

<?php

    echo $this->loadTemplate('order');

    ?>


	<div class='spaceStyle'>
	<?php

    $tabarray = array();

    $tabarray['items'] = 'COM_VIRTUEMART_ORDER_ITEM';
    $tabarray['history'] = 'COM_VIRTUEMART_ORDER_HISTORY';

    shopFunctionsF::buildTabs( $this, $tabarray);
    echo '</div>
	    <br clear="all"/><br/>';
}


?>






