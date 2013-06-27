<?php
defined('_JEXEC') or die('');
?>

<html>
    <head>
	<style type="text/css">
            body, td, span, p, th {   }
	    table.html-email {margin:10px auto;background:#fff;border:solid #dad8d8 1px;}
	    .html-email tr{border-bottom : 1px solid #eee;}
	    span.grey {color:#666;}
	    span.date {color:#666; }
	    a.default:link, a.default:hover, a.default:visited {color:#666;line-height:25px;background: #f2f2f2;margin: 10px ;padding: 3px 8px 1px 8px;border: solid #CAC9C9 1px;border-radius: 4px;-webkit-border-radius: 4px;-moz-border-radius: 4px;text-shadow: 1px 1px 1px #f2f2f2; background-position: 0px 0px;display: inline-block;text-decoration: none;}
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
				<td >
				    <?php echo JText::sprintf('COM_VIRTUEMART_WELCOME_VENDOR', $this->vendor->vendor_store_name); ?>
				    <br />
				</td>
			    </tr>
			</table>

			<table class="html-email" cellspacing="0" cellpadding="0" border="0" width="100%">  <tr >
				<th width="100%">
				    <?php echo JText::_('COM_VIRTUEMART_VENDOR_CONTACT').' '.$this->vendor->vendor_name ?>
				</th>
			    </tr>
			    <tr>
				<td valign="top" width="100%">
				    <?php
				    echo JText::sprintf('COM_VIRTUEMART_QUESTION_MAIL_FROM', $this->user['name'], $this->user['email']) . "<br />";
				    echo nl2br($this->comment). "<br />";
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
</head>

