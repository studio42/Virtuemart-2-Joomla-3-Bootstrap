<?php
/**
*
* State View
*
* @package	VirtueMart
* @subpackage State
* @author RickG, RolandD
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.json.php 6043 2012-05-21 21:40:56Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
jimport( 'joomla.application.component.view');

/**
 * HTML View class for maintaining the state
 *
 * @package	VirtueMart
 * @subpackage State
 * @author RolandD, jseros
 */
class VirtuemartViewState extends JView {

	function display($tpl = null) {

		$states = array();
		$db = JFactory::getDBO();
		//retrieving countries id
		$country_ids = JRequest::getString('virtuemart_country_id');
		$country_ids = explode(',', $country_ids);
		
		foreach($country_ids as $country_id){
			$q= 'SELECT `virtuemart_state_id`, `state_name` FROM `#__virtuemart_states`  WHERE `virtuemart_country_id`= "'.(int)$country_id.'" 
				ORDER BY `#__virtuemart_states`.`state_name`';
			$db->setQuery($q);
			
			$states[$country_id] = $db->loadAssocList();
		}
		
		echo json_encode($states);
	}
}
// pure php no closing tag
