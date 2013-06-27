<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage UpdatesMigration
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: default_update.php 3274 2011-05-17 20:43:48Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<?php
$checkLatestVerisonLink = JROUTE::_('index.php?option=com_virtuemart&view=updatesmigration&task=checkForLatestVersion');
$testVersionLink = JROUTE::_('index.php?option=com_virtuemart&view=updatesmigration&task=testVersion&view=updatesmigration');
$installSampleLink = JROUTE::_('index.php?option=com_virtuemart&view=updatesmigration&task=installSample&view=updatesmigration');
$updateVMTables10to11Link = JROUTE::_('index.php?option=com_virtuemart&view=updatesmigration&task=updateVMTables10to11&view=updatesmigration');
$updateVMTables11to15Link = JROUTE::_('index.php?option=com_virtuemart&view=updatesmigration&task=updateVMTables11to15&view=updatesmigration');

$linkDeleteALL =JROUTE::_('index2.php?option=com_virtuemart&view=updatesmigration&view=updatesmigration&task=deleteAll');
$linkDeleteOnlyRestorable =JROUTE::_('index2.php?option=com_virtuemart&view=updatesmigration&view=updatesmigration&task=deleteRestorable');
$linkDoNothing =JROUTE::_('index2.php');
?>
<br />
<table class="admintable">
    <tr>
	<td class="key"><?php echo JText::_('COM_VIRTUEMART_UPDATE_CHECK_VERSION_INSTALLED'); ?></td>
	<td>
	    <h1 style="display:inline">
		<?php echo VmConfig::getInstalledVersion(); ?>
	    </h1>
	</td>
    </tr>
    <tr>
	<td class="key"><?php echo JText::_('COM_VIRTUEMART_UPDATE_CHECK_LATEST_VERSION'); ?></td>
	<td>
	    <?php
	    if ($this->latestVersion) {
		echo "<h1 style='display:inline'>" . $this->latestVersion . "</h1>";
	    }
	    else {?>
	    <a href="<?php echo $checkLatestVerisonLink; ?>">
		&nbsp;[<?php echo JText::_('COM_VIRTUEMART_UPDATE_CHECK_CHECKNOW'); ?>]</a>
		<?php
	    }
	    ?>
	    <?php
	    if ($this->latestVersion) {
		if (version_compare($this->latestVersion, VmConfig::getInstalledVersion(), '>') == 1) {
		    ?>
	    <input name="downloadbutton" id="downloadbutton" type="submit" value="<?php echo JText::_('COM_VIRTUEMART_UPDATE_CHECK_DLUPDATE'); ?>" style="<?php echo $downloadbutton_style ?>font-weight:bold;" />
		    <?php
		}
		else {
		    // need something in the lanuage file here
		    echo '&nbsp;&nbsp;' . JText::_('COM_VIRTUEMART_UPDATE_NONEWVERSION');
		}
	    }
	    ?>
	</td>
    </tr>
</table>