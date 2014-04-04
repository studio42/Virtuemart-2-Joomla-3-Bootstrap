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


?>
    <fieldset>
	<legend><?php echo JText::_('COM_VIRTUEMART_FORM_GENERAL'); ?></legend>
	<?php
	$this->customfields->addHidden('view', 'custom');
	$this->customfields->addHidden('task', '');
	$this->customfields->addHidden(JSession::getFormToken(), 1);
//if ($this->custom->custom_parent_id) $this->customfields->addHidden('custom_parent_id',$this->custom->custom_parent_id);
	$attribute_id = JRequest::getVar('attribute_id', '');
	if (!empty($attribute_id))
	    $this->customfields->addHidden('attribute_id', $attribute_id);
	?>
	<table class="admintable table">
	    <?php echo $this->customfields->displayCustomFields($this->custom); ?>
	</table>
    <?php 
	if (!empty($this->customPlugin->custom_jplugin_id)) { ?>
        <input type="hidden" name="custom_jplugin_id" value="<?php echo $this->customPlugin->virtuemart_custom_id ?>" >
    <?php } 
	if (!empty($this->tmpl)) { ?>
        <input type="hidden" name="tmpl" value="component" >
    <?php } ?>

