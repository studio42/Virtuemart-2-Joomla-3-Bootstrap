<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author  Patrick Kohl
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 3006 2011-04-08 13:16:08Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
jimport( 'joomla.application.component.view');

/**
 * Json View class for the VirtueMart Component
 *
 * @package		VirtueMart
 * @author  Patrick Kohl
 */
class VirtuemartViewMedia extends JView {

	/* json object */
	private $json = null;

	function display($tpl = null) {
		$document =JFactory::getDocument();
		$document->setMimeEncoding( 'application/json' );

		if ($virtuemart_media_id = JRequest::getInt('virtuemart_media_id')) {
			//JResponse::setHeader( 'Content-Disposition', 'attachment; filename="media'.$virtuemart_media_id.'.json"' );

			$model = VmModel::getModel('Media');
			$image = $model->createMediaByIds($virtuemart_media_id);
// 			echo '<pre>'.print_r($image,1).'</pre>';
			$this->json = $image[0];
			//echo json_encode($this->json);
			if (isset($this->json->file_url)) {
				$this->json->file_root = JURI::root(true).'/';
				$this->json->msg =  'OK';
				echo @json_encode($this->json);
			} else {
				$this->json->msg =  '<b>'.JText::_('COM_VIRTUEMART_NO_IMAGE_SET').'</b>';
				echo @json_encode($this->json);
			}
		}
		else {
			$this->loadHelper('mediahandler');
			$start = JRequest::getInt('start',0);

			$type = JRequest::getWord('mediatype',0);
			$list = VmMediaHandler::displayImages($type,$start );
			echo @json_encode($list);
		}

		jExit();
	}


}
// pure php no closing tag
