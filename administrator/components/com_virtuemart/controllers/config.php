<?php
/**
*
* Config controller
*
* @package	VirtueMart
* @subpackage Config
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: config.php 6188 2012-06-29 09:38:30Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');


/**
 * Configuration Controller
 *
 * @package    VirtueMart
 * @subpackage Config
 * @author RickG
 */
class VirtuemartControllerConfig extends VmController {

	/**
	 * Method to display the view
	 *
	 * @access	public
	 * @author
	 */
	function __construct() {
		parent::__construct();

	}


	/**
	 * Handle the save task
	 *
	 * @author RickG
	 */
	function save($data = 0){

		JRequest::checkToken() or jexit( 'Invalid Token' );
		$model = VmModel::getModel('config');

		$data = JRequest::get('post');
		$data['offline_message'] = JRequest::getVar('offline_message','','post','STRING',JREQUEST_ALLOWHTML);

		if(strpos($data['offline_message'],'|')!==false){
			$data['offline_message'] = str_replace('|','',$data['offline_message']);
		}

		if ($model->store($data)) {
			$msg = JText::_('COM_VIRTUEMART_CONFIG_SAVED');
			// Load the newly saved values into the session.
			VmConfig::loadConfig();
		}
		else {
			$msg = $model->getError();
		}

		$redir = 'index.php?option=com_virtuemart';
		if(JRequest::getCmd('task') == 'apply'){
			$redir = $this->redirectPath;
		}

		$this->setRedirect($redir, $msg);


	}


	/**
	 * Overwrite the remove task
	 * Removing config is forbidden.
	 * @author Max Milbers
	 */
	function remove(){

		$msg = JText::_('COM_VIRTUEMART_ERROR_CONFIGS_COULD_NOT_BE_DELETED');

		$this->setRedirect( $this->redirectPath , $msg);
	}
}

//pure php no tag
