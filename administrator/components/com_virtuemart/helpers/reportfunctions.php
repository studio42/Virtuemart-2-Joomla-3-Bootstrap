<?php
/**
 * Report helper class
 *
 * This class provides some functions that are used by reports in VirtueMart shop.
 *
 * @package	VirtueMart
 * @subpackage Helpers
 * @author Wicksj
 * @copyright Copyright (c) 2004-2008 Soeren Eberhardt-Biermann, 2009 VirtueMart Team. All rights reserved.
 */

class ReportFunctions {

	/**
	 * @var global database object
	 */
	private $_db = null;


	/**
	 * Contructor
	 */
	public function __construct(){

		$this->_db = JFactory::getDBO();
	}



}

//pure php no tag