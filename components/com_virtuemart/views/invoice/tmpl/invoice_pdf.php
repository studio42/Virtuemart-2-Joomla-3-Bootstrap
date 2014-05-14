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

	$siteUrl = JURI::getInstance()->toString();
	$siteUrl = str_replace("format=pdf", "", $siteUrl);
	// ordered render by line, all Adresses
	$lines = array();
	$lines['company'] = array('company');
	$lines['name'] = array('title','first_name','middle_name','last_name');
	$lines['adress'] = array('address_1');
	$lines['adress2'] = array('address_2');
	$lines['city'] = array('zip','city');
	$lines['state'] = array('virtuemart_state_id');// note this is the state
	$lines['country'] = array('virtuemart_country_id');// note this is the country

	$vendorAdress = array('store_name'=>$this->vendor->vendor_store_name);
	$billAdress = array();
	$shipAdress = array();
	$billfields = $this->userfields['fields']; // shorcut
	$shipfields = $this->shipmentfields['fields']; // shorcut
	$vendorfields = $this->vendor->vendorFields['fields']; // shorcut
	// var_dump($vendorfields);jexit();
	foreach ($lines as $key => $line) {
		foreach ($line as $field) {
			if(isset($billfields[$field]) ) {
				if (!empty($billfields[$field]['value'])) {
					if (isset($billAdress[$key])) $billAdress[$key] .= ' '.$billfields[$field]['value'];
					else $billAdress[$key] = $billfields[$field]['value'];
				}
			}
			if(isset($shipfields[$field]) ) {
				if (!empty($shipfields[$field]['value'])) {
					if (isset($shipAdress[$key])) $shipAdress[$key] .= ' '.$shipfields[$field]['value'];
					else $shipAdress[$key] = $shipfields[$field]['value'];
				}
			}
			if(isset($vendorfields[$field]) ) {
				if (!empty($vendorfields[$field]['value'])) {
					if (isset($vendorAdress[$key])) $vendorAdress[$key] .= ' '.$vendorfields[$field]['value'];
					else $vendorAdress[$key] = $vendorfields[$field]['value'];
				}
			}
		}
	}
	unset($vendorAdress['name']);
// load default styles for pdf
$this->document->addStyleSheet('components/com_virtuemart/assets/css/pdf.css');
// set PDF header
$size = 0 ;
$img = '';

$htmlHeader = '<span class="left">'.$this->vendor->vendor_store_name.'</span>';
	$invoiceHeader = JText::_('COM_VIRTUEMART_INVOICE');//.' '.$this->invoiceNumber ;
$htmlHeader = '<span class="right"><h3>'.$invoiceHeader.'</h3></span>' ;
$this->document->Set('HTMLHeader',$htmlHeader);

if ($this->vendor->vendor_letter_css) { ?>
	<style><?php echo $this->vendor->vendor_letter_css; ?></style>
<?php }
if (file_exists( JPATH_ROOT.'/'.$this->vendor->images[0]->file_url)) {
	$img = JURI::root (true) .'/'. $this->vendor->images[0]->file_url ;
	$size = $this->document->params->get('logo_height',48);
	$img = '<img src="'.$img.'" style="height:'.$size.'px;width:'.$size.'px">';
	$counter = count($vendorAdress);
} 
?>

<table class="main-table" width="100%" cellspacing="0" cellpadding="0" border="0">
 <tr>
  <td width="60%" valign="top">
	<table class="pdf-vendor-head" width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<?php if ($img) { ?>
				<td valign="top" rowspan="<?php echo $counter ?>" style="width:<?php echo $size+10 ?>px"><?php echo $img ?></td>
			<?php
			} else $counter = 0 ;
			$counter --;
			foreach ($vendorAdress as $class => $line) {
				echo '<td class="'.$class.'" >'. $line . '</td>';
				if ($counter > 0) echo '</tr><tr>';
				$counter --;
			}
			?>
		</tr>
		<tr>
			<td>
				<?php echo $this->vendor->vendor_legal_info ?>
			</td>
		</tr>
		
	<?php // echo ($this->format=="html")?$this->replaceVendorFields($this->vendor->vendor_letter_header_html, $this->vendor):$this->vendor->vendor_letter_header_html; ?>
	</table>
  </td>
  <td valign="top" width="40%" >
	<table border="0" class="billto-adress">
		<tr><th class="billto-header"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_BILL_TO_LBL') ?></th></tr>
	    <?php
	    foreach ($billAdress as $class => $line) {
		    echo '<tr><td class="'.$class.'">'.
					$line . 
				'</td></tr>';
		} ?>
	</table>
  </td>
 </tr>
