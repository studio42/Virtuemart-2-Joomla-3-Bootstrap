<?php
/**
*
* State controller
*
* @package	VirtueMart
* @subpackage State
* @author RickG, Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: state.php 5399 2012-02-08 19:29:45Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');


/**
 * Product Controller
 *
 * @package    VirtueMart
 * @subpackage State
 * @author RickG, Max Milbers
 */
class VirtuemartControllerState extends VmController {

	/**
	 * Method to display the view
	 *
	 * @access	public
	 * @author RickG, Max Milbers
	 */
	function __construct() {
		parent::__construct('virtuemart_state_id');

		$country = JRequest::getInt('virtuemart_country_id', 0);
		$this->redirectPath .= ($country > 0) ? '&virtuemart_country_id=' . $country : '';
	}


	/**
	 * Retrieve full statelist
	 */
	function getList() {

		/* Create the view object. */
		$view = $this->getView('state', 'json');

		/* Now display the view. */
		$view->display(null);
	}
}

