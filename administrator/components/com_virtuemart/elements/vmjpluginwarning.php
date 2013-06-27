<?php
defined('_JEXEC') or die();
/**
 *
 * @package	VirtueMart
 * @subpackage Plugins  - Elements
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: $
 */
/*
 * This class is used by VirtueMart Payment or Shipment Plugins
 * which uses JParameter
 * So It should be an extension of JElement
 * Those plugins cannot be configured througth the Plugin Manager anyway.
 */
class JElementVmjpluginwarning extends JElement {

    /**
     * Element name
     * @access	protected
     * @var		string
     */
    var $_name = 'vmjpluginwarning';

    function fetchElement($name, $value, &$node, $control_name) {
	    $lang = JFactory::getLanguage();
	    $lang->load('com_virtuemart',JPATH_ADMINISTRATOR);

	$option = JRequest::getWord('option');
	if ($option == 'com_virtuemart')
	    return null;
	else
	    return JText::_('COM_VIRTUEMART_PLUGIN_WARNING');
    }

}