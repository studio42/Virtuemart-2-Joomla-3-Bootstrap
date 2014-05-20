<?php
if( !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
*
* @version $Id: virtuemart.php 6558 2012-10-18 08:50:00Z alatak $
* @package VirtueMart
* @subpackage core
* @author Max Milbers
* @copyright Copyright (C) 2009-11 by the authors of the VirtueMart Team listed at /administrator/com_virtuemart/copyright.php - All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/

/* Require the config */

//Console::logSpeed('virtuemart start');

if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR .'/components/com_virtuemart/helpers/config.php');
VmConfig::loadConfig();
JLoader::register('Permissions', JPATH_VM_ADMINISTRATOR.'/helpers/permissions.php');

$isAdmin = Permissions::getInstance()->check("admin,storeadmin");
$offline = VmConfig::get('shop_is_offline',0);
vmRam('Start');
vmSetStartTime('Start');

VmConfig::loadJLang('com_virtuemart', true);
$input = JFactory::getApplication()->input;
$basePath = JPATH_VM_SITE;

if($offline && !$isAdmin){
	$_controller = 'virtuemart';
	require (JPATH_VM_SITE.'/controllers/virtuemart.php');
	JRequest::setVar('view', 'virtuemart');
	$task='';

} else {

	/* Front-end helpers */
	JLoader::register('VmImage', JPATH_VM_ADMINISTRATOR.'/helpers/image.php'); //dont remove that file it is actually in every view except the state view
	JLoader::register('shopFunctionsF', JPATH_VM_SITE.'/helpers/shopfunctionsf.php'); //dont remove that file it is actually in every view

	/* Loading jQuery and VM scripts. */
	//vmJsApi::jPrice();    //in create button
	vmJsApi::jQuery();
	vmJsApi::jSite();
	vmJsApi::cssSite();
	$_controller = $input->get( 'controller' , 'virtuemart' , 'word');
	$_controller = $input->get( 'view' , $_controller , 'word');
	$_format = $input->get( 'format' , null);
	$trigger = 'onVmSiteController';
	// for now admin view is virtuemart view
	// var_dump($_controller); jexit();
	if ($_controller === 'admin') {
		$_controller = 'virtuemart';
		$input->set( 'view' , $_controller);
		$input->set( 'tmpl' , 'component');
	}
	if ($_format === 'pdf') {
		$input->set( 'tmpl' , 'component');
	} elseif ($input->get('tmpl') == 'component' && file_exists(JPATH_VM_ADMINISTRATOR.'/controllers/'.$_controller.'.php')) {
		// trouble with manufacturer displayed in modal (perhaps changing controller name ?)
		$manufacturer = ($input->get( 'virtuemart_manufacturer_id' , null , 'int') && $_controller == 'manufacturer' && !$input->get( 'task' , null , 'word'));
		// vendor check is in vmcontroller Back-end to secure front and backen acces same way
		//check("admin,storeadmin");

		if ($isVendor = Permissions::getInstance()->isSuperVendor() && !$manufacturer){
			$jlang =JFactory::getLanguage();
			$jlang->load('com_virtuemart', JPATH_ADMINISTRATOR, null, true);
			$jlang->load('', JPATH_ADMINISTRATOR, null, true);
			$basePath = JPATH_VM_ADMINISTRATOR;
		} else {
			$input->set( 'view' , 'user');
			$input->set( 'tmpl' , null);
			$input->set( 'task' , null);
			$_controller = 'user';
			$input->set( 'layout' , 'login');
		}

	}

}

/* Create the controller name */
$_class = 'VirtuemartController'.ucfirst($_controller);

if (file_exists($basePath.'/controllers/'.$_controller.'.php')) {
	if (!class_exists($_class)) {
		require ($basePath.'/controllers/'.$_controller.'.php');
	}
}
else {
	// try plugins
	JPluginHelper::importPlugin('vmextended');
	$dispatcher = JDispatcher::getInstance();
	$dispatcher->trigger($trigger, array($_controller));
}


if (class_exists($_class)) {
    $controller = new $_class();
	if ($basePath === JPATH_VM_ADMINISTRATOR) $controller->addViewPath(JPATH_VM_ADMINISTRATOR . '/views'); 
	// try plugins
	JPluginHelper::importPlugin('vmuserfield');
	$dispatcher = JDispatcher::getInstance();
	$dispatcher->trigger('plgVmOnMainController', array($_controller));
	// set task here that plugin or controller can change task, if we have permission control for eg.
	$task = $input->get( 'task' , 'display' , 'word');
    /* Perform the Request task */
    $controller->execute($task);

    //Console::logSpeed('virtuemart start');
    vmTime($_class.' Finished task '.$task,'Start');
    vmRam('End');
    vmRamPeak('Peak');
    /* Redirect if set by the controller */
    $controller->redirect();
} else {
    vmDebug('VirtueMart controller not found: '. $_class);
    $app = Jfactory::getApplication();
    $app->redirect(JRoute::_ ('index.php?option=com_virtuemart&view=virtuemart', FALSE));
}
