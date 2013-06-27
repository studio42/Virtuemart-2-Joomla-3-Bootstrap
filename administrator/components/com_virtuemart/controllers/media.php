<?php
/**
*
* Media controller
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
* @version $Id: media.php 6071 2012-06-06 15:33:04Z Milbo $
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
 * @author Max Milbers
 */
class VirtuemartControllerMedia extends VmController {

	/**
	 * Method to display the view
	 *
	 * @access	public
	 * @author
	 */
	function __construct() {
		parent::__construct('virtuemart_media_id');

	}


	/**
	 * for ajax call media
	 */
	function viewJson() {

		/* Create the view object. */
		$view = $this->getView('media', 'json');

		/* Now display the view. */
		$view->display(null);
	}

	function save($data = 0){

		$fileModel = VmModel::getModel('media');

		//Now we try to determine to which this media should be long to
		$data = JRequest::get('post');

		//$data['file_title'] = JRequest::getVar('file_title','','post','STRING',JREQUEST_ALLOWHTML);
		$data['file_description'] = JRequest::getVar('file_description','','post','STRING',JREQUEST_ALLOWHTML);

		$data['media_attributes'] = JRequest::getWord('media_attributes');
		$data['file_type'] = JRequest::getWord('file_type');
		if(empty($data['file_type'])){
			$data['file_type'] = $data['media_attributes'];
		}

		if ($id = $fileModel->store($data,$data['file_type'])) {
			$msg = JText::_('COM_VIRTUEMART_FILE_SAVED_SUCCESS');
		} else {
			$msg = $fileModel->getError();
		}

		$cmd = JRequest::getCmd('task');
		if($cmd == 'apply'){
			$redirection = 'index.php?option=com_virtuemart&view=media&task=edit&virtuemart_media_id='.$id;
		} else {
			$redirection = 'index.php?option=com_virtuemart&view=media';
		}

		$this->setRedirect($redirection, $msg);
	}

	function synchronizeMedia(){

		if(!class_exists('Permissions'))
	    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
		if(!Permissions::getInstance()->check('admin')){
		    $msg = 'Forget IT';
		    $this->setRedirect('index.php?option=com_virtuemart', $msg);
		}

		if(!class_exists('Migrator')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'migrator.php');
		$migrator = new Migrator();
		$result = $migrator->portMedia();

		$this->setRedirect($this->redirectPath, $result);
	}

}
// pure php no closing tag