</table>

<table class="main-table" width="100%" cellspacing="0" cellpadding="0" border="0">
 <tr>
  <td valign="top" width="50%" >
	<table border="0" class="shipto-adress">
		<tr><th class="shipto-header"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIP_TO_LBL') ?></th></tr>
	<?php
		foreach ($shipAdress as $class => $line) {
			echo '<tr><td class="'.$class.'">'.
					$line . 
				'</td></tr>';
		}
	?></table>
  </td>
  <td width="50%">
	<table class="pdf-invoice-head" width="100%">
		<tr>
		<td class="key"><?php echo JText::_('COM_VIRTUEMART_INVOICE') ?></td>
		<td align="left"><a href="<?php echo $siteUrl ?>"><?php echo $this->invoiceNumber ; ?></a></td>
		</tr>
		<?php if ($this->invoiceNumber) { ?>
		<tr>
		<td class="key"><?php echo JText::_('COM_VIRTUEMART_INVOICE_DATE') ?></td>
		<td align="left"><?php echo vmJsApi::date($this->invoiceDate, 'LC4', true); ?></td>
		</tr>
			<?php } ?>
		<tr>
		<td class="key"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_NUMBER') ?></td>
		<td align="left"><strong>
			<?php echo $this->orderDetails['details']['BT']->order_number; ?>
			</strong>
		</td>
		</tr>

		<tr>
		<td class="key"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_DATE') ?></td>
		<td align="left"><?php echo vmJsApi::date($this->orderDetails['details']['BT']->created_on, 'LC4', true); ?></td>
		</tr>
		<tr>
		<td class="key"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PO_STATUS') ?></td>
		<td align="left"><?php echo $this->orderstatuses[$this->orderDetails['details']['BT']->order_status]; ?></td>
		</tr>
		<tr>
		<td class="key"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_SHIPMENT_LBL') ?></td>
		<td align="left"><?php echo $this->orderDetails['shipmentName']; ?></td>
		</tr>
		<tr>
		<td class="key"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_PAYMENT_LBL') ?></td>
		<td align="left"><?php echo $this->orderDetails['paymentName']; ?>
		</td>
		</tr>
	<?php if ($this->orderDetails['details']['BT']->customer_note) { ?>
		<tr>
			<td class="key"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_CUSTOMER_NOTE') ?></td>
			<td valign="top" align="left" width="50%"><?php echo $this->orderDetails['details']['BT']->customer_note; ?></td>
		</tr>
	<?php } ?>

		 <tr>
		<td class="bill_total"><?php echo JText::_('COM_VIRTUEMART_ORDER_PRINT_TOTAL') ?></td>
		<td class="bill_total" align="left"><?php echo $this->currency->priceDisplay($this->orderDetails['details']['BT']->order_total,$this->currency); ?></td>
		</tr>


		<tr>
		<td colspan="2"></td>
		</tr>
	</table>
  </td>
 </tr>
</table>

<?php

// ordered product list
echo $this->loadTemplate('items');

?>
<hr class="page-break">
<pagebreak />
<div class="tos">
<h3><?php echo JText::_('COM_VIRTUEMART_VENDOR_TOS') ?></h3>
<?php echo $this->vendor->vendor_terms_of_service; ?>
</div>
<?php // order states history
// echo $this->loadTemplate('history');
?>
