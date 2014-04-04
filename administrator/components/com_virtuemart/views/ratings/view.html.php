<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage	ratings
* @author
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 6219 2012-07-04 16:10:42Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmview.php');

/**
 * HTML View class for ratings (and customer reviews)
 *
 */
class VirtuemartViewRatings extends VmView {
	public $max_rating;

	function display($tpl = null) {

		//Load helpers
		$this->loadHelper('html');

		/* Get the review IDs to retrieve (input variable may be cid, cid[] or virtuemart_rating_review_id */
		$cids = JRequest::getVar('cid', 0);
		if (empty($cids)) {
			$cids= JRequest::getVar('virtuemart_rating_review_id',0);
		}
		if ($cids && !is_array($cids)) $cids = array($cids);

		jimport( 'joomla.utilities.arrayhelper' );
		JArrayHelper::toInteger($cids);

		// Figure out maximum rating scale (default is 5 stars)
		$this->max_rating = VmConfig::get('vm_maximum_rating_scale',5);

		$model = VmModel::getModel();
		$this->SetViewTitle('REVIEW_RATE' );


		/* Get the task */
		$task = JRequest::getWord('task');
		switch ($task) {
			case 'listreviews':
				/* Get the data */
				$this->addStandardDefaultViewLists($model,0,'ASC','filter_ratings');
				//TODO note this is not assigned, old code ?
				// $lists = array();
				// $lists['filter_order'] = $mainframe->getUserStateFromRequest($option.'filter_order', 'filter_order', '', 'cmd');
				// $lists['filter_order_Dir'] = $mainframe->getUserStateFromRequest($option.'filter_order_Dir', 'filter_order_Dir', '', 'word');
				$this->setLayout('listreviews');
				$virtuemart_product_id = JRequest::getVar('virtuemart_product_id',array(),'', 'array');
				if(is_array($virtuemart_product_id) && count($virtuemart_product_id) > 0){
					$virtuemart_product_id = (int)$virtuemart_product_id[0];
				} else {
					$virtuemart_product_id = (int)$virtuemart_product_id;
				}
				$this->reviewslist = $model->getReviews($virtuemart_product_id);
				$this->pagination = $model->getPagination();
				$this->addStandardDefaultViewCommands(false,true);

				break;
			case 'edit':
			case 'add':
				/* Get the data */
				$rating_id = JRequest::getVar('vrituemart_rating_id', 0);
				$this->rating = $model->getRating($rating_id);
				$this->addStandardEditViewCommands();
				break;
			case 'edit_review':
				JToolBarHelper::divider();

				/* Get the data */
				$rating = $model->getReview($cids);
				if(!empty($rating)){
					$this->SetViewTitle('REVIEW_RATE',$rating->product_name." (". $rating->customer.")" );
					JToolBarHelper::custom('saveReview', 'save', 'save',  JText::_('COM_VIRTUEMART_SAVE'), false);
					JToolBarHelper::custom('applyReview', 'apply', 'apply',  JText::_('COM_VIRTUEMART_APPLY'), false);
				} else {
					$this->SetViewTitle('REVIEW_RATE','ERROR' );
				}
				JToolBarHelper::custom('cancelEditReview', 'cancel', 'cancel',  JText::_('COM_VIRTUEMART_CANCEL'), false);

				/* Assign the data */
				$this->rating = $rating ;
				break;
			default:
				$this->addStandardDefaultViewCommands(false, true);
				$this->addStandardDefaultViewLists($model,0,'ASC','filter_ratings');
				$this->ratingslist = $model->getRatings();
				$this->pagination = $model->getPagination();

				break;
		}
		parent::display($tpl);
	}

}
// pure php no closing tag
