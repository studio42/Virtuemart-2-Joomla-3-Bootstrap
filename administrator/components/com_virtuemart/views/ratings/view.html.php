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
if(!class_exists('VmView'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmview.php');

/**
 * HTML View class for ratings (and customer reviews)
 *
 */
class VirtuemartViewRatings extends VmView {
	public $max_rating;

	function display($tpl = null) {

		$mainframe = Jfactory::getApplication();
		$option = JRequest::getWord('option');

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
		$this->assignRef('max_rating', $this->max_rating);

		$model = VmModel::getModel();
		$this->SetViewTitle('REVIEW_RATE' );


		/* Get the task */
		$task = JRequest::getWord('task');
		switch ($task) {
			case 'listreviews':
				/* Get the data */
				$this->addStandardDefaultViewLists($model);
				$virtuemart_product_id = JRequest::getInt('virtuemart_product_id',0);
				$reviewslist = $model->getReviews($virtuemart_product_id);

				$lists = array();
				$lists['filter_order'] = $mainframe->getUserStateFromRequest($option.'filter_order', 'filter_order', '', 'cmd');
				$lists['filter_order_Dir'] = $mainframe->getUserStateFromRequest($option.'filter_order_Dir', 'filter_order_Dir', '', 'word');

				$this->assignRef('reviewslist', $reviewslist);

				$pagination = $model->getPagination();
				$this->assignRef('pagination', $pagination);

				$this->addStandardDefaultViewCommands(false,true);
				break;

			case 'edit':
				/* Get the data */
				$rating = $model->getRating($cids);
				$this->addStandardEditViewCommands();

				/* Assign the data */
				$this->assignRef('rating', $rating);

				break;
			case 'edit_review':

				JToolBarHelper::divider();

				/* Get the data */
				$rating = $model->getReview($cids);
				if(!empty($rating)){
					$this->SetViewTitle('REVIEW_RATE',$rating->product_name." (". $rating->customer.")" );

					JToolBarHelper::customX('saveReview', 'save', 'save',  JText::_('COM_VIRTUEMART_SAVE'), false);
					JToolBarHelper::customX('applyReview', 'apply', 'apply',  JText::_('COM_VIRTUEMART_APPLY'), false);

				} else {
					$this->SetViewTitle('REVIEW_RATE','ERROR' );
				}

				JToolBarHelper::customX('cancelEditReview', 'cancel', 'cancel',  JText::_('COM_VIRTUEMART_CANCEL'), false);

				/* Assign the data */
				$this->assignRef('rating', $rating);

				break;
			default:

				$this->addStandardDefaultViewCommands(false, true);
				$this->addStandardDefaultViewLists($model);

				$ratingslist = $model->getRatings();
				$this->assignRef('ratingslist', $ratingslist);

				$pagination = $model->getPagination();
				$this->assignRef('pagination', $pagination);

				break;
		}
		parent::display($tpl);
	}

}
// pure php no closing tag
