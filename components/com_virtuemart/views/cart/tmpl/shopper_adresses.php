<?php
/**
*
* Layout for the shopping cart and the mail
* shows the chosen adresses of the shopper
* taken from the cart in the session
*
* @package	VirtueMart
* @subpackage Cart
* @author Max Milbers
*
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<table width="100%">
  <tr>
    <td width="50%" bgcolor="#ccc">
		<?php echo JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL'); ?>
	</td>
	<td width="50%" bgcolor="#ccc">
		<?php echo JText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL'); ?>
	</td>
  </tr>
  <tr>
    <td width="50%">

		<?php 	foreach($this->BTaddress['fields'] as $item){
					if(!empty($item['value'])){
						echo $item['title'].': '.$this->escape($item['value']).'<br/>';
					}
				} ?>

	</td>
    <td width="50%">
			<?php
			if(!empty($this->STaddress['fields'])){
				foreach($this->STaddress['fields'] as $item){
					if(!empty($item['value'])){
						echo $item['title'].': '.$this->escape($item['value']).'<br/>';
					}
				}
			} else {
				foreach($this->BTaddress['fields'] as $item){
					if(!empty($item['value'])){
						echo $item['title'].': '.$this->escape($item['value']).'<br/>';
					}
				}
			} ?>
	</td>
  </tr>
</table>