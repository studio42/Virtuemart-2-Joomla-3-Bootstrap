<?php

/**

*

* Description

*

* @package	VirtueMart

* @subpackage

* @author StephanieS

* @link http://www.virtuemart.net

* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.

* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php

* VirtueMart is free software. This version may have been modified pursuant

* to the GNU General Public License, and as distributed it includes or

* is derivative of works licensed under the GNU General Public License or

* other free or open source software licenses.

* @version $Id: edit.php 5225 2012-01-06 01:50:19Z electrocity $

*/



// Check to ensure this file is included in Joomla!

defined('_JEXEC') or die('Restricted access');


AdminUIHelper::startAdminArea();

?>


<form action="index.php" method="post" name="adminForm" id="adminForm">


<div class="col50">

	<fieldset>

	<legend><?php echo JText::_('COM_VIRTUEMART_USERGROUP_DETAILS'); ?></legend>

	<table class="admintable">

		<tr>

			<td width="110" class="key">

				<label for="group_name">

					<?php echo JText::_('COM_VIRTUEMART_USERGROUPS_LIST_NAME'); ?>:

				</label>

			</td>

			<td>

				<input class="inputbox" type="text" name="group_name" id="group_name" size="50" value="<?php echo $this->usergroup->group_name; ?>" />

			</td>

		</tr>

		<tr>

			<td width="110" class="key">

				<label for="group_level">

					<?php echo JText::_('COM_VIRTUEMART_USERGROUPS_LEVEL'); ?>:

				</label>

			</td>

			<td>

				<input class="inputbox" type="text" name="group_level" id="group_level" size="3" value="<?php echo $this->usergroup->group_level; ?>" />

			</td>

		</tr>

	</table>

	</fieldset>

</div>

	<input type="hidden" name="virtuemart_shoppergroup_id" value="<?php echo $this->usergroup->virtuemart_shoppergroup_id; ?>" />

	<?php echo $this->addStandardHiddenToForm(); ?>

</form>





<?php AdminUIHelper::endAdminArea(); ?>