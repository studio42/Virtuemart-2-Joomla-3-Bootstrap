<?php
/**
 *
 * Layout for the shopper mail, when he confirmed an ordner
 *
 * The addresses are reachable with $this->BTaddress, take a look for an exampel at shopper_adresses.php
 *
 * With $this->orderDetails['shipmentName'] or paymentName, you get the name of the used paymentmethod/shippmentmethod
 *
 * In the array order you have details and items ($this->orderDetails['details']), the items gather the products, but that is done directly from the cart data
 *
 * $this->orderDetails['details'] contains the raw address data (use the formatted ones, like BTaddress). Interesting informatin here is,
 * order_number ($this->orderDetails['details']['BT']->order_number), order_pass, coupon_code, order_status, order_status_name,
 * user_currency_rate, created_on, customer_note, ip_address
 *
 * @package	VirtueMart
 * @subpackage Cart
 * @author Max Milbers, Valerie Isaksen
 *
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
defined('_JEXEC') or die('Restricted access');
?>

<html>
    <head>
	<style type="text/css">
            body, td, span, p, th {  }
	    table.html-email {margin:10px auto;background:#fff;border:solid #dad8d8 1px;}
	    .html-email tr{border-bottom : 1px solid #eee;}
	    span.grey {color:#666;}
	    span.date {color:#666; }
	    a.default:link, a.default:hover, a.default:visited {color:#666;line-height:25px;background: #f2f2f2;margin: 10px ;padding: 3px 8px 1px 8px;border: solid #CAC9C9 1px;border-radius: 4px;-webkit-border-radius: 4px;-moz-border-radius: 4px;text-shadow: 1px 1px 1px #f2f2f2;font-size: 12px;background-position: 0px 0px;display: inline-block;text-decoration: none;}
	    a.default:hover {color:#888;background: #f8f8f8;}
	    .cart-summary{ }
	    .html-email th { background: #ccc;margin: 0px;padding: 10px;}
	    .sectiontableentry2, .html-email th, .cart-summary th{ background: #ccc;margin: 0px;padding: 10px;}
	    .sectiontableentry1, .html-email td, .cart-summary td {background: #fff;margin: 0px;padding: 10px;}
	    .line-through{text-decoration:line-through}
	    <?php if ($this->vendor->vendor_letter_header==1 || $this->vendor->vendor_letter_footer==1) { echo $this->vendor->vendor_letter_css; } ?> 
	    /* Firefox has a hard-coded font-size style for tables, so it won't by default inherit the surrounding div's font-size! */
	    #vmdoc-footer table, #vmdoc-header table, .vmdoc-footer table, .vmdoc-header table { font-size: inherit; }
	    #vmdoc-header h1, #vmdoc-footer h1, #vmdoc-header p, #vmdoc-footer p { margin-top: 0; margin-bottom: 0; }
	    .vmdoc-header-image { padding: 0; vertical-align: top; }
	    .vmdoc-header-vendor { width: 100%; }
	    td.vmdoc-header-separator, td.vmdoc-header-separator hr { padding: 0; margin-top: 0; margin-bottom: 0; }
	    td.vmdoc-header-separator { padding: 0; }
	</style>

    </head>

    <body style="background: #F2F2F2;word-wrap: break-word;">
	<div style="background-color: #e6e6e6;" width="100%">
	    <table style="margin: auto;" cellpadding="0" cellspacing="0"  ><tr><td>
			<?php
// Shop desc for shopper and vendor
			if ($this->recipient == 'shopper') {
			    echo $this->loadTemplate('header');
			}
// Message for shopper or vendor
			echo $this->loadTemplate($this->recipient);
// render shipto billto adresses
			echo $this->loadTemplate('shopperaddresses');
// render price list
			echo $this->loadTemplate('pricelist');
// more infos
			echo $this->loadTemplate($this->recipient . '_more');
// end of mail
			echo $this->loadTemplate('footer');
			?>
		    </td></tr>
	    </table>
	</div>
    </body>
</html>