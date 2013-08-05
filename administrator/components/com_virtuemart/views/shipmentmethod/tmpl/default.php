<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage Shipment
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 5628 2012-03-08 09:00:21Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php AdminUIHelper::startAdminArea(); ?>

	<div id="results">
		<?php 
		// split to use ajax search
		echo $this->loadTemplate('results'); ?>
	</div>

	<div id="shipmentsModal" class="modal hide" tabindex="-1" aria-hidden="true">
		<div class="module-title nav-header"><?php echo JText::_('COM_VIRTUEMART_SHIPMENTMETHOD_S').' ('.JText::_('COM_VIRTUEMART_ADD').')'; ?><button type="button" class="close" aria-hidden="true">&times;</button></div>
		<div class="modal-body">
		<div class="row-striped">
		<?php // shipment_jplugin_id
		// var_dump($this->installedshipments); 
		foreach ($this->installedShipments as $shipment) {
			if ($shipment->enabled == 1 ) {
				$link = JROUTE::_('index.php?option=com_virtuemart&view=shipmentmethod&task=add&shipment_jplugin_id=' . $shipment->extension_id);
				?>
				<div class="row-fluid"><a href="<?php echo $link ?>"> <?php echo $shipment->name ?></a></div>
			<?php
			}
			else
			{ ?>
					<div><?php echo $shipment->name ?></div>
				<?php
			}
		} ?>
		</div>
		</div>
		<div class="close btn"><?php echo JText::_('JCANCEL') ?></div>
	</div>
	<script type="text/javascript">
		Joomla.submitbutton = function(pressbutton) {
			if (pressbutton == 'add') {
				jQuery('#shipmentsModal').removeClass('hide');
				e.preventDefault();
				return false;
			} else {
				Joomla.submitform( pressbutton );
				return;
			}
		}
				// Attach the modal to document
		jQuery(function($){
			$('#shipmentsModal .close').click( function() {
				$('#shipmentsModal').addClass('hide');
			});
		});
	</script>

	<?php AdminUIHelper::endAdminArea(true); ?>
</form>