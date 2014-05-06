<?php
/**
 * abstract controller class containing get,store,delete,publish and pagination
 *
 *
 * This class provides the functions for the calculatoins
 *
 * @package	VirtueMart
 * @subpackage Helpers
 * @author Max Milbers
 * @copyright Copyright (c) 2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */
jimport('joomla.application.component.controller');

if (!class_exists('ShopFunctions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'shopfunctions.php');

class VmController extends JControllerLegacy{

	protected $_cidName = null;
	protected $_cname = null;
	protected $_canEdit = 0;
	protected $_vendor = null;
	/**
	 * Sets automatically the shortcut for the language and the redirect path
	 *
	 * @author Max Milbers
	 */
	public function __construct($cidName=null, $config=array()) {

		$this->_cname = strtolower(substr(get_class( $this ), 20));
		if ( !$cidName) {
			$cidName = 'virtuemart_'.$this->_cname.'_id';
		}
		$this->_cidName = $cidName;
		parent::__construct($config);

		$this->registerTask( 'add',  'edit' );
		$this->registerTask('apply','save');
		// task is filtered then save2copy is savecopy in registerTask
		$this->registerTask('savecopy','save');
		$this->registerTask('savenew','save');

		//VirtuemartController

		$this->mainLangKey = JText::_('COM_VIRTUEMART_'.strtoupper($this->_cname));
		$this->redirectPath = 'index.php?option=com_virtuemart&view='.$this->_cname;
		if (JRequest::getWord( 'tmpl') === 'component') 
			$this->redirectPath .= '&tmpl=component' ;
		if ( $this->checkVendor() ) {
			$task = explode ('.',JRequest::getCmd( 'task'));
			if ($task[0] == 'toggle') {
				$val = (isset($task[2])) ? $task[2] : NULL;
				$this->toggle($task[1],$val);
			}
		}
	}
	/*
	 * control the vendor access
	 * restrict acces to vendor only
	 * edit own and new is not checked here.
	 */
	protected function checkVendor(){
		$input = JFactory::getApplication()->input;
		$this->_vendor = Permissions::getInstance()->isSuperVendor();
		if ($this->_vendor == 1 ) return true; // can do all
		if (!$this->_vendor) { //non vendor have no access !
			$msg = JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN').' ('.JText::_('COM_VIRTUEMART_' . strtoupper($this->_cname)).')' ;
			jRequest::setVar('task','');
			$input->set('task','');
			$this->setRedirect('index.php', $msg,'error');
			return false;
		}
		if ($this->_cname === 'user') $this->_canEdit = ShopFunctions::can('editshop',$this->_cname);
		else $this->_canEdit = ShopFunctions::can('edit',$this->_cname);
		$this->_canAdd =  ShopFunctions::can('add',$this->_cname);
		// publish is for all controllers
		$this->_canPublish =  ShopFunctions::can('publish');
		$tasks = explode ('.',JRequest::getCmd( 'task','default'));
		$task = $tasks[0];

		$addTasks =array('add','edit','apply','save','save2new','save2copy');
		$canDo = true;
		if (!$this->_canEdit) {
			// toggle is checked in controller
			$taskBlacklist =array('edit','apply','save','apply','toggle','orderUp','orderDown','saveOrder');
			// only check non admin
			if (in_array($task,$taskBlacklist) ) {
				$msg = JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED').' ('.JText::_('COM_VIRTUEMART_' . strtoupper($this->_cname)).')' ;
				jRequest::setVar('task','');
				$input->set('task','');
				$this->setRedirect(null, $msg,'error');
				$canDo = false;
			}
		} elseif (!$this->_canPublish && ($task=='publish' || $task=='unpublish' || $task=='toggle') ) {
			$msg = JText::_('JLIB_APPLICATION_ERROR_PUBLISH_NOT_PERMITTED').' ('.JText::_('COM_VIRTUEMART_' . strtoupper($this->_cname)).')' ;
			jRequest::setVar('task','');
			$input->set('task','');
			$this->setRedirect(null, $msg,'error');
			$canDo = false;
		} elseif (!$this->_canAdd && $task=='add') {
			$msg = JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED').' ('.JText::_('COM_VIRTUEMART_' . strtoupper($this->_cname)).')' ;
			jRequest::setVar('task','');
			$input->set('task','');
			$this->setRedirect(null, $msg,'error');
			$canDo = false;
		} elseif ($this->_canAdd && isset($addTasks[$task])) {
			$canDo = true;
		} else {
			//$taskBlacklist =array('add','edit','apply','save');
			// verify if it's own item
			if (isset($addTasks[$task]) && !$this->checkOwn() ) {
				$msg = JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN').' ('.JText::_('COM_VIRTUEMART_' . strtoupper($this->_cname)).' '.$tasks[0].' vendor '.$this->_vendor.')' ;
				jRequest::setVar('task','');
				$input->set('task','');
				$this->setRedirect(null, $msg,'error');
				$canDo = false;
			}
		}
		return $canDo;
	}
	
	/*
	 * control the vendor access
	 * restrict acces to vendor only
	 * edit own and new is not checked here.
	 */
	protected function checkOwn($id = null){
		if ($id === null) $id = jRequest::getint($this->_cidName);
		if ($this->_vendor != 1) {
			//check if this is my own
			$model = VmModel::getModel($this->_cname);
			$own = $model->checkOwn($id);
			return $own;
		}
		return true;
	}

	/**
	* Typical view method for MVC based architecture
	*
	* This function is provide as a default implementation, in most cases
	* you will need to override it in your own controllers.
	*
	* For the virtuemart core, we removed the "Get/Create the model"
	*
	* @param   boolean  $cachable   If true, the view output will be cached
	* @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	*
	* @return  JControllerLegacy  A JControllerLegacy object to support chaining.
	* @since   11.1
	*/
	public function display($cachable = false, $urlparams = false)
	{
		$document	= JFactory::getDocument();
		$viewType	= $document->getType();
		$viewName	= JRequest::getCmd('view', $this->default_view);
		$viewLayout	= JRequest::getCmd('layout', 'default');

		$view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath));


		// Set the layout
		$view->setLayout($viewLayout);

		$view->assignRef('document', $document);

		$conf = JFactory::getConfig();

		// Display the view
		if ($cachable && $viewType != 'feed' && $conf->get('caching') >= 1) {
			$option	= JRequest::getCmd('option');
			$cache	= JFactory::getCache($option, 'view');

			if (is_array($urlparams)) {
				$app = JFactory::getApplication();

				$registeredurlparams = $app->get('registeredurlparams');

				if (empty($registeredurlparams)) {
					$registeredurlparams = new stdClass;
				}

				foreach ($urlparams as $key => $value)
				{
					// Add your safe url parameters with variable type as value {@see JFilterInput::clean()}.
					$registeredurlparams->$key = $value;
				}

				$app->set('registeredurlparams', $registeredurlparams);
			}

			$cache->get($view, 'display');

		}
		else {
			$view->display();
		}

		return $this;
	}


	/**
	 * Generic edit task
	 *
	 * @author Max Milbers
	 */
	function edit($layout='edit'){

		JRequest::setVar('layout', $layout);
		$this->display();
		JFactory::getApplication()->input->set('hidemainmenu', true);
	}

	/**
	 * Generic save task
	 *
	 * @author Max Milbers
	 * @param post $data sometimes we just want to override the data to process
	 */
	function save($data = 0){

		JSession::checkToken() or jexit( 'Invalid Token save' );
		if($data===0)$data = JRequest::get('post');
		$task = JRequest::getCmd('task');
		// remove shared when not superVendor
		if ($this->_vendor>1) {
			if (isset($data['shared'])) $data['shared']= 0;
			if (!ShopFunctions::can('publish') ) {
				$data['published']= 0;
			}
			// check vendor max uploaded images
			if (!$max_uploads = ShopFunctions::can('max_uploads') ) {
				JRequest::setVar('uploads', null, 'files');
				 jexit( 'save file error' );
			}
			// better filter in mediaManager
			// else $medias = JRequest::getVar('uploads', array(), 'files');
		}
		// save2copy is same as save, only unset the primary ID
		if ($task == 'save2copy') {
			unset($data[$this->_cidName]);
			$data['published'] = 0;
		}

		$model = VmModel::getModel($this->_cname);
		$id = $model->store($data);

		$errors = $model->getErrors();
		if(empty($errors)) {
			$msg = JText::sprintf('COM_VIRTUEMART_STRING_SAVED',$this->mainLangKey);
			$type = 'message';
		}
		else $type = 'error';
		foreach($errors as $error){
			$msg = ($error).'<br />';
		}

		$redir = $this->redirectPath;
		if($task == 'apply' || $task == 'save2copy'){
			$redir .= '&task=edit&'.$this->_cidName.'='.$id;
		} else if ($task == 'save2new') {
			$redir .= '&task=add';
		}
		if ($task == 'apply') {
			$app = JFactory::getApplication();
			$lastTab = $app->input->get('lastTab', '','cmd');
			$app->setUserState( "com_virtuemart.lasttab", $lastTab );
		}
		//else $this->display();

		$this->setRedirect($redir, $msg,$type);
	}

	/**
	 * Generic remove task
	 *
	 * @author Max Milbers
	 */
	function remove(){

		JSession::checkToken() or jexit( 'Invalid Token remove' );

		$ids = JRequest::getVar($this->_cidName, JRequest::getVar('cid',array(),'', 'ARRAY'), '', 'ARRAY');
		jimport( 'joomla.utilities.arrayhelper' );
		JArrayHelper::toInteger($ids);

		if(count($ids) < 1) {
			$msg = JText::_('COM_VIRTUEMART_SELECT_ITEM_TO_DELETE');
			$type = 'notice';
		} else {
			$model = VmModel::getModel($this->_cname);
			$ret = $model->remove($ids,$this->_vendor);
			$errors = $model->getErrors();
			$msg = JText::sprintf('COM_VIRTUEMART_STRING_DELETED',$this->mainLangKey);
			if(!empty($errors) or $ret==false) {
				$msg = JText::sprintf('COM_VIRTUEMART_STRING_COULD_NOT_BE_DELETED',$this->mainLangKey);
						$type = 'error';
			}
			else $type = 'remove';
			foreach($errors as $error){
				$msg .= '<br />'.($error);
			}
		}

		$this->setRedirect(null, $msg,$type);

	}

	/**
	 * Generic cancel task
	 *
	 * @author Max Milbers
	 */
	public function cancel(){
		$msg = JText::sprintf('COM_VIRTUEMART_STRING_CANCELLED',$this->mainLangKey); //'COM_VIRTUEMART_OPERATION_CANCELED'
		$this->setRedirect(null, $msg,'notice');
	}

	/**
	 * Handle the toggle task
	 *
	 * @author Max Milbers , Patrick Kohl
	 */

	public function toggle($field,$val=null){

		JSession::checkToken() or jexit( 'Invalid Token' );

		$model = VmModel::getModel($this->_cname);
		if (!$model->toggle($field,$val,$this->_cidName)) {
			$msg = JText::sprintf('COM_VIRTUEMART_STRING_TOGGLE_ERROR',$this->mainLangKey);
		} else{
			$msg = JText::sprintf('COM_VIRTUEMART_STRING_TOGGLE_SUCCESS',$this->mainLangKey);
		}

		$this->setRedirect( null, $msg);
	}

	/**
	 * Handle the publish task
	 *
	 * @author Jseros, Max Milbers
	 */
	public function publish($cidname=0,$table=0,$redirect = null){

		JSession::checkToken() or jexit( 'Invalid Token' );

		$model = VmModel::getModel($this->_cname);

		if($cidname === 0) $cidname = $this->_cidName;

		if (!$model->toggle('published', 1, $cidname, $table)) {
			$msg = JText::sprintf('COM_VIRTUEMART_STRING_PUBLISHED_ERROR',$this->mainLangKey);
		} else{
			$msg = JText::sprintf('COM_VIRTUEMART_STRING_PUBLISHED_SUCCESS',$this->mainLangKey);
		}
		$this->setRedirect( $redirect , $msg);
	}


	/**
	 * Handle the publish task
	 *
	 * @author Max Milbers, Jseros
	 */
	function unpublish($cidname=0,$table=0,$redirect = null){

		JSession::checkToken() or jexit( 'Invalid Token' );

		$model = VmModel::getModel($this->_cname);

		if($cidname === 0) $cidname = $this->_cidName;

		if (!$model->toggle('published', 0, $cidname, $table)) {
			$msg = JText::sprintf('COM_VIRTUEMART_STRING_UNPUBLISHED_ERROR',$this->mainLangKey);
		} else{
			$msg = JText::sprintf('COM_VIRTUEMART_STRING_UNPUBLISHED_SUCCESS',$this->mainLangKey);
		}
		$this->setRedirect( $redirect, $msg);
	}

	function orderup() {

		JSession::checkToken() or jexit( 'Invalid Token' );

		$model = VmModel::getModel($this->_cname);
		$model->move(-1);
		$msg = JText::sprintf('COM_VIRTUEMART_STRING_ORDER_UP_SUCCESS',$this->mainLangKey);
		$this->setRedirect( null, $msg);
	}

	function orderdown() {

		JSession::checkToken() or jexit( 'Invalid Token' );

		$model = VmModel::getModel($this->_cname);
		$model->move(1);
		$msg = JText::sprintf('COM_VIRTUEMART_STRING_ORDER_DOWN_SUCCESS',$this->mainLangKey);
		$this->setRedirect( null, $msg);
	}

	function saveorder() {

		JSession::checkToken() or jexit( 'Invalid Token' );

		$cid 	= JRequest::getVar( $this->_cidName, JRequest::getVar('cid',array(0)), 'post', 'array' );
		$order 	= JRequest::getVar( 'order', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);

		$model = VmModel::getModel($this->_cname);
		$ordered = $model->saveorder($cid, $order);
		
		// if (!$ordered ) $msg = JText::sprintf ('COM_VIRTUEMART_ITEMS_MOVED', $ordered);
		if (!$ordered ) $msg = JText::_ ('COM_VIRTUEMART_ITEMS_NOT_MOVED');
		else $msg = JText::sprintf ('COM_VIRTUEMART_ITEMS_MOVED', $ordered);
		$this->setRedirect( null, $msg);
		return $ordered;
	}

	/**
	 * This function just overwrites the standard joomla function, using our standard class VmModel
	 * for this
	 * @see JControllerLegacy::getModel()
	 */
	function getModel($name = '', $prefix = '', $config = array()){
		if(!class_exists('ShopFunctions'))require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'shopfunctions.php');

		if(empty($name)) $name = false;
		return VmModel::getModel($name);
	}
	/**
	 * Set a URL for browser redirection.
	 * Use type 'error' if the message is an error message
	 * @param   string  $url   URL to redirect to.
	 * @param   string  $msg   Message to display on redirect. Optional, defaults to value set internally by controller, if any.
	 * @param   string  $type  Message type. Optional, defaults to 'message' or the type set by a previous call to setMessage.
	 *
	 * @return  JController  This object to support chaining.
	 *
	 * @since   11.1
	 */
	public function setRedirect($url = null, $msg = null, $type = null)
	{
		if ($url === null) $url = $this->redirectPath;
		$format = JRequest::getWord('format');
		if ($format !== 'json') {
			// add menu item id in front
			if (JFactory::getApplication()->isSite()) $url = jRoute::_($url, false);
			return parent::setRedirect($url, $msg , $type );
		}
		if ($msg !== null)
		{
			// Controller may have set this directly
			$this->json->message = $msg;
		}

		// Ensure the type is not overwritten by a previous call to setMessage.
		if (empty($type))
		{
			if (empty($this->messageType))
			{
				$this->messageType = 'message';
			}
		}
		// If the type is explicitly set, set it.
		else
		{
			$this->messageType = $type;
		}
		if ($this->messageType == 'message') $this->json->type = 'alert-info';
		else if ($this->messageType == 'error') $this->json->type = 'alert-error';
		else $this->json->type = 'alert-'.$this->messageType;
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
		header("Cache-Control: no-cache, must-revalidate"); 
		header("Pragma: no-cache");
		header("Content-type: application/json;; charset=utf-8");
		echo json_encode($this->json);
		jexit();
	}
	/**
	 * Clean the cache
	 *
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 *
	 * @return  void
	 *
	 * @since   12.2
	 * NOTE studio42 category tree cache is $group '_virtuemart'
	 */
	protected function cleanCache($group = null)
	{
		$conf = JFactory::getConfig();
		$options = array(
			'defaultgroup' => ($group) ? $group : (isset($this->option) ? $this->option : JFactory::getApplication()->input->get('option')),
			'cachebase' => $conf->get('cache_path', JPATH_SITE . '/cache')) ;
		$cache = JCache::getInstance('', $options);
		$cache->clean($group);

		// Trigger the onContentCleanCache event.
		// $dispatcher->trigger($this->event_clean_cache, $options);
	}
}