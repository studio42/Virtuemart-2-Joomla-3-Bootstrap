<?php
/**
*
* Description
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
* @version $Id: category.php 2641 2010-11-09 19:25:13Z milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();


/**
* Class Description
*
* @package VirtueMart
* @author Max Milbers
*/
class VirtueMartControllerCategories extends JControllerLegacy {

	/**
	* Function Description
	*
	* @author RolandD
	* @author George
	* @access public
	*/
	public function display($cachable = false, $urlparams = false)  {

		if (JRequest::getvar('search')) {
			$safeurlparams = '';
			$cachable = false;
		} else {
			// Display it all
			$safeurlparams = array('virtuemart_category_id'=>'INT','virtuemart_manufacturer_id'=>'INT','virtuemart_currency_id'=>'INT','return'=>'BASE64','lang'=>'CMD','orderby'=>'CMD','limitstart'=>'CMD','order'=>'CMD','limit'=>'CMD');
		}
		parent::display($cachable, $safeurlparams);
		if($categoryId = JRequest::getInt('virtuemart_category_id',0)){
			shopFunctionsF::setLastVisitedCategoryId($categoryId);
		}
		return $this;
	}

	public function json(){

		parent::display();

	}
}
// pure php no closing tag
