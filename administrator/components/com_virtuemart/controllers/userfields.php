<?php
/**
*
* Userfields controller
*
* @package	VirtueMart
* @subpackage Userfields
* @author Oscar van Eijk
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: userfields.php 5644 2012-03-09 22:36:32Z electrocity $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');


/**
 * Controller class for the Order status
 *
 * @package    VirtueMart
 * @subpackage Userfields
 * @author     Oscar van Eijk
 */
class VirtuemartControllerUserfields extends VmController {

	/**
	 * Method to display the view
	 *
	 * @access public
	 * @author
	 */
	function __construct(){
		parent::__construct('virtuemart_userfield_id');

	}

	function Userfields(){

		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$view = $this->getView('userfields', $viewType);
		$view->loadHelper('paramhelper');

		parent::display();
	}
	function viewJson() {

		// Create the view object.
		$view = $this->getView('userfields', 'json');

		// Now display the view.
		$view->display(null);
	}

}

//No Closing tag
