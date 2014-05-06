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

if(!class_exists('VmController'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmcontroller.php');
		VmConfig::loadJLang('com_virtuemart_media');
/**
 * Product Controller
 *
 * @package    VirtueMart
 * @author Max Milbers
 */
class VirtuemartControllerMedia extends VmController {

	/**
	 * for ajax call media
	 */
	function viewJson() {
		
		// $this->addViewPath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart' . DS . 'views'); 
		/* Create the view object. */
		// $view = $this->getView('media', 'json');

		/* Now display the view. */
		$this->display();
	}

	function save($data = 0){

		$fileModel = VmModel::getModel('media');

		//Now we try to determine to which this media should be long to
		$data = JRequest::get('post');
		// remove shared when not superVendor
		if ($this->_vendor>1) {
			if (isset($data['shared'])) $data['shared']= 0;
		}
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
			$this->redirectPath .= '&task=edit&virtuemart_media_id='.$id;
		}

		$this->setRedirect(null, $msg);
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

		$this->setRedirect(null, $result);
	}
	function removeUnused(){
		JSession::checkToken() or jexit('Invalid Token, in ' . JRequest::getWord('task'));
		if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
		if(!Permissions::getInstance()->check('admin')){
		    $msg = jText::_('JERROR_CORE_DELETE_NOT_PERMITTED');
			$type = 'error';
		} else {
		    $msg = jText::_('COM_VIRTUEMART_SYNC_MEDIA_FILES').' '.jText::_('COM_VIRTUEMART_ADMIN_UPDATES');
			$type = null;
		}
		$ids = array();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$db = JFactory::getDBO();

		$q = 'SELECT m.`virtuemart_media_id`,`file_type`,`file_url`,`file_url_thumb`,`file_is_forSale` FROM `#__virtuemart_medias` as m
			LEFT JOIN `#__virtuemart_product_medias` as pm
			ON m.`virtuemart_media_id`=pm.`virtuemart_media_id`
			WHERE m.file_type ="product" AND pm.`virtuemart_media_id` IS NULL';
		$db->setQuery($q);
		$unusedMedias = $db->loadObjectList();

		//remove all unused files
		foreach($unusedMedias as $file){

			if($file->file_is_forSale!=1){
				$media_path = JPATH_ROOT.DS.str_replace('/',DS,$file->file_url);
			} else {
				$media_path = $file->file_url;
			}
			if ($ret = JFile::delete($media_path)) {
				$removed++;
			} else vmInfo( $media_path. ' file unknow');
			if ($ret = JFile::delete($file->file_url_thumb)) {
				$removed++;
			} else vmInfo( $file->file_url_thumb. ' file unknow');
			$ids[] = $file->virtuemart_media_id;
		}
		if(count($ids) < 1) {
			$msg = JText::_('COM_VIRTUEMART_SELECT_ITEM_TO_DELETE');
			$type = 'notice';
		} else {
			$model = VmModel::getModel($this->_cname);
			$ret = $model->remove($ids,$this->_vendor);
			$errors = $model->getErrors();
			$msg = JText::sprintf('COM_VIRTUEMART_STRING_DELETED',$this->mainLangKey);
			if(!empty($errors) or $ret==false) {
				$msg = JText::sprintf('COM_VIRTUEMART_STRING_COULD_NOT_BE_DELETED',$this->mainLangKey);
						$type = 'error';
			}
			else $type = null;
			foreach($errors as $error){
				$msg .= '<br />'.($error);
			}
		}
		if (removed) vmInfo( $removed .' files removed');
		$this->setRedirect(null, $msg,$type);
	}
}
// pure php no closing tag
