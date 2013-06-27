<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
/**
*
* Base controller
*
* @package	VirtueMart
* @subpackage Core
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2011 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id$
*/

jimport('joomla.application.component.controller');

/**
 * VirtueMart default administrator controller
 *
 * @package		VirtueMart
 */
class VirtuemartControllerVirtuemart extends JController {


	public function __construct() {
		parent::__construct();
	}

	/**
	 *
	 * Task for disabling dangerous database tools, used after install
	 * @author Max Milbers
	 */
	public function disableDangerousTools(){
		$data = JRequest::get('get');
		JRequest::setVar($data['token'], '1', 'post');
		$config = JModel::getInstance('config', 'VirtueMartModel');
		$config->setDangerousToolsOff();
		$this->display();
	}
}
