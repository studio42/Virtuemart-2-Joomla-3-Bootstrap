<?php
/**
*
* Description
*
* @package	VirtueMart
* @subpackage
* @author P.Kohl
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.pdf.php 5320 2012-01-25 14:28:40Z Electrocity $
 */
defined('_JEXEC') or die;

if(!class_exists('VmView'))require(JPATH_VM_SITE.DS.'helpers'.DS.'vmview.php');

class VirtueMartViewPdf extends VmView
{

	function __construct( $config = array() ) {

		$config['base_path'] = JPATH_COMPONENT_SITE;

		parent::__construct( $config );

	}


	function display($tpl = 'pdf'){

		if(!file_exists(JPATH_VM_LIBRARIES.DS.'tcpdf'.DS.'tcpdf.php')){
			vmError('View pdf: For the pdf invoice, you must install the tcpdf library at '.JPATH_VM_LIBRARIES.DS.'tcpdf');
		} else {
			$viewName = jRequest::getWord('view','productdetails');
			$class= 'VirtueMartView'.ucfirst($viewName);
			if(!class_exists($class)) require(JPATH_VM_SITE.DS.'views'.DS.$viewName.DS.'view.html.php');
			$view = new $class ;

			$view->display($tpl);
		}

	}

}
