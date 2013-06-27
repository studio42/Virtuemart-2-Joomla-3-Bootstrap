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

// Load the controller framework
jimport('joomla.application.component.controller');

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
	 * Method to display the view
	 *
	 * @access	public
	 */
	function __construct() {
		parent::__construct();

		$task = JRequest::getVar('task');
		vmdebug('cconstruct',$task);
	}

	/**
	 * Generic edit task
	 *
	 * @author Max Milbers
	 */
	function edit_review(){

		JRequest::setVar('controller', $this->_cname);
		JRequest::setVar('view', $this->_cname);
		JRequest::setVar('layout', 'edit_review');
// 		JRequest::setVar('hidemenu', 1);

		if(empty($view)){
			$document = JFactory::getDocument();
			$viewType = $document->getType();
			$view = $this->getView($this->_cname, $viewType);
		}


		parent::display();
	}

	/**
	 * lits the reviews
	 * @author Max Milbers
	 */
	public function listreviews(){

		/* Create the view object */
		$view = $this->getView('ratings', 'html');

		$view->setLayout('list_reviews');

		$view->display();
	}

	/**
	 * we must overwrite it here, because the task publish can be meant for two different list layouts.
	 */
	function publish(){

		JRequest::checkToken() or jexit( 'Invalid Token save' );
		$layout = JRequest::getString('layout','default');

		if($layout=='list_reviews'){

			$product_id= JRequest::getInt('virtuemart_product_id',0);
			$redPath = '';
			if (!empty($product_id)) {
				$redPath = '&task=listreviews&virtuemart_product_id=' . $product_id;
			}

			parent::publish('virtuemart_rating_review_id','rating_reviews',$this->redirectPath.$redPath);
		} else {
			parent::publish();
		}

	}

	function unpublish(){

		JRequest::checkToken() or jexit( 'Invalid Token save' );
		$layout = JRequest::getString('layout','default');

		if($layout=='list_reviews'){

			$product_id= JRequest::getInt('virtuemart_product_id',0);
			$redPath = '';
			if (!empty($product_id)) {
				$redPath = '&task=listreviews&virtuemart_product_id=' . $product_id;
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
		JRequest::checkToken() or jexit( 'Invalid Token save' );

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

		$redir = $this->redirectPath;
		if($apply){
			$redir = 'index.php?option=com_virtuemart&view=ratings&task=edit_review&virtuemart_rating_review_id='.$id;
		} else {
			$virtuemart_product_id = JRequest::getInt('virtuemart_product_id',0);
			$redir = 'index.php?option=com_virtuemart&view=ratings&task=listreviews&virtuemart_product_id='.$virtuemart_product_id;
		}

		$this->setRedirect($redir, $msg);
	}
	/**
	 * Save task for review
	 *
	 * @author Max Milbers
	 */
	function cancelEditReview(){

		$virtuemart_product_id = JRequest::getInt('virtuemart_product_id',0);
		$msg = JText::sprintf('COM_VIRTUEMART_STRING_CANCELLED',$this->mainLangKey); //'COM_VIRTUEMART_OPERATION_CANCELED'
		$this->setRedirect('index.php?option=com_virtuemart&view=ratings&task=listreviews&virtuemart_product_id='.$virtuemart_product_id, $msg);
	}

}
// pure php no closing tag
