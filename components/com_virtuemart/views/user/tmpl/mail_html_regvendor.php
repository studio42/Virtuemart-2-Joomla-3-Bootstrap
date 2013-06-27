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
$li = '<br />';
?>


<html>
    <head>
	<style type="text/css">
            body, td, span, p, th { font-size: 11px; }
	    table.html-email {margin:10px auto;background:#fff;border:solid #dad8d8 1px;}
	    .html-email tr{border-bottom : 1px solid #eee;}
	    span.grey {color:#666;}
	    span.date {color:#666;font-size: 10px;}
	    a.default:link, a.default:hover, a.default:visited {color:#666;line-height:25px;background: #f2f2f2;margin: 10px ;padding: 3px 8px 1px 8px;border: solid #CAC9C9 1px;border-radius: 4px;-webkit-border-radius: 4px;-moz-border-radius: 4px;text-shadow: 1px 1px 1px #f2f2f2;font-size: 12px;background-position: 0px 0px;display: inline-block;text-decoration: none;}
	    a.default:hover {color:#888;background: #f8f8f8;}
	    .cart-summary{ }
	    .html-email th { background: #ccc;margin: 0px;padding: 10px;}
	    .sectiontableentry2, .html-email th, .cart-summary th{ background: #ccc;margin: 0px;padding: 10px;}
	    .sectiontableentry1, .html-email td, .cart-summary td {background: #fff;margin: 0px;padding: 10px;}
	</style>

    </head>

    <body style="background: #F2F2F2;word-wrap: break-word;">
	<div style="background-color: #e6e6e6;" width="100%">
	    <table style="margin: auto;" cellpadding="0" cellspacing="0"  >
		<tr>
		    <td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="html-email">
			    <tr>
				<td>

				    <?php echo JText::sprintf('COM_VIRTUEMART_WELCOME_VENDOR', $this->vendor->vendor_store_name); ?>
				    <br />
				</td>
			    </tr>
			</table>

			<table class="html-email" cellspacing="0" cellpadding="0" border="0" width="100%">  <tr>
				<th width="100%">
				    <?php echo JText::_('COM_VIRTUEMART_VENDOR_REGISTRATION_DATA') ?>
				</th>
			    </tr>
			    <tr>
				<td valign="top" width="100%">
				    <?php
				    echo JText::_('COM_VIRTUEMART_LOGINAME') . ' : ' . $this->user->username . $li;
				    echo JText::_('COM_VIRTUEMART_DISPLAYED_NAME') . ' : ' . $this->user->name . $li . $li;

				    echo JText::_('COM_VIRTUEMART_ENTERED_ADDRESS') . $li;

				    foreach ($this->userFields['fields'] as $userField) {
					if (!empty($userField['value']) && $userField['type'] != 'delimiter' && $userField['type'] != 'BT') {
					    echo $userField['title'] . ': ' . $this->escape($userField['value']) . $li;
					}
				    }
				    ?>
				</td>
			    </tr>
			</table>
		    </td>
		</tr>
	    </table>
	</div>
    </body>
</html>

