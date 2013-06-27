<?php
/**
*
* Manufacturer View
*
* @package	VirtueMart
* @subpackage Manufacturer
* @author Kohl Patrick
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 2641 2010-11-09 19:25:13Z milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView'))require(JPATH_VM_SITE.DS.'helpers'.DS.'vmview.php');

/**
 * HTML View class for maintaining the list of manufacturers
 *
 * @package	VirtueMart
 * @subpackage Manufacturer
 * @author Kohl Patrick
 */
class VirtuemartViewManufacturer extends VmView {

	function display($tpl = null) {

		$document = JFactory::getDocument();
		$mainframe = JFactory::getApplication();
		$pathway = $mainframe->getPathway();
		/* Set the helper */
		$this->addHelperPath(JPATH_VM_ADMINISTRATOR.DS.'helpers');
		$this->loadHelper('image');

		$virtuemart_manufacturer_id = JRequest::getInt('virtuemart_manufacturer_id', 0);
		$mf_category_id = JRequest::getInt('mf_category_id', 0);

		// get necessary models
		$model = VmModel::getModel('manufacturer');
		if ($virtuemart_manufacturer_id !=0 ) {

			$manufacturer = $model->getManufacturer();
			$model->addImages($manufacturer,1);

			$manufacturerImage = $manufacturer->images[0]->displayMediaThumb('class="manufacturer-image"',false);

			$document->setTitle(JText::_('COM_VIRTUEMART_MANUFACTURER_DETAILS').' '.strip_tags($manufacturer->mf_name));

			$this->assignRef('manufacturerImage', $manufacturerImage);
// 			$this->assignRef('manufacturerProductsURL', $manufacturerProductsURL);
			$this->assignRef('manufacturer',	$manufacturer);
			$pathway->addItem(strip_tags($manufacturer->mf_name));

			$this->setLayout('details');
// 			vmdebug('$manufacturer',$manufacturer);
		} else {
			$document->setTitle(JText::_('COM_VIRTUEMART_MANUFACTURER_PAGE')) ;
			$manufacturers = $model->getManufacturers(true, true,  true);
			$model->addImages($manufacturers,1);
			$this->assignRef('manufacturers',	$manufacturers);
			$this->setLayout('default');
		}

		parent::display($tpl);
	}

}
// pure php no closing tag
