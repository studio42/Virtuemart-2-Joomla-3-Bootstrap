<?php

/**
 *
 * Description
 *
 * @package	VirtueMart
 * @subpackage shipment
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: edit_config.php 3386 2011-05-27 12:34:11Z alatak $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if ($this->shipment->shipment_name) {
    $parameters = new vmParameters($this->shipment, $this->shipment->shipment_element, 'plugin', 'vmshipment');

    echo $rendered = $parameters->render();
} else {
    echo JText::_('COM_VIRTUEMART_SELECT_SHIPPING_METHOD');
}
 /*
  <script type="text/javascript">
  function check() {
  if (document.adminForm.type[0].checked == true || document.adminForm.type[1].checked == true) {
  document.getElementById('accepted_creditcards1').innerHTML = '<strong><?php echo JText::_('COM_VIRTUEMART_PAYMENT_ACCEPTED_CREDITCARDS') ?>:';
  if (document.getElementById('accepted_creditcards_store').innerHTML != '')
  document.getElementById('accepted_creditcards2').innerHTML ='<input type="text" name="accepted_creditcards" value="' + document.getElementById('accepted_creditcards_store').innerHTML + '" class="inputbox" />';
  else
  document.getElementById('accepted_creditcards2').innerHTML = '<?php ps_creditcard::creditcard_checkboxes( $this->paym->payment_creditcards ); ?>';
  }
  else {
  try {
  document.getElementById('accepted_creditcards_store').innerHTML = document.adminForm.accepted_creditcards.value;
  }
  catch (e) {}
  document.getElementById('accepted_creditcards1').innerHTML = '';
  document.getElementById('accepted_creditcards2').innerHTML = '';
  }
  }
  check();
  </script> */
