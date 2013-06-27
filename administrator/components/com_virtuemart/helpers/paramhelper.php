<?php

/**
*
* Helper class to handle additional parameters
*
* @package	VirtueMart
* @subpackage Helpers
* @author Oscar van Eijk
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: paramhelper.php 2928 2011-03-31 16:53:36Z oscar $
*/
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Parameter helper class for datase fields in the format "key=value[(s)key=value(s)key=value...]
 * where (s) is a seperator (default: '\n').
 *
 * @package	VirtueMart
 * @subpackage Helper
 * @author Oscar van Eijk
 */
class ParamHelper {

	/** @var array list with parameter=>value */
	private $_data;

	/** @var char seperator */
	private $_sep;

	public function __construct($p = null, $s = '\n')
	{
		// initialise
		$this->_sep = $s;
		if ($p === null) {
			$this->_data = array();
		} else {
			$this->parseParam($p);
		}
	}

	/**
	 * Set the field seperator
	 * @param char $s
	 */
	public function setSeper($s)
	{
		$this->_sep = $s;
	}

	/**
	 * Return a single parameter value
	 * @param $p parameter name
	 * @param $d default value
	 * @return mixed parameter value or default value if non existing
	 */
	public function get($p, $d = null)
	{
		if (array_key_exists($p, $this->_data)) {
			return $this->_data[$p];
		} else {
			return $d;
		}
	}

	/**
	 * Return a single parameter value
	 * @param $p parameter name
	 * @param $v value
	 */
	public function set($p, $v = null)
	{
		$this->_data[$p] = $v;
	}

	/**
	 * Parse a parameter string and fill the _data array with key/value pairs
	 * @param string $p
	 */
	public function parseParam  ($p)
	{
		if (!$p) {
			return;
		}
		$_arr = explode($this->_sep, $p);
		if (count($_arr) == 0) {
			$this->_data = array();
			return;
		}
		foreach ($_arr as $_p) {
			$_p = trim($_p);
			list($k, $v) = explode('=', $_p, 2);
			$this->_data[$k] = $v;
		}
	}

	/**
	 * Format the _data array for database storage
	 * @return string or null when no parameters exist
	 */
	public function paramString()
	{
		$s = array();
		foreach ($this->_data as $k => $v) {
			$s[]=$k.'='.$v;
		}
		return (count($s) == 0 ) ? null : implode($this->_sep, $s);
	}
}

// No closing tag
