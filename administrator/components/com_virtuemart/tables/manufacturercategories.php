<?php
/**
*
* Manufacturer Category table
*
* @package	VirtueMart
* @subpackage Manufacturer category
* @author Patrick Kohl
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: manufacturercategories.php 4731 2011-11-17 01:35:45Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTable'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtable.php');

/**
 * Manufacturer category table class
 * The class is used to manage the manufacturer category in the shop.
 *
 * @package		VirtueMart
 * @author Patrick Kohl
 */
class TableManufacturercategories extends VmTable {

	/** @var int Primary key */
	var $virtuemart_manufacturercategories_id = 0;
	/** @var string manufacturer category name */
	var $mf_category_name = '';
	/** @var string manufacturer category description */
	var $mf_category_desc = '';
	/** @var int published or unpublished */
	var $published = 1;

	/**
	 * @author Max Milbers
	 * @param $db A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__virtuemart_manufacturercategories', 'virtuemart_manufacturercategories_id', $db);

		$this->setUniqueName('mf_category_name');

		$this->setLoggable();
		$this->setTranslatable(array('mf_category_name','mf_category_desc'));
		$this->setSlug('mf_category_name');
	}


	/*
	 * Verify that user have to delete all manufacturers of a particular category before that category can be removed
	 *
	 * @return boolean True if category is ready to be removed, otherwise False
	 */
	function checkManufacturer($categoryId = 0)
	{
		if($categoryId > 0) {
			$db = JFactory::getDBO();

			$q = 'SELECT count(*)'
				.' FROM #__virtuemart_manufacturers'
				.' WHERE virtuemart_manufacturercategories_id = '.$categoryId;
			$db->setQuery($q);
			$mCount = $db->loadResult();

			if($mCount > 0) {
				vmInfo('COM_VIRTUEMART_REMOVE_IN_USE');
				return false;
			}

		}
		return true;
	}

}
// pure php no closing tag
