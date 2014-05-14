<?php
/**
*
* Category controller
*
* @package	VirtueMart
* @subpackage Category
* @author RickG, jseros
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: category.php 6071 2012-06-06 15:33:04Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

// Load the controller framework

if(!class_exists('VmController')) require(JPATH_VM_ADMINISTRATOR.'/helpers/vmcontroller.php');

/**
 * Category Controller
 *
 * @package    VirtueMart
 * @subpackage Category
 * @author jseros, Max Milbers, Patrick Kohl
 */
class VirtuemartControllerCategory extends VmController {

	/**
	 * We want to allow html so we need to overwrite some request data
	 *
	 * @author Max Milbers, Patrick Kohl
	 */
	function save($data = 0){

		$data = JRequest::get('post');
		$data['category_name'] = $this->filterText('category_name');
		$data['category_description'] = $this->filterText('category_description');
		$this->cleanCache('com_virtuemart');
		parent::save($data);
	}

	/**
	* Save the categories order
	*/
	public function saveorder()
	{
		if ($ordered = parent::saveorder()) {
			$this->cleanCache('_virtuemart');
		}
	}
	/**
	 * Handle the toggle task
	 *
	 * @author Max Milbers , Patrick Kohl
	 */

	public function toggle($field,$val=null){
		$this->cleanCache('_virtuemart');
		parent::toggle($field,$val);
		
	}

}
