<?php
/**
 *
 * Handle the waitinglist, and the send an email to shoppers who bought this product
 *
 * @package    VirtueMart
 * @subpackage Product
 * @author Seyi, Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2012 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: product_edit_customer.php 6272 2012-07-12 18:01:11Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');
$i = 0;
?>
<table class="adminform">
	<tbody>
	<tr class="row<?php echo $i?>">
		<td width="21%" valign="top">
			<?php
			$mail_options = array(
				'customer'=> JText::_ ('COM_VIRTUEMART_PRODUCT_SHOPPERS'),
				'notify'  => JText::_ ('COM_VIRTUEMART_PRODUCT_WAITING_LIST_USERLIST'),
			);
			$mail_default = 'notify';
			if (VmConfig::get ('stockhandle', 0) != 'disableadd' or empty($this->waitinglist)) {
				echo '<input type="hidden" name="customer_email_type" value="customer" id="customer_email_type0">';
			}
			else {
				echo VmHtml::radioList ('customer_email_type', $mail_default, $mail_options);
			}
			?>

			<div id="notify_particulars" style="padding-left:20px;">
				<div><input type="checkbox" name="notification_template" id="notification_template" value="1" CHECKED>
					<label for="notification_template">
						<span class="hasTip" title="<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_USE_NOTIFY_TEMPLATE_TIP'); ?>">
						<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_USE_NOTIFY_TEMPLATE'); ?></span>
				</div>
				</label>
				<div><input type="text" name="notify_number" value="" size="4"/><?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_NOTIFY_NUMBER'); ?></div>
			</div>
			<br/>

			<div class="mailing">
				<div class="button2-left" data-type="sendmail">
					<div class="blank" style="padding:0 6px;cursor: pointer;" title="<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_EMAIL_SEND_TIP'); ?>">
						<span class="vmicon vmicon-16-email"></span>
						<?php echo Jtext::_ ('COM_VIRTUEMART_PRODUCT_EMAIL_SEND'); ?>
					</div>

				</div>
				<div id="customers-list-msg"></div>
				<br/>


		</td>
	</tr>
	<?php $i = 1 - $i; ?>
	<tr class="row<?php echo $i?>">
		<td width="21%" valign="top">
			<div id="customer-mail-content">
				<div><?php echo Jtext::_ ('COM_VIRTUEMART_PRODUCT_EMAIL_SUBJECT') ?></div>
				<input type="text" class="mail-subject" id="mail-subject" size="100"   value="<?php echo JText::sprintf ('COM_VIRTUEMART_PRODUCT_EMAIL_SHOPPERS_SUBJECT',$this->product->product_name) ?>">

				<div><?php echo Jtext::_ ('COM_VIRTUEMART_PRODUCT_EMAIL_CONTENT') ?></div>
				<textarea style="width: 100%;" class="inputbox"   id="mail-body" cols="35" rows="10"></textarea>
				<br/>
			</div>
		</td>
	</tr>
	<?php $i = 1 - $i; ?>
	<tr class="row<?php echo $i?>">
		<td width="21%" valign="top">
			<div id="customer-mail-list">
				<span class="hasTip" title="<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_EMAIL_ORDER_ITEM_STATUS_TIP'); ?>">
				<strong><?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_EMAIL_ORDER_ITEM_STATUS') ?></strong>
				</span><br/>
				<?php echo $this->lists['OrderStatus'];?>
				<br/> <br/>
				<div style="font-weight:bold;"><?php echo JText::sprintf ('COM_VIRTUEMART_PRODUCT_SHOPPERS_LIST', htmlspecialchars ($this->product->product_name)); ?></div>
				<table class="adminlist" cellspacing="0" cellpadding="0">
					<thead>
					<tr>
						<th class="title"><?php echo JText::_ ('COM_VIRTUEMART_NAME');?></th>
						<th class="title"><?php echo JText::_ ('COM_VIRTUEMART_EMAIL');?></th>
						<th class="title"><?php echo JText::_ ('COM_VIRTUEMART_SHOPPER_FORM_PHONE');?></th>
						<th class="title"><?php echo JText::_ ('COM_VIRTUEMART_ORDER_PRINT_QUANTITY');?></th>
						<th class="title"><?php echo JText::_ ('COM_VIRTUEMART_ORDER_PRINT_ITEM_STATUS');?></th>
						<th class="title"><?php echo JText::_ ('COM_VIRTUEMART_ORDER_NUMBER');?></th>
					</tr>
					</thead>
					<tbody id="customers-list">
					<?php
					if(!class_exists('ShopFunctions'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'shopfunctions.php');
					echo ShopFunctions::renderProductShopperList($this->productShoppers);
					?>
					</tbody>
				</table>
			</div>

			<div id="customer-mail-notify-list">

				<?php if (VmConfig::get ('stockhandle', 0) == 'disableadd' && !empty($this->waitinglist)) { ?>
				<div style="font-weight:bold;"><?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_WAITING_LIST_USERLIST'); ?></div>
				<table class="adminlist" cellspacing="0" cellpadding="0">
					<thead>
					<tr>
						<th class="title"><?php echo JText::_ ('COM_VIRTUEMART_NAME');?></th>
						<th class="title"><?php echo JText::_ ('COM_VIRTUEMART_USERNAME');?></th>
						<th class="title"><?php echo JText::_ ('COM_VIRTUEMART_EMAIL');?></th>
					</tr>
					</thead>
					<tbody id="customers-notify-list">
						<?php
						if (isset($this->waitinglist) && count ($this->waitinglist) > 0) {
							$i=0;
							foreach ($this->waitinglist as $key => $wait) {
								if ($wait->virtuemart_user_id == 0) {
									$row = '<tr class="row'.$i.'"><td></td><td></td><td><a href="mailto:' . $wait->notify_email . '">' .
									$wait->notify_email . '</a></td></tr>';
								}
								else {
									$row = '<tr class="row'.$i.'"><td>' . $wait->name . '</td><td>' . $wait->username . '</td><td>' . '<a href="mailto:' . $wait->notify_email . '">' . $wait->notify_email . '</a>' . '</td></tr>';
								}
								echo $row;
								$i = 1 - $i;
							}
						}
						else {
							?>
						<tr>
							<td colspan="4">
								<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_WAITING_NOWAITINGUSERS'); ?>
							</td>
						</tr>
							<?php
						} ?>
					</tbody>
				</table>

				<?php } ?>
			</div>

			</div>
		</td>
	</tr>
	<tr>
		<td>
			<?php

			$aflink = '<a target="_blank" href="http://www.acyba.com/acymailing.html?partner_id=19513"><img title="AcyMailing 2" height=60 src="http://www.acyba.com/images/banners/acymailing_450-109.png"/></a>';
			echo JText::sprintf('COM_VIRTUEMART_AD_ACY',$aflink);
			?>
		</td>
	</tr>
	</tbody>
</table>
<script type="text/javascript">
	<!--
	var $customerMailLink = '<?php echo JURI::root () . '/index.php?option=com_virtuemart&view=productdetails&task=sentproductemailtoshoppers&virtuemart_product_id=' . $this->product->virtuemart_product_id ?>';
	var $customerMailNotifyLink = '<?php echo 'index.php?option=com_virtuemart&view=product&task=ajax_notifyUsers&virtuemart_product_id=' . $this->product->virtuemart_product_id ?>';
	var $customerListLink = '<?php echo 'index.php?option=com_virtuemart&view=product&format=json&type=userlist&virtuemart_product_id=' . $this->product->virtuemart_product_id ?>';
	var $customerListNotifyLink = '<?php echo 'index.php?option=com_virtuemart&view=product&task=ajax_waitinglist&virtuemart_product_id=' . $this->product->virtuemart_product_id ?>';
	var $customerListtype = 'reserved';

	jQuery(document).ready(function () {

		populate_customer_list(jQuery('select#order_items_status').val());
		customer_initiliaze_boxes();
		jQuery("input:radio[name=customer_email_type],input:checkbox[name=notification_template]").click(function () {
			customer_initiliaze_boxes();
		});
		jQuery('select#order_items_status').chosen({enable_select_all:true, select_some_options_text:vm2string.select_some_options_text}).change(function () {
			populate_customer_list(jQuery(this).val());
		})
		jQuery('.mailing .button2-left').click(function () {

			email_type = jQuery("input:radio[name=customer_email_type]:checked").val();
			if (email_type == 'notify') {

				var $body = '';
				var $subject = '';
				if (jQuery('input:checkbox[name=notification_template]').is(':checked')); else {
					 $subject = jQuery('#mail-subject').val();
					 $body = jQuery('#mail-body').val();
				}
				var $max_number = jQuery('input[name=notify_number]').val();

				jQuery.post($customerMailNotifyLink, { subject:$subject, mailbody:$body, max_number:$max_number, token:'<?php echo JUtility::getToken () ?>' },
					function (data) {
						alert('<?php echo addslashes (JTExt::_ ('COM_VIRTUEMART_PRODUCT_NOTIFY_MESSAGE_SENT')); ?>');
						jQuery.getJSON($customerListNotifyLink, {tmpl:'component', no_html:1},
							function (data) {
								//			jQuery("#customers-list").html(data.value);
								$html = '';
								jQuery.each(data, function (key, val) {
									if (val.virtuemart_user_id == 0) {
										$html += '<tr><td></td><td></td><td><a href="mailto:' + val.notify_email + '">' + val.notify_email + '</a></td></tr>';
									}
									else {
										$html += '<tr><td>' + val.name + '</td><td>' + val.username + '</td><td><a href="mailto:' + val.notify_email + '">' + val.notify_email + '</a></td></tr>';
									}
								});
								jQuery("#customers-notify-list").html($html);
							}
						);
					}
				);

			}
			else if (email_type = 'customer') {
				var $subject = jQuery('#mail-subject').val();
				var $body = jQuery('#mail-body').val();
				if ($subject == '') {
					alert("<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_EMAIL_ENTER_SUBJECT')?>");
				}
				else if ($body == '') {
					alert("<?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_EMAIL_ENTER_BODY')?>");
				}
				else {
					var $statut = jQuery('select#order_items_status').val();
					jQuery.post($customerMailLink, { subject:$subject, mailbody:$body, statut:$statut, token:'<?php echo JUtility::getToken () ?>' },
						function (data) {
							alert('<?php echo addslashes (JTExt::_ ('COM_VIRTUEMART_PRODUCT_NOTIFY_MESSAGE_SENT')); ?>');
							//jQuery("#customers-list-msg").html('<strong><?php echo JText::_ ('COM_VIRTUEMART_PRODUCT_NOTIFY_MESSAGE_SENT')?></strong>');
							//jQuery("#mail-subject").html('');
							jQuery("#mail-body").html('');
						}
					);
				}

			}

		});

	});

	/* JS for list changes */


	function populate_customer_list($status) {
		if ($status == "undefined" || $status == null) $status = '';
		jQuery.getJSON($customerListLink, { status:$status  },
			function (data) {
				jQuery("#customers-list").html(data.value);
			});
	}
	function customer_initiliaze_boxes() {
		email_type = jQuery("input:radio[name=customer_email_type]:checked").val();
		if (email_type == 'notify') {
			jQuery('#notify_particulars').show();
			jQuery('#customer-mail-list').hide();
			jQuery('#customer-mail-notify-list').show();
			jQuery("input:radio[name=customer_email_type]").val()
			if (jQuery('input:checkbox[name=notification_template]').is(':checked')) jQuery('#customer-mail-content').hide();
			else  jQuery('#customer-mail-content').show();

		}
		else if (email_type = 'customer') {
			jQuery('#notify_particulars').hide();
			jQuery('#customer-mail-content').show();
			jQuery('#customer-mail-list').show();
			jQuery('#customer-mail-notify-list').hide();
		}
	}
-->
</script>