<?php

/**
 *
 * Controller for the Plugins Response
 *
 * @package	VirtueMart
 * @subpackage paymentResponse
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: cart.php 3388 2011-05-27 13:50:18Z alatak $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

/**
 * Controller for the payment response view
 *
 * @package VirtueMart
 * @subpackage paymentResponse
 * @author Valérie Isaksen
 *
 */
class VirtueMartControllerPluginresponse extends JController {

    /**
     * Construct the cart
     *
     * @access public
     */
    public function __construct() {
	parent::__construct();
    }

    /**
     * ResponseReceived()
     * From the plugin page, the user returns to the shop. The order email is sent, and the cart emptied.
     *
     * @author Valerie Isaksen
     *
     */
    function pluginResponseReceived() {

	$this->PaymentResponseReceived();
	$this->ShipmentResponseReceived();
    }

    /**
     * ResponseReceived()
     * From the payment page, the user returns to the shop. The order email is sent, and the cart emptied.
     *
     * @author Valerie Isaksen
     *
     */
    function PaymentResponseReceived() {

	if (!class_exists('vmPSPlugin'))
	    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php'); JPluginHelper::importPlugin('vmpayment');

	$return_context = "";
	$dispatcher = JDispatcher::getInstance();
	$html = "";
	$paymentResponse = Jtext::_('COM_VIRTUEMART_CART_THANKYOU');
	$returnValues = $dispatcher->trigger('plgVmOnPaymentResponseReceived', array( 'html' => &$html,&$paymentResponse));

// 	JRequest::setVar('paymentResponse', Jtext::_('COM_VIRTUEMART_CART_THANKYOU'));
// 	JRequest::setVar('paymentResponseHtml', $html);
	$view = $this->getView('pluginresponse', 'html');
	$layoutName = JRequest::getVar('layout', 'default');
	$view->setLayout($layoutName);

	$view->assignRef('paymentResponse', $paymentResponse);
   $view->assignRef('paymentResponseHtml', $html);

	// Display it all
	$view->display();
    }

    function ShipmentResponseReceived() {
		// TODO: not ready yet

	    if (!class_exists('vmPSPlugin'))
		    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
	    JPluginHelper::importPlugin('vmshipment');

	    $return_context = "";
	    $dispatcher = JDispatcher::getInstance();

	    $html = "";
	    $shipmentResponse = Jtext::_('COM_VIRTUEMART_CART_THANKYOU');
	    $dispatcher->trigger('plgVmOnShipmentResponseReceived', array( 'html' => &$html,&$shipmentResponse));
/*
// 	JRequest::setVar('paymentResponse', Jtext::_('COM_VIRTUEMART_CART_THANKYOU'));
// 	JRequest::setVar('paymentResponseHtml', $html);
	    $view = $this->getView('pluginresponse', 'html');
	    $layoutName = JRequest::getVar('layout', 'default');
	    $view->setLayout($layoutName);

	    $view->assignRef('shipmentResponse', $shipmentResponse);
	    $view->assignRef('shipmentResponseHtml', $html);

	    // Display it all
	    $view->display();
	    */

    }

    /**
     * PaymentUserCancel()
     * From the payment page, the user has cancelled the order. The order previousy created is deleted.
     * The cart is not emptied, so the user can reorder if necessary.
     * then delete the order
     * @author Valerie Isaksen
     *
     */
    function pluginUserPaymentCancel() {

	if (!class_exists('vmPSPlugin'))
	    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');

	if (!class_exists('VirtueMartCart'))
	    require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');

	JPluginHelper::importPlugin('vmpayment');
	$dispatcher = JDispatcher::getInstance();
	$dispatcher->trigger('plgVmOnUserPaymentCancel', array());

	// return to cart view
	$view = $this->getView('cart', 'html');
	$layoutName = JRequest::getWord('layout', 'default');
	$view->setLayout($layoutName);

	// Display it all
	$view->display();
    }

    /**
     * Attention this is the function which processs the response of the payment plugin
     *
     * @author Valerie Isaksen
     * @return success of update
     */
    function pluginNotification() {

	if (!class_exists('vmPSPlugin'))
	    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');

	if (!class_exists('VirtueMartCart'))
	    require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');

	if (!class_exists('VirtueMartModelOrders'))
	    require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );

	JPluginHelper::importPlugin('vmpayment');

	$dispatcher = JDispatcher::getInstance();
	$returnValues = $dispatcher->trigger('plgVmOnPaymentNotification', array());

    }

}

//pure php no Tag
