<?php
defined('_JEXEC') or die(); 
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author Max Milbers, Roland?
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default.php 2978 2011-04-06 14:21:19Z alatak $
*/
jimport('joomla.filesystem.file');

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php AdminUIHelper::startAdminArea(); ?>

	<div id="filter-bar" class="btn-toolbar">
		<?php echo $this->displayDefaultViewSearch('COM_VIRTUEMART_TITLE','keyword') ;
		echo $this->customs->customsSelect ; ?>
	</div>
	<div class="clearfix"> </div>
	<div id="results">
		<?php 
		// split to use ajax search
		echo $this->loadTemplate('results'); ?>
	</div>
	<?php //var_dump($this->lang); ?>
	<!-- new custom  preselect in modal -->
	<div id="customsModal" class="modal hide" tabindex="-1" aria-hidden="true">
		<div class="module-title nav-header"><?php echo JText::_('COM_VIRTUEMART_CUSTOM').' ('.JText::_('COM_VIRTUEMART_ADD').')'; ?><button type="button" class="close" aria-hidden="true">&times;</button></div>
		<div class="modal-body">
		<div class="row-striped">
			<?php 
			// var_dump($this->installedcustoms); 
			// standard customfields
			unset($this->customfieldTypes['E']);
			foreach ($this->customfieldTypes as $key => $custom) {
				
				$link = JROUTE::_('index.php?option=com_virtuemart&view=custom&task=add&field_type=' . $key);
				?>
				<div class="row-fluid"><a href="<?php echo $link ?>"> <?php echo jText::_($custom)  ?></a></div>
				<?php
			}
			// plugins
			foreach ($this->installedCustoms as $key => $custom) {
				if ($custom['enabled'] == 1 ) {
					$link = JROUTE::_('index.php?option=com_virtuemart&view=custom&task=add&field_type=E&custom_jplugin_id=' . $key);
					$langKey = 'vmcustom_'.$custom['element'];
					if ( $this->lang->hasKey($langKey) ) $name= $langKey;
					else $name= $custom['name'];
					if ( $this->lang->hasKey($langKey.'_desc') ) $title = 'class="hasTooltip" title="'.jText::_($langKey.'_desc').'" ';
					else $title = "";
					?>
					<div class="row-fluid"><a <?php echo $title ?> href="<?php echo $link ?>"> <?php echo jText::_($name) ?></a></div>
					<?php
				}
				else
				{
					?>
					<div class="row-fluid"><?php echo jText::_($custom['name']) // disabled plugin ?></div>
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
				jQuery('#customsModal').removeClass('hide');
				return false;
			} else {
				Joomla.submitform( pressbutton );
				return;
			}
		}
				// Attach the modal to document
		jQuery(function($){
			jQuery('#customsModal .close').click( function() {
				jQuery('#customsModal').addClass('hide');
			});
		});
	</script>
	<?php AdminUIHelper::endAdminArea(true); ?>
</form>