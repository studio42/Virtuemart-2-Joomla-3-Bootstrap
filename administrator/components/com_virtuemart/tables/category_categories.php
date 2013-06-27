<?php
/**
*
* category_categories table ( to map calc rules to shoppergroups)
*
* @package	VirtueMart
* @subpackage nested categories
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2011 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: category_categories.php 3002 2011-04-08 12:35:45Z alatak $
*/

defined('_JEXEC') or die();

if(!class_exists('VmTableData'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtabledata.php');

class TableCategory_categories extends VmTableData {

	var $category_parent_id = 0;

	/**
	 * @author Max Milbers
	 * @param $db A database connector object
	 */
	function __construct(&$db){
		parent::__construct('#__virtuemart_category_categories', 'id', $db);

		$this->setPrimaryKey('category_child_id');

		$this->setOrderable();
		$this->setTableShortCut('cx');
	}

}