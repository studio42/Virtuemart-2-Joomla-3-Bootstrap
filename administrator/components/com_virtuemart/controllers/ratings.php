<?php
/**
*
* Review controller
*
* @package	VirtueMart
* @subpackage
* @author Max Milberes
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: ratings.php 6219 2012-07-04 16:10:42Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if (!class_exists ('VmController')){
	require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmcontroller.php');
}

/**
 * Review Controller
 *
 * @package    VirtueMart
 * @author Max Milbers
 */
class VirtuemartControllerRatings extends VmController {

	/**
	 * Generic edit task
	 *
	 * @author Max Milbers
	 */
	function edit_review(){

		JRequest::setVar('layout', 'edit_review');
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	/**
	 * lits the reviews
	 * @author Max Milbers,Patrick Kohl
	 */
	public function listreviews(){

		$this->display();
	}

	/**
	 * we must overwrite it here, because the task publish can be meant for two different list layouts.
	 */
	function publish(){

		JSession::checkToken() or jexit( 'Invalid Token save' );
		$layout = JRequest::getString('layout','default');

		if($layout=='list_reviews'){

			$virtuemart_product_id = JRequest::getVar('virtuemart_product_id',array(),'', 'array');
			if(is_array($virtuemart_product_id) && count($virtuemart_product_id) > 0){
				$virtuemart_product_id = (int)$virtuemart_product_id[0];
			} else {
				$virtuemart_product_id = (int)$virtuemart_product_id;
			}
			$redPath = '';
			if (!empty($virtuemart_product_id)) {
				$redPath = '&task=listreviews&virtuemart_product_id=' . $virtuemart_product_id;
			}

			parent::publish('virtuemart_rating_review_id','rating_reviews',$this->redirectPath.$redPath);
		} else {
			parent::publish();
		}

	}

	function unpublish(){

		JSession::checkToken() or jexit( 'Invalid Token save' );
		$layout = JRequest::getString('layout','default');

		if($layout=='list_reviews'){

			$virtuemart_product_id = JRequest::getVar('virtuemart_product_id',array(),'', 'array');
			if(is_array($virtuemart_product_id) && count($virtuemart_product_id) > 0){
				$virtuemart_product_id = (int)$virtuemart_product_id[0];
			} else {
				$virtuemart_product_id = (int)$virtuemart_product_id;
			}
			$redPath = '';
			if (!empty($virtuemart_product_id)) {
				$redPath = '&task=listreviews&virtuemart_product_id=' . $virtuemart_product_id;
			}

			parent::unpublish('virtuemart_rating_review_id','rating_reviews',$this->redirectPath.$redPath);
		} else {
			parent::unpublish();
		}

	}

	/**
	 * Save task for review
	 *
	 * @author Max Milbers
	 */
	function saveReview(){

		$this->storeReview(FALSE);
	}

	/**
	 * Save task for review
	 *
	 * @author Max Milbers
	 */
	function applyReview(){

		$this->storeReview(TRUE);
	}


	function storeReview($apply){
		JSession::checkToken() or jexit( 'Invalid Token save' );

		if (empty($data)){
			$data = JRequest::get ('post');
		}

		$model = VmModel::getModel($this->_cname);
		$id = $model->saveRating($data);

		$errors = $model->getErrors();
		if (empty($errors)) {
			$msg = JText::sprintf ('COM_VIRTUEMART_STRING_SAVED', $this->mainLangKey);
		}
		foreach($errors as $error){
			$msg = ($error).'<br />';
		}

		if($apply){
			$this->redirectPath .= '&task=edit_review&virtuemart_rating_review_id='.$id;
		} else {
				$virtuemart_product_id = JRequest::getInt('virtuemart_product_id');

			$this->redirectPath .= 'task=listreviews&virtuemart_product_id='.$virtuemart_product_id;
		}

		$this->setRedirect(null, $msg);
	}
	/**
	 * Save task for review
	 *
	 * @author Max Milbers
	 */
	function cancelEditReview(){

		$virtuemart_product_id = JRequest::getInt('virtuemart_product_id');
		$msg = JText::sprintf('COM_VIRTUEMART_STRING_CANCELLED',$this->mainLangKey); //'COM_VIRTUEMART_OPERATION_CANCELED'
		$this->redirectPath.='&task=listreviews&virtuemart_product_id='.$virtuemart_product_id;
		$this->setRedirect(null, $msg);
	}

}
// pure php no closing tag
