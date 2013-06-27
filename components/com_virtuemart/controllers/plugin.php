<?php defined ('_JEXEC') or die('Restricted access');
/**
 *
 * plugin controller
 *
 * @package    VirtueMart
 * @subpackage Core
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: plugin.php 2641 2010-11-09 19:25:13Z milbo $
 */

jimport ('joomla.application.component.controller');

/**
 * VirtueMart default administrator controller
 *
 * @package        VirtueMart
 */
class VirtuemartControllerPlugin extends JController {

	/**
	 * Method to render the plugin datas
	 * this is an entry point to plugin to easy renders json or html
	 *
	 *
	 * @access    public
	 */
	function display($cachable = false, $urlparams = false)  {

		if (!$type = JRequest::getWord ('vmtype', NULL)) {
			$type = JRequest::getWord ('type', 'vmcustom');
		}
		$typeWhiteList = array('vmcustom', 'vmcalculation', 'vmuserfield', 'vmpayment', 'vmshipment');
		if (!in_array ($type, $typeWhiteList)) {
			return FALSE;
		}

// 		if(!$name = JRequest::getCmd('name', null) ) return $name;

		$name = JRequest::getCmd ('name', 'none');

		$nameBlackList = array('plgVmValidateCouponCode', 'plgVmRemoveCoupon', 'none');
		if (in_array ($name, $nameBlackList)) {
			echo 'You got logged';
			return FALSE;
		}

		JPluginHelper::importPlugin ($type, $name);
		$dispatcher = JDispatcher::getInstance ();
		// if you want only one render simple in the plugin use jExit();
		// or $render is an array of code to echo as html or json Objects!
		$render = NULL;
		$dispatcher->trigger ('plgVmOnSelfCallFE', array($type, $name, &$render));
		if ($render) {
			// Get the document object.
			$document = JFactory::getDocument ();
			if (JRequest::getWord ('cache') == 'no') {
				JResponse::setHeader ('Cache-Control', 'no-cache, must-revalidate');
				JResponse::setHeader ('Expires', 'Mon, 6 Jul 2000 10:00:00 GMT');
			}
			$format = JRequest::getWord ('format', 'json');
			if ($format == 'json') {
				$document->setMimeEncoding ('application/json');
				// Change the suggested filename.

				JResponse::setHeader ('Content-Disposition', 'attachment;filename="' . $type . '".json"');
				echo json_encode ($render);
			}
			else {
				echo $render;
			}
		}
	}
}
