<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Paymentmethod
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 6475 2012-09-21 11:54:21Z Milbo $
*/

// Added modal for adding NEW payment. THe customer have to preselect a payment now to add a new one.
// this permit to load directly the config XML and not to save 2 time the paymentmethod form
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
//if($virtuemart_vendor_id==1 || $perm->check( 'admin' )){
$multiX = Vmconfig::get('multix','none')!=='none' ? true : false ;
$cols = $multiX ? 10 : 9 ;
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php AdminUIHelper::startAdminArea(); ?>

	<div class="clearfix"> </div>
	<div id="results">
		<?php 
		// split to use ajax search
		echo $this->loadTemplate('results'); ?>
	</div>

	<!-- new Payment method preselect in modal -->
	<div id="PaymentsModal" class="modal hide" tabindex="-1" aria-hidden="true">
		<div class="module-title nav-header"><?php echo JText::_('COM_VIRTUEMART_PAYMENTMETHOD_S').' ('.JText::_('COM_VIRTUEMART_ADD').')'; ?><button type="button" class="close" aria-hidden="true">&times;</button></div>
		<div class="modal-body">
		<div class="row-striped">
		<?php // payment_jplugin_id
		// var_dump($this->installedPayments); 
		foreach ($this->installedPayments as $payment) {
			if ($payment->enabled == 1 ) {
				$link = JROUTE::_('index.php?option=com_virtuemart&view=paymentmethod&task=add&payment_jplugin_id=' . $payment->extension_id);
				?>
				<div class="row-fluid"><a href="<?php echo $link ?>"> <?php echo $payment->name ?></a></div>
			<?php
			}
			else
			{ ?>
					<div><?php echo $payment->name ?></div>
				<?php
			}
		} ?>
		</div>
		</div>
		<div class="close btn"><?php echo JText::_('JCANCEL') ?></div></div>
	</div>
	<script type="text/javascript">
		Joomla.submitbutton = function(pressbutton) {
			if (pressbutton == 'add') {
				jQuery('#PaymentsModal').removeClass('hide');
				e.preventDefault();
				return false;
			} else {
				Joomla.submitform( pressbutton );
				return;
			}
		}
				// Attach the modal to document
		jQuery(function($){
			jQuery('#PaymentsModal .close').click( function() {
				jQuery('#PaymentsModal').addClass('hide');
			});
		});
	</script>
	<?php AdminUIHelper::endAdminArea(true); ?>

</form>