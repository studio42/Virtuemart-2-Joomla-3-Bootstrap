<?php
/**
*
* Currency table
*
* @package	VirtueMart
* @subpackage Currency
* @author RickG
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: currencies.php 6347 2012-08-14 15:49:02Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!class_exists('VmTableData'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'vmtabledata.php');

/**
 * Currency table class
 * The class is is used to manage the currencies in the shop.
 *
 * @package		VirtueMart
 * @author RickG, Max Milbers
 */
class TableCurrencies extends VmTableData {

	/** @var int Primary key */
	var $virtuemart_currency_id				= 0;
	/** @var int vendor id */
	var $virtuemart_vendor_id					= 1;
	/** @var string Currency name*/

	var $currency_name 			= '';
	var $currency_code_2		= '';
	var $currency_code_3		= '';
	var $currency_numeric_code	= 0;
	var $currency_exchange_rate = 0.0;
	var $currency_symbol		= '';
	var $currency_decimal_place		= 0;
	var $currency_decimal_symbol		= '';
	var $currency_thousands		= '';
	var $currency_positive_style	= '';
	var $currency_negative_style	= '';
	var $shared					= 0;
	var $published				= 0;

	/**
	 * @author Max Milbers
	 * @param $db A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__virtuemart_currencies', 'virtuemart_currency_id', $db);

		$this->setUniqueName('currency_name');

		$this->setLoggable();

		$this->setOrderable();
	}

	function check(){

		//$this->checkCurrencySymbol();
		return parent::check();
	}

	/**
	 * ATM Unused !
	 * Checks a currency symbol wether it is a HTML entity.
	 * When not and $convertToEntity is true, it converts the symbol
	 * Seems not be used      ATTENTION   seems BROKEN, working only for euro, ...
	 *
	 */
	function checkCurrencySymbol($convertToEntity=true ) {

		$symbol = str_replace('&amp;', '&', $this->currency_symbol );

		if( substr( $symbol, 0, 1) == '&' && substr( $symbol, strlen($symbol)-1, 1 ) == ';') {
			return $symbol;
		}
		else {
			if( $convertToEntity ) {
				$symbol = htmlentities( $symbol, ENT_QUOTES, 'utf-8' );

				if( substr( $symbol, 0, 1) == '&' && substr( $symbol, strlen($symbol)-1, 1 ) == ';') {
					return $symbol;
				}
				// Sometimes htmlentities() doesn't return a valid HTML Entity
				switch( ord( $symbol ) ) {
					case 128:
					case 63:
						$symbol = '&euro;';
						break;
				}

			}
		}

		 $this->currency_symbol = $symbol;
	}
}
// pure php no closing tag
