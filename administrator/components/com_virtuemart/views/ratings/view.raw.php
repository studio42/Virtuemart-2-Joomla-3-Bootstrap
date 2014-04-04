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

		// Figure out maximum rating scale (default is 5 stars)
		$this->max_rating = VmConfig::get('vm_maximum_rating_scale',5);

		$model = VmModel::getModel();

		/* Get the task */
		$task = JRequest::getWord('task');
		switch ($task) {
			case 'listreviews':
				/* Get the data */
				$this->addStandardDefaultViewLists($model,0,'ASC','filter_ratings');
				$virtuemart_product_id = JRequest::getInt('virtuemart_product_id',0);
				$this->reviewslist = $model->getReviews($virtuemart_product_id);
				$this->pagination = $model->getPagination();
				$this->addStandardDefaultViewCommands(false,true);
				$this->setLayout('listreviews');
				$tpl = 'results';
				break;

			default:
				$this->addStandardDefaultViewCommands(false, true);
				$this->addStandardDefaultViewLists($model,0,'ASC','filter_ratings');
				$this->ratingslist = $model->getRatings();
				$this->pagination = $model->getPagination();
				$tpl = 'results';
				break;
		}
		parent::display($tpl);
		echo $this->AjaxScripts();
	}

}
// pure php no closing tag
