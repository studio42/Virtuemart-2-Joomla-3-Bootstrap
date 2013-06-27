<?php
/**
 *
 * Description
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: media_edit.php 3049 2011-04-17 07:01:44Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
vmJsApi::JvalideForm();
AdminUIHelper::startAdminArea();
?>
<form name="adminForm" id="adminForm" method="post" action="">
    <fieldset>
	<legend><?php echo JText::_('COM_VIRTUEMART_PRODUCT_CUSTOM_FIELD'); ?></legend>
	<?php
	$this->customfields->addHidden('view', 'custom');
	$this->customfields->addHidden('task', '');
	$this->customfields->addHidden(JUtility::getToken(), 1);
//if ($this->custom->custom_parent_id) $this->customfields->addHidden('custom_parent_id',$this->custom->custom_parent_id);
	$attribute_id = JRequest::getVar('attribute_id', '');
	if (!empty($attribute_id))
	    $this->customfields->addHidden('attribute_id', $attribute_id);
	?>
	<table class="admintable">
	    <?php echo $this->customfields->displayCustomFields($this->custom); ?>

	    <tr id="custom_plg">
		<td valign="top"><?php echo JText::_('COM_VIRTUEMART_SELECT_CUSTOM_PLUGIN') ?></td>
		<td>
		    <fieldset>
			<?php echo $this->pluginList ?>
			<div class="clear"></div>
    			<div id="plugin-Container">
			<?php
			if (!empty($this->customPlugin)) {
			    ?>


				<?php
				$parameters = new vmParameters($this->customPlugin, $this->customPlugin->custom_element, 'plugin', 'vmcustom');
				echo $rendered = $parameters->render();
				?>

    			</div>
    		    </fieldset>
    		</td>
    	    </tr>
	    <?php } ?>
	</table>
    </fieldset>
    <?php if (!empty($this->customPlugin->custom_jplugin_id)) { ?>
        <input type="hidden" name="id" value="<?php echo $this->customPlugin->virtuemart_custom_id ?>" >
    <?php } ?>
</form>
<script type="text/javascript">
function submitbutton(pressbutton) {
	if (pressbutton=='cancel') submitform(pressbutton);
	if (jQuery('#adminForm').validationEngine('validate')== true) submitform(pressbutton);
	else return false ;
}
jQuery(function($) {

<?php if ($this->custom->field_type !== "E") { ?>$('#custom_plg').hide();<?php } ?>
    $('#field_type').change(function () {
	var $selected = $(this).val();
	if ($selected == "E" ) $('#custom_plg').show();
	else { $('#custom_plg').hide();
	    $('#custom_jplugin_id option:eq(0)').attr("selected", "selected");
	    $('#custom_jplugin_id').change();
	}

    });
    $('#custom_jplugin_id').change(function () {
	var $id = $(this).val();
	$('#plugin-Container').load( 'index.php?option=com_virtuemart&view=custom&task=viewJson&format=json&custom_jplugin_id='+$id , function() { 
	$(this).find("[title]").vm2admin('tips',tip_image) });

    });
});
</script>
<?php AdminUIHelper::endAdminArea(); ?>