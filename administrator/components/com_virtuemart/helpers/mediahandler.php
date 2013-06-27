<?php
/**
 * Media file handler class
 *
 * This class provides some file handling functions that are used throughout the VirtueMart shop.
 *  Uploading, moving, deleting
 *
 * @package	VirtueMart
 * @subpackage Helpers
 * @author Max Milbers
 * @copyright Copyright (c) 2011 VirtueMart Team. All rights reserved by the author.
 */

defined('_JEXEC') or die();

/**
 * Sanitizes the filenames and transliterates them also for non latin languages
 *
 * @author constantined
 *
 */
class vmFile {

	/**
	 * This function does not allow unicode
	 * @param      $string
	 * @param bool $forceNoUni
	 * @return mixed|string
	 */
	function makeSafe($string,$forceNoUni=false) {

		$string = trim(JString::strtolower($string));

		// Delete all '?'
		$str = str_replace('?', '', $string);

		// Replace double byte whitespaces by single byte (East Asian languages)
		$str = preg_replace('/\xE3\x80\x80/', ' ', $str);
		$str = str_replace(' ', '-', $str);

		$lang = JFactory::getLanguage();
		$str = $lang->transliterate($str);

		if(function_exists('mb_ereg_replace')){
			$regex = array('#(\.){2,}#', '#[^\w\.\- ]#', '#^\.#');
			return mb_ereg_replace($regex, '', $str);
		} else {
			$regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#');
			return preg_replace($regex, '', $str);
		}

	}
}

class VmMediaHandler {

	var $media_attributes = 0;
	var $setRole = false;
	var $file_name = '';
	var $file_extension = '';
	var $virtuemart_media_id = '';


	function __construct($id=0){

		$this->virtuemart_media_id = $id;

		$this->theme_url = VmConfig::get('vm_themeurl',0);
		if(empty($this->theme_url)){
			$this->theme_url = JURI::root().'components/com_virtuemart/';
		}
	}

	/**
	 * The type of the media determines the used path for storing them
	 *
	 * @author Max Milbers
	 * @param string $type type of the media, allowed values product, category, shop, vendor, manufacturer, forSale
	 */
	public function getMediaUrlByView($type){

		//the problem is here, that we use for autocreatoin the name of the model, here products
		//But for storing we use the product to build automatically the table out of it (product_medias)
		$choosed = false;
		if($type == 'product' || $type == 'products'){
			$relUrl = VmConfig::get('media_product_path');
			$choosed = true;
		}
		else if($type == 'category' || $type == 'categories'){
			$relUrl = VmConfig::get('media_category_path');
			$choosed = true;
		}
		else if($type == 'shop'){
			$relUrl = VmConfig::get('media_path');
			$choosed = true;
		}
		else if($type == 'vendor' || $type == 'vendors'){
			$relUrl = VmConfig::get('media_vendor_path');
			//	$relUrl = 'components/com_virtuemart/assets/images/vendors/';
			$choosed = true;
		}
		else if($type == 'manufacturer' || $type == 'manufacturers'){
			$relUrl = VmConfig::get('media_manufacturer_path');
			$choosed = true;
		}
		else if($type == 'forSale' || $type== 'file_is_forSale'){

			$relUrl = shopFunctions::checkSafePath();
			if($relUrl){
				$choosed = true;
				$this->file_is_forSale=1;
			}

		}

		// 		$this->type = $type;
		// 		$this->setRole=false;
		if($choosed && empty($relUrl)){
			$uri = JFactory::getURI();
			$link = $uri->root() . 'administrator/index.php?option=com_virtuemart&view=config';
			vmInfo('COM_VIRTUEMART_MEDIA_NO_PATH_TYPE',$type,$link );
			//Todo add general media_path to config
			//$relUrl = VmConfig::get('media_path');
			$relUrl = 'images/stories/virtuemart/';
			$this->setRole=true;
			// 		} else if(!$choosed and empty($relUrl) and $this->file_is_forSale==0){
		} else if(!$choosed and empty($relUrl) ){

			vmWarn('COM_VIRTUEMART_MEDIA_CHOOSE_TYPE',$this->file_title );
			// 			vmError('Ignore this message, when it appears while the media synchronisation process, else report to http://forum.virtuemart.net/index.php?board=127.0 : cant create media of unknown type, a programmers error, used type ',$type);
			//$relUrl = VmConfig::get('media_path');
			$relUrl = 'images/stories/virtuemart/typeless/';
			$this->setRole=true;

		} else if(!$choosed and $this->file_is_forSale==1){
			$relUrl = '';
			$this->setRole=false;
		}

		return $relUrl;
	}

	/**
	 * This function determines the type of a media and creates it.
	 * When you want to write a child class of the mediahandler, you need to manipulate this function.
	 * We may use later here a hook for plugins or simular
	 *
	 * @author Max Milbers
	 * @param object $table
	 * @param string  $type vendor,product,category,...
	 * @param string $file_mimetype such as image/jpeg
	 */
	static public function createMedia($table,$type='',$file_mimetype=''){

		if(!class_exists('JFile')) require(JPATH_VM_LIBRARIES.DS.'joomla'.DS.'filesystem'.DS.'file.php');

		$extension = strtolower(JFile::getExt($table->file_url));

		$isImage = self::isImage($extension);

		if($isImage){
			if (!class_exists('VmImage')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'image.php');
			$media = new VmImage();
		} else {
			$media = new VmMediaHandler();
		}

		$attribsImage = get_object_vars($table);

		foreach($attribsImage as $k=>$v){
			$media->$k = $v;
		}

		if(empty($type)){
			$type = $media->file_type;
		} else {
			$media->file_type = $type;
		}

		$media->setFileInfo($type);

		return $media;
	}

	/**
	 * This prepares the object for storing the data. This means it does the action
	 * and returns the data for storing in the table
	 *
	 * @author Max Milbers
	 * @param object $table
	 * @param array $data
	 * @param string $type
	 */
	public function prepareStoreMedia($table,$data,$type){

		$media = VmMediaHandler::createMedia($table,$type);

		$data = $media->processAttributes($data);
		$data = $media->processAction($data);

		$attribsImage = get_object_vars($media);
		foreach($attribsImage as $k=>$v){
			$data[$k] = $v;
		}

		return $data;
	}

	/**
	 * Sets the file information and paths/urls and so on.
	 *
	 * @author Max Milbers
	 * @param unknown_type $filename
	 * @param unknown_type $url
	 * @param unknown_type $path
	 */
	function setFileInfo($type=0){


		$this->file_url_folder = '';
		$this->file_path_folder = '';
		$this->file_url_folder_thumb = '';

		if($this->file_is_forSale==0 and $type!='forSale'){

			$this->file_url_folder = $this->getMediaUrlByView($type);
			$this->file_url_folder_thumb = $this->file_url_folder.'resized/';
			$this->file_path_folder = str_replace('/',DS,$this->file_url_folder);
		} else {
			$safePath = shopFunctions::checkSafePath();
			if(!$safePath){
				return FALSE;
			}
			$this->file_path_folder = $safePath;
			$this->file_url_folder = $this->file_path_folder;//str_replace(DS,'/',$this->file_path_folder);
			$this->file_url_folder_thumb = VmConfig::get('forSale_path_thumb');
		}

		//Clean from possible injection
		while(strpos($this->file_path_folder,'..')!==false){
			$this->file_path_folder  = str_replace('..', '', $this->file_path_folder);
		};
		$this->file_path_folder  = preg_replace('#[/\\\\]+#', DS, $this->file_path_folder);

		if(empty($this->file_url)){
			$this->file_url = $this->file_url_folder;
			$this->file_name = '';
			$this->file_extension = '';
		} else {
			if(!class_exists('JFile')) require(JPATH_VM_LIBRARIES.DS.'joomla'.DS.'filesystem'.DS.'file.php');

			if($this->file_is_forSale==1){

				$rdspos = strrpos($this->file_url,DS);
				if($rdspos!==false){
					$name = substr($this->file_url,$rdspos+1);
				}
				//vmdebug('$name',$this->file_url,$rdspos,$name);
			} else {
				//This construction is only valid for the images, it is for own structuring using folders
				$name = str_replace($this->file_url_folder,'',$this->file_url);
			}


			if(!empty($name) && $name !=='/'){
				$this->file_name = JFile::stripExt($name);
				$this->file_extension = strtolower(JFile::getExt($name));

				//Ensure using right directory
				$file_url = $this->getMediaUrlByView($type).$name;

				if($this->file_is_forSale==1){
					if(JFile::exists($file_url)){
						$this->file_url = $file_url;
					} else {
					//	vmdebug('MediaHandler, file does not exist in safepath '.$file_url);
					}
				} else {
					$pathToTest = JPATH_ROOT.DS.str_replace('/',DS,$file_url);
					if(JFile::exists($pathToTest)){
						$this->file_url = $file_url;
					} else {
					//	vmdebug('MediaHandler, file does not exist in '.$pathToTest);
					}
				}

			}


		}

		if($this->file_is_downloadable) $this->media_role = 'file_is_downloadable';
		if($this->file_is_forSale) $this->media_role = 'file_is_forSale';
		if(empty($this->media_role)) $this->media_role = 'file_is_displayable';
		// 		vmdebug('$this->media_role',$this->media_role);

		$this->determineFoldersToTest();

		if(!empty($this->file_url) && empty($this->file_url_thumb)){
			$this->displayMediaThumb('',true,'',false);
		}


	}

	public function getUrl(){
		return $this->file_url_folder.$this->file_name.'.'.$this->file_extension;
	}

	public function getThumbUrl(){
		return $this->file_url_folder_thumb.$this->file_name.'.'.$this->file_extension;
	}

	public function getFullPath(){

		$rel_path = str_replace('/',DS,$this->file_url_folder);
		return JPATH_ROOT.DS.$rel_path.$this->file_name.'.'.$this->file_extension;
	}

	public function getThumbPath(){

		$rel_path = str_replace('/',DS,$this->file_url_folder);
		return JPATH_ROOT.DS.$rel_path.$this->file_name_thumb.'.'.$this->file_extension;
	}

	/**
	 * Tests if a function is an image by mime or extension
	 *
	 * @author Max Milbers
	 * @param string $file_mimetype
	 * @param string $file_extension
	 */
	static private function isImage($file_extension=0){

		//		if(!empty($file_mimetype)){
		//			if(strpos($file_mimetype,'image')===FALSE){
		//				$isImage = FALSE;
		//			}else{
		//				$isImage = TRUE;
			//			}
			//		} else {
			if($file_extension == 'jpg' || $file_extension == 'jpeg' || $file_extension == 'png' || $file_extension == 'gif'){
				$isImage = TRUE;

			} else {
				$isImage = FALSE;
			}
			//		}

			return $isImage;
		}

		private $_foldersToTest = array();

		/**
		 * This functions adds the folders to test for each media, you can add more folders to test with
		 * addFoldersToTest
		 * @author Max Milbers
		 */
		public function determineFoldersToTest(){

			$file_path = str_replace('/',DS,$this->file_url_folder);
			if($this->file_is_forSale){
				$this->addFoldersToTest($file_path);
			} else {
				$this->addFoldersToTest(JPATH_ROOT.DS.$file_path);
			}


			$file_path_thumb = str_replace('/',DS,$this->file_url_folder_thumb);
			$this->addFoldersToTest(JPATH_ROOT.DS.$file_path_thumb);

		}


		/**
		 * Add complete paths here to test/display if their are writable
		 *
		 * @author Max Milbers
		 * @param absolutepPath $folders
		 */
		public function addFoldersToTest($folders){
			if(!is_array($folders)) $folders = (array) $folders;
			$this->_foldersToTest = array_merge($this->_foldersToTest, $folders);
		}

		/**
		 * Displays for paths if they are writeable
		 * You set the folders to test with the function addFoldersToTest
		 * @author Max Milbers
		 */
		public function displayFoldersWriteAble(){

			$style = 'text-align:left;margin-left:20px;';
			$result = '<div class="vmquote" style="'.$style.'">';
			foreach( $this->_foldersToTest as $dir ) {
				$result .= $dir . ' :: ';
				$result .= is_writable( $dir )
				? '<span style="font-weight:bold;color:green;">'.JText::_('COM_VIRTUEMART_WRITABLE').'</span>'
				: '<span style="font-weight:bold;color:red;">'.JText::_('COM_VIRTUEMART_UNWRITABLE').'</span>';
				$result .= '<br/>';
			}
			$result .= '</div>';
			return $result;
		}

		/**
		 * Shows the supported file types for the server
		 *
		 * @author enyo 06-Nov-2003 03:32 http://www.php.net/manual/en/function.imagetypes.php
		 * @author Max Milbers
		 * @return multitype:string
		 */
		function displaySupportedImageTypes() {
			$aSupportedTypes = array();

			$aPossibleImageTypeBits = array(
			IMG_GIF=>'GIF',
			IMG_JPG=>'JPG',
			IMG_PNG=>'PNG',
			IMG_WBMP=>'WBMP'
			);

			foreach ($aPossibleImageTypeBits as $iImageTypeBits => $sImageTypeString) {

				if(function_exists('imagetypes')){
					if (imagetypes() & $iImageTypeBits) {
						$aSupportedTypes[] = $sImageTypeString;
					}
				}

			}

			$supportedTypes = '';
			if(function_exists('mime_content_type')){
				$supportedTypes .= JText::_('COM_VIRTUEMART_FILES_FORM_MIME_CONTENT_TYPE_SUPPORTED').'<br />';
			} else {
				$supportedTypes .= JText::_('COM_VIRTUEMART_FILES_FORM_MIME_CONTENT_TYPE_NOT_SUPPORTED').'<br />';
			}

			$supportedTypes .= JText::_('COM_VIRTUEMART_FILES_FORM_IMAGETYPES_SUPPORTED'). implode($aSupportedTypes,', ');

			return $supportedTypes;
		}

		/**
		 * Just for overwriting purpose for childs. Take a look on VmImage to see an example
		 *
		 * @author Max Milbers
		 */
		function displayMediaFull(){
			return $this->displayMediaThumb('id="vm_display_image"',false,'',true,true);
		}

		/**
		 * This function displays the image, when the image is not already a resized one,
		 * it tries to get first the resized one, or create a resized one or fallback in case
		 *
		 * @author Max Milbers
		 *
		 * @param string $imageArgs Attributes to be included in the <img> tag.
		 * @param boolean $lightbox alternative display method
		 * @param string $effect alternative lightbox display
		 * @param boolean $withDesc display the image media description
		 */
		function displayMediaThumb($imageArgs='',$lightbox=true,$effect="class='modal' rel='group'",$return = true,$withDescr = false,$absUrl = false, $width=0,$height=0){

			if(empty($this->file_name)){

				if($return){
					if($this->file_is_downloadable){
						$file_url = $this->theme_url.'assets/images/vmgeneral/'.VmConfig::get('downloadable','zip.png');
						$file_alt = JText::_('COM_VIRTUEMART_NO_IMAGE_SET').' '.$this->file_description;
						return $this->displayIt($file_url, $file_alt, '',true,'',$withDescr);
					} else {
						$file_url = $this->theme_url.'assets/images/vmgeneral/'.VmConfig::get('no_image_set');
						$file_alt = JText::_('COM_VIRTUEMART_NO_IMAGE_SET').' '.$this->file_description;
						return $this->displayIt($file_url, $file_alt, $imageArgs,$lightbox);
					}
				}
			}

			if(!empty($this->file_url_thumb)){
				$file_url = $this->file_url_thumb;
			}

			$media_path = JPATH_ROOT.DS.str_replace('/',DS,$this->file_url_thumb);

			if(empty($this->file_meta)){
				if(!empty($this->file_description)){
					$file_alt = $this->file_description;
				} else if(!empty($this->file_name)) {
					$file_alt = $this->file_name;
				} else {
					$file_alt = '';
				}
			} else {
				$file_alt = $this->file_meta;
			}

			if ((empty($this->file_url_thumb) || !file_exists($media_path)) && is_a($this,'VmImage')) {

				if(empty($width)) $width = VmConfig::get('img_width', 90);
				if(empty($height)) $height = VmConfig::get('img_height', 90);
				$this->file_url_thumb = $this->createThumb($width,$height);
				// 				vmdebug('displayMediaThumb',$this->file_url_thumb);
				$media_path = JPATH_ROOT.DS.str_replace('/',DS,$this->file_url_thumb);
				$file_url = $this->file_url_thumb;

				//Here we need now to update the database field of $this->file_url_thumb to prevent dynamic thumbnailing in future
				if(empty($this->_db)) $this->_db = JFactory::getDBO();
				$query = 'UPDATE `#__virtuemart_medias` SET `file_url_thumb` = "'.$this->_db->getEscaped($this->file_url_thumb).'" WHERE `#__virtuemart_medias`.`virtuemart_media_id` = "'.(int)$this->virtuemart_media_id.'" ';
				$this->_db->setQuery($query);
				$this->_db->query();
			}

			if($withDescr) $withDescr = $this->file_description;
			if (empty($this->file_url_thumb) || !file_exists($media_path)) {
				return $this->getIcon($imageArgs,$lightbox,$return,$withDescr,$absUrl);
			}

			if($return) return $this->displayIt($file_url, $file_alt, $imageArgs,$lightbox,$effect,$withDescr,$absUrl);

		}

		/**
		 * This function should return later also an icon, if there isnt any automatic thumbnail creation possible
		 * like pdf, zip, ...
		 *
		 * @author Max Milbers
		 * @param string $imageArgs
		 * @param boolean $lightbox
		 */
		function getIcon($imageArgs,$lightbox,$return=false,$withDescr=false,$absUrl = false){

			if(!empty($this->file_extension)){
				$file_url = $this->theme_url.'assets/images/vmgeneral/filetype_'.$this->file_extension.'.png';
				$file_alt = $this->file_description;
			} else {
				$file_url = $this->theme_url.'assets/images/vmgeneral/'.VmConfig::get('no_image_found');
				$file_alt = JText::_('COM_VIRTUEMART_NO_IMAGE_FOUND').' '.$this->file_description;
			}
			if($return){
				if($this->file_is_downloadable){
					return $this->displayIt($file_url, $file_alt, '',true,'',$withDescr,$absUrl);
				} else {
					return $this->displayIt($file_url, $file_alt, $imageArgs,$lightbox,'',$withDescr,$absUrl);
				}
			}

		}

		/**
		 * This function is just for options how to display an image...
		 * we may add here plugins for displaying images
		 *
		 * @author Max Milbers
		 * @param string $file_url relative Url
		 * @param string $file_alt media description
		 * @param string $imageArgs attributes for displaying the images
		 * @param boolean $lightbox use lightbox
		 */
		function displayIt($file_url, $file_alt, $imageArgs,$lightbox, $effect ="class='modal'",$withDesc=false,$absUrl = false){

			if ($withDesc) $desc='<span class="vm-img-desc">'.$withDesc.'</span>';
			else $desc='';
			// 			vmdebug('displayIt $file_alt'.$file_alt,$imageArgs);
			if($lightbox){
				$image = JHTML::image($file_url, $file_alt, $imageArgs);
				if ($file_alt ) $file_alt = 'title="'.$file_alt.'"';
				if ($this->file_url and pathinfo($this->file_url, PATHINFO_EXTENSION) ) $href = JURI::root() .$this->file_url ;
				else $href = $file_url ;
				if ($this->file_is_downloadable) {
					$lightboxImage = '<a '.$file_alt.' '.$effect.' href="'.$href.'">'.$image.$desc.'</a>';
				} else {
					$lightboxImage = '<a '.$file_alt.' '.$effect.' href="'.$href.'">'.$image.'</a>';
					$lightboxImage = $lightboxImage.$desc;
				}

				return $lightboxImage;
			} else {
				$root='';
				if($absUrl) $root = JURI::root();
				return JHTML::image($root.$file_url, $file_alt, $imageArgs).$desc;
			}
		}

		/**
		 * Handles the upload process of a media, sets the mime_type, when success
		 *
		 * @author Max Milbers
		 * @param string $urlfolder relative url of the folder where to store the media
		 * @return name of the uploaded file
		 */
		function uploadFile($urlfolder,$overwrite = false){

			if(empty($urlfolder) OR strlen($urlfolder)<2){
				vmError('Not able to upload file, give path/url empty/too short '.$urlfolder.' please correct path in your virtuemart config');
				return false;
			}
			$media = JRequest::getVar('upload', array(), 'files');

			$app = JFactory::getApplication();
			switch ($media['error']) {
				case 0:
					$path_folder = str_replace('/',DS,$urlfolder);

					//Sadly it does not work to upload unicode files,
					// the ä for example is stored on windows as Ã¤, this seems to be a php issue (maybe a config setting)
					//
					//Sanitize name of media
				/*	$dotPos = strrpos($media['name'],'.');
					$safeMediaName = vmFile::makeSafe( $media['name'] );
					if($dotPos!==FALSE){
						$mediaPure = substr($media['name'],0,$dotPos);
						$mediaExtension = strtolower(substr($media['name'],$dotPos));
					} else{
						$mediaPure = '';
						$mediaExtension = '';
					}
				*/

					$safeMediaName = vmFile::makeSafe( $media['name'] );
					$media['name'] = $safeMediaName;

					$mediaPure = JFile::stripExt($media['name']);
					$mediaExtension = '.'.strtolower(JFile::getExt($media['name']));
					vmdebug('uploadFile $safeMediaName',$media['name'],$safeMediaName,$mediaPure,$mediaExtension);

					if(!$overwrite){
						while (file_exists(JPATH_ROOT.DS.$path_folder.$mediaPure.$mediaExtension)) {
							$mediaPure = $mediaPure.rand(1,9);
						}
					}

					$media['name'] = $this->file_name =$mediaPure.$mediaExtension;
					if($this->file_is_forSale==0){
						JFile::upload($media['tmp_name'],JPATH_ROOT.DS.$path_folder.$media['name']);
					} else {
						JFile::upload($media['tmp_name'],$path_folder.$media['name']);
					}

					$this->file_mimetype = $media['type'];
					$this->media_published = 1;
					$app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_FILE_UPLOAD_OK',JPATH_ROOT.DS.$path_folder.$media['name']));
					return $media['name'];

				case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
					$app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_PRODUCT_FILES_ERR_UPLOAD_MAX_FILESIZE',$media['name'],$media['tmp_name']), 'warning');
					break;
				case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
					$app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_PRODUCT_FILES_ERR_MAX_FILE_SIZE',$media['name'],$media['tmp_name']), 'warning');
					break;
				case 3: //uploaded file was only partially uploaded
					$app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_PRODUCT_FILES_ERR_PARTIALLY',$media['name'],$media['tmp_name']), 'warning');
					break;
				case 4: //no file was uploaded
					//$vmLogger->warning( "You have not selected a file/image for upload." );
					break;
				default: //a default error, just in case!  :)
					//$vmLogger->warning( "There was a problem with your upload." );
					break;
			}
			return false;
		}

		/**
		 * Deletes a file
		 *
		 * @param string $url relative Url, gets adjusted to path
		 */
		function deleteFile($url){

			jimport('joomla.filesystem.file');
			$file_path = str_replace('/',DS,$url);
			$app = JFactory::getApplication();
			if($res = JFile::delete( JPATH_ROOT.DS.$file_path )){
				$app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_FILE_DELETE_OK',$file_path));
			} else {
				$app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_FILE_DELETE_ERR',$res));
			}
			return ;
		}

		/**
		 * Processes the choosed Action while storing the data, gets extend by the used child, use for the action clear commands.
		 * Useable commands in all medias upload, upload_delete, delete, and all of them with _thumb on it also.
		 *
		 * @author Max Milbers
		 * @param arraybyform $data
		 */
		function processAction($data){

			if(empty($data['media_action'])) return $data;
			// 			$data['published'] = 1;
			if( $data['media_action'] == 'upload' ){

				$this->virtuemart_media_id=0;
				$this->file_url='';
				$this->file_url_thumb='';
				$file_name = $this->uploadFile($this->file_url_folder);
				$this->file_name = $file_name;
				$this->file_url = $this->file_url_folder.$this->file_name;
			}
			else if( $data['media_action'] == 'replace' ){
				// 				$oldFileUrl = $data['file_url'];
				// 				vmdebug('replace media',$this);
				$oldFileUrl = $this->file_url;
				$file_name = $this->uploadFile($this->file_url_folder,true);
				$this->file_name = $file_name;
				$this->file_url = $this->file_url_folder.$this->file_name;
				if($this->file_url!=$oldFileUrl && !empty($this->file_name)){
					$this->deleteFile($oldFileUrl);
				}
			}
			else if( $data['media_action'] == 'replace_thumb' ){

				$oldFileUrl = $this->file_url_folder_thumb;
				$file_name = $this->uploadFile($this->file_url_folder_thumb,true);
				$this->file_name = $file_name;
				$this->file_url_thumb = $this->file_url_folder_thumb.$this->file_name;
				if($this->file_url_thumb!=$oldFileUrl&& !empty($this->file_name)){
					$this->deleteFile($oldFileUrl);
				}

			}
			else if( $data['media_action'] == 'delete' ){
				//TODO this is complex, we must assure that the media entry gets also deleted.
				//$this->deleteFile($this->file_url);
				unset($data['active_media_id']);

			}


			if(empty($this->file_title) && !empty($file_name)) $this->file_title = $file_name;
			//		if(empty($this->file_title) && !empty($file_name)) $data['file_title'] = $file_name;

			return $data;
		}


		/**
		 * For processing the Attributes of the media while the storing process
		 *
		 * @author Max Milbers
		 * @param unknown_type $data
		 */
		function processAttributes($data){

			$this->file_is_product_image = 0;
			$this->file_is_downloadable = 0;
// 			$this->file_is_forSale = 0;

			if(empty($data['media_roles'])) return $data;

			if($data['media_roles'] == 'file_is_downloadable'){
				$this->file_is_downloadable = 1;
				$this->file_is_forSale = 0;
			}
			else if($data['media_roles'] == 'file_is_forSale'){
				$this->file_is_downloadable = 0;
				$this->file_is_forSale = 1;
				$this->file_url_folder = VmConfig::get('forSale_path');
				$this->file_url_folder_thumb = VmConfig::get('forSale_path_thumb');

				$this->setRole = false;
			}

			if($this->setRole and $data['media_roles'] != 'file_is_forSale'){

				$this->file_url_folder = $this->getMediaUrlByView($data['media_attributes']);	//media_roles
				$this->file_url_folder_thumb = $this->file_url_folder.'resized/';

				$typelessUrl = 'images/stories/virtuemart/typeless/'.$this->file_name;
				vmdebug('the Urls',$data['media_roles'],$typelessUrl,$this->file_url_folder.$this->file_name);
				if(!file_exists($this->file_url_folder.$this->file_name) and file_exists($typelessUrl)){
					vmdebug('Execute move');
					JFile::move($typelessUrl, $this->file_url_folder.$this->file_name);
				}
			}

			if(!empty($data['vmlangimg'])) {
				$vmlangimg = implode(",", $data['vmlangimg']);
				$this->file_lang = $vmlangimg;
			}


			return $data;
		}

		private $_actions = array();
		/**
		 * This method can be used to add extra actions to the media
		 *
		 * @author Max Milbers
		 * @param string $optionName this is the value in the form
		 * @param string $langkey the langkey used
		 */
		function addMediaAction($optionName,$langkey){
			$this->_actions[$optionName] = $langkey ;
		}

		/**
		 * Adds the media action which are needed in the form for all media,
		 * you can use this function in your child calling parent. Look in VmImage for an exampel
		 * @author Max Milbers
		 */
		function addMediaActionByType(){

			$this->addMediaAction(0,'COM_VIRTUEMART_NONE');

			$this->addMediaAction('upload','COM_VIRTUEMART_FORM_MEDIA_UPLOAD');
			if(empty($this->file_name)){

			} else {
				//			$this->addMediaAction('upload_delete','COM_VIRTUEMART_FORM_MEDIA_UPLOAD_DELETE');
				$this->addMediaAction('replace','COM_VIRTUEMART_FORM_MEDIA_UPLOAD_REPLACE');
				//			$this->addMediaAction('delete','COM_VIRTUEMART_FORM_MEDIA_DELETE');
			}

			$this->addMediaAction('replace_thumb','COM_VIRTUEMART_FORM_MEDIA_UPLOAD_REPLACE_THUMB');

			//		$this->addMediaAction('replace_thumb','COM_VIRTUEMART_FORM_MEDIA_UPLOAD_DELETE_THUMB');

			//		if(empty($this->file_url_thumb)){
			//			$this->addMediaAction('upload_thumb','COM_VIRTUEMART_FORM_MEDIA_UPLOAD_THUMB');
			//		} else {
			//			$this->addMediaAction('upload_delete_thumb','COM_VIRTUEMART_FORM_MEDIA_UPLOAD_DELETE_THUMB');
			//			$this->addMediaAction('delete_thumb','COM_VIRTUEMART_FORM_MEDIA_DELETE_THUMB');
			//		}

		}


		private $_mLocation = array();

		/**
		 * This method can be used to add extra attributes to the media
		 *
		 * @author Max Milbers
		 * @param string $optionName this is the value in the form
		 * @param string $langkey the langkey used
		 */
		public function addMediaAttributes($optionName,$langkey=''){
			$this->_mLocation[$optionName] = $langkey ;
		}

		/**
		 * Adds the attributes which are needed in the form for all media,
		 * you can use this function in your child calling parent. Look in VmImage for an exampel
		 * @author Max Milbers
		 */
		public function addMediaAttributesByType(){


			if($this->setRole){
				// 				$this->addMediaAttributes('file_is_product_image','COM_VIRTUEMART_FORM_MEDIA_SET_PRODUCT');
				$this->addMediaAttributes('product','COM_VIRTUEMART_FORM_MEDIA_SET_PRODUCT'); // => file_is_displayable  =>location
				$this->addMediaAttributes('category','COM_VIRTUEMART_FORM_MEDIA_SET_CATEGORY');
				$this->addMediaAttributes('manufacturer','COM_VIRTUEMART_FORM_MEDIA_SET_MANUFACTURER');
				$this->addMediaAttributes('vendor','COM_VIRTUEMART_FORM_MEDIA_SET_VENDOR');

				$this->_mRoles['file_is_displayable'] = 'COM_VIRTUEMART_FORM_MEDIA_DISPLAYABLE' ;
				$this->_mRoles['file_is_downloadable'] = 'COM_VIRTUEMART_FORM_MEDIA_DOWNLOADABLE' ;
				$this->_mRoles['file_is_forSale'] = 'COM_VIRTUEMART_FORM_MEDIA_SET_FORSALE' ;
			} else {

				if($this->file_is_forSale==1){
					$this->_mRoles['file_is_forSale'] = 'COM_VIRTUEMART_FORM_MEDIA_SET_FORSALE' ;
				} else {
					$this->_mRoles['file_is_displayable'] = 'COM_VIRTUEMART_FORM_MEDIA_DISPLAYABLE' ;
					$this->_mRoles['file_is_downloadable'] = 'COM_VIRTUEMART_FORM_MEDIA_DOWNLOADABLE' ;

				}
			}

		}


		private $_hidden = array();

		/**
		 * Use this to adjust the hidden fields of the displayFileHandler to your form
		 *
		 * @author Max Milbers
		 * @param string $name for exampel view
		 * @param string $value for exampel media
		 */
		public function addHidden($name, $value=''){
			$this->_hidden[$name] = $value;
		}

		/**
		 * Adds the hidden fields which are needed for the form in every case
		 * @author Max Milbers
		 */
		private function addHiddenByType(){

			$this->addHidden('active_media_id',$this->virtuemart_media_id);
			$this->addHidden('option','com_virtuemart');
			//		$this->addHidden('file_mimetype',$this->file_mimetype);

		}

		/**
		 * Displays file handler and file selector
		 *
		 * @author Max Milbers
		 * @param array $fileIds
		 */
		public function displayFilesHandler($fileIds,$type){

			VmConfig::loadJLang('com_virtuemart_media');
			$html = $this->displayFileSelection($fileIds,$type);
			$html .= $this->displayFileHandler();

			if(empty($this->_db)) $this->_db = JFactory::getDBO();
			$this->_db->setQuery('SELECT FOUND_ROWS()');
			$imagetotal = $this->_db->loadResult();
			//vmJsApi::jQuery(array('easing-1.3.pack','mousewheel-3.0.4.pack','fancybox-1.3.4.pack'),'','fancybox');
			$j = '
//<![CDATA[
'; 
			if (JVM_VERSION===1) {
				$j .= "
			jQuery(document).ready(function(){ jQuery('#ImagesContainer').vm2admin('media','".$type."','0') }); " ;
			}
			else $j .= "
			jQuery(document).ready(function(){ jQuery('#ImagesContainer').vm2admin('media','".$type."','0') }); " ;

			$j .="
			jQuery(document).ready(function($){
			var medialink = 'index.php?option=com_virtuemart&view=media&task=viewJson&format=json&mediatype=".$type."';
			var media = $('#searchMedia').data();
			var searchMedia = $('input#searchMedia');
			searchMedia.click(function () {
				if (media.start>0) media.start=0;
			});
			searchMedia.autocomplete({

				source: medialink,
				select: function(event, ui){
					$('#ImagesContainer').append(ui.item.label);
					//$(this).autocomplete( 'option' , 'source' , 'index.php?option=com_virtuemart&view=product&task=getData&format=json&type=relatedcategories&row='+nextCustom )

				},
				minLength:1,
				html: true
			});
			 $('.js-pages').click(function (e) {
				e.preventDefault();
				if (searchMedia.val() =='') {
					searchMedia.val(' ');
					media.start = 0;
				} else if ($(this).hasClass('js-next')) media.start = media.start+16 ;
				else if (media.start > 0) media.start = media.start-16 ;

				searchMedia.autocomplete( 'option' , 'source' , medialink+'&start='+media.start );
				searchMedia.autocomplete( 'search');
			});
			$('#ImagesContainer').sortable({
				update: function(event, ui) {
					$(this).find('.ordering').each(function(index,element) {
						$(element).val(index);
						//console.log(index+' ');

					});

				}
			});
			$('#upload').change( function (){
					if ($('#media_action0').is(':checked') ) $('#media_actionupload').attr('checked', true);
				});


		}); 
//]]>
		";

			$document = JFactory::getDocument ();
			$document->addScriptDeclaration ( $j);
			return $html;
		}


		/**
		 * Displays a possibility to select already uploaded media
		 * the getImagesList must be adjusted to have more search functions
		 * @author Max Milbers
		 * @param array $fileIds
		 */
		public function displayFileSelection($fileIds,$type = 0){

			$html='';
			$html .= '<fieldset class="checkboxes">' ;
			$html .= '<legend>'.JText::_('COM_VIRTUEMART_IMAGES').'</legend>';
			$html .=  '<span class="hasTip always-left" title="'.JText::_('COM_VIRTUEMART_SEARCH_MEDIA_TIP').'">'.JText::_('COM_VIRTUEMART_SEARCH_MEDIA') . '</span>';
			$html .=   '
					<input type="text" name="searchMedia" id="searchMedia" data-start="0" value="' .JRequest::getString('searchMedia') . '" class="text_area always-left" />
					<button class="reset-value fg-button">'.JText::_('COM_VIRTUEMART_RESET') .'</button>
					<a class="js-pages js-previous fg-button ui-state-default fg-button-icon-left ui-corner-all" ><span class="ui-icon ui-icon-circle-minus" style="display:inline-block;"></span> 16 </a>
					<a class="js-pages js-next fg-button ui-state-default fg-button-icon-right ui-corner-all"> 16 <span class="ui-icon ui-icon-circle-plus" style="display:inline-block;"></span></a>
					<br class="clear"/>';
			;
			//$result = $this->getImagesList($type);
			$html .= '<div id="ImagesContainer">';

			// 			$html .= ShopFunctions::displayDefaultViewSearch('COM_VIRTUEMART_NAME','','searchMedia') ;

			// if(empty($fileIds)) {
			// return  $html;
			// }
			// $text = 'COM_VIRTUEMART_FILES_FORM_ALREADY_ATTACHED_FILE_PRIMARY';
			if(!empty($fileIds)) {
				foreach($fileIds as $k=>$id){
					$html .= $this->displayImage($id,$k );
				}
			}
			//$html .= '<a id="addnewselectimage2" href="#media-dialog">'.JText::_('COM_VIRTUEMART_IMAGE_ATTACH_NEW').'</a>';
			$html .= '</div>';



			return $html.'</fieldset><div class="clear"></div>';
		}


		function displayImage($virtuemart_media_id ,$key) {

			$db = JFactory::getDBO();
			$query='SELECT * FROM `#__virtuemart_medias` where `virtuemart_media_id`='.(int)$virtuemart_media_id;
			$db->setQuery( $query );
			$image = $db->loadObject();
			if (isset($image->file_url)) {
				$image->file_root = JURI::root(true).'/';
				$image->msg =  'OK';
				return  '<div  class="vm_thumb_image"><input type="hidden" value="'.$image->virtuemart_media_id.'" name="virtuemart_media_id[]">
				<input class="ordering" type="hidden" name="mediaordering['.$image->virtuemart_media_id.']" value="'.$key.'">
			<a class="vm_thumb" rel="group1" title ="'.$image->file_title.'"href="'.JURI::root(true).'/'.$image->file_url.'" >
			'.JHTML::image($image->file_url_thumb, $image->file_title, '').'
			</a><div class="vmicon vmicon-16-remove" title="'.JText::_('COM_VIRTUEMART_IMAGE_REMOVE').'"></div><div class="edit-24-grey" title="'.JText::_('COM_VIRTUEMART_IMAGE_EDIT_INFO').'"></div></div>';
			} else {
				$fileTitle = empty($image->file_title)? 'no  title':$image->file_title;
				return  '<div  class="vm_thumb_image"><b>'.JText::_('COM_VIRTUEMART_NO_IMAGE_SET').'</b><br />'.$fileTitle.'</div>';
			}

		}


		function displayImages($types ='',$page=0,$max=16 ) {

			$Images = array();
			$list = VmMediaHandler::getImagesList($types,$page,$max);
			if (empty($list['images'])) return JText::_('COM_VIRTUEMART_ADMIN_CFG_NOIMAGEFOUND');

			foreach ($list['images'] as $key =>$image) {
				$htmlImages ='';
				if ($image->file_url_thumb > "0" ) {
					// $imagesList->file_root = JURI::root(true).'/';
					// $imagesList->msg =  'OK';
					$htmlImages .= '<div class="vm_thumb_image">
					<span><a class="vm_thumb" rel="group1" title ="'.$image->file_title.'"href="'.JURI::root(true).'/'.$image->file_url.'" >'
					.JHTML::image($image->file_url_thumb,$image->file_title, 'class="vm_thumb" ').'</span></a>';
				} else {
					$htmlImages .=  '<div class="vm_thumb_image">'.JText::_('COM_VIRTUEMART_NO_IMAGE_SET').'<br />'.$image->file_title ;
				}
				$Images[$key ]['label'] = $htmlImages.'<input type="hidden" value="'.$image->virtuemart_media_id.'" name="virtuemart_media_id['.$image->virtuemart_media_id.']"><input class="ordering" type="hidden" name="mediaordering['.$image->virtuemart_media_id.']" value=""><div class="vmicon vmicon-16-remove" title="remove"></div><div title="edit image information" class="edit-24-grey"></div></div>';
				$Images[$key ]['value'] = $image->file_title.' :: '.$image->virtuemart_media_id;
			}
			//$list['htmlImages'] = $htmlImages;
			return $Images;
		}


		/**
		 * Retrieve a list of layouts from the default and chosen templates directory.
		 *
		 * We may use here the getFiles function of the media model or write something simular
		 * @author Max Milbers
		 * @param name of the view
		 * @return object List of flypage objects
		 */
		function getImagesList($type = '',$page=0, $max=16) {

			if(empty($this->_db)) $this->_db = JFactory::getDBO();
			$list = array();
			$vendorId=1;//TODO control the vendor
			$q='SELECT SQL_CALC_FOUND_ROWS `virtuemart_media_id` FROM `#__virtuemart_medias` WHERE `published`=1
    	AND (`virtuemart_vendor_id`= "'.(int)$vendorId.'" OR `shared` = "1")';
			if(!empty($type)){
				$q .= ' AND `file_type` = "'.$type.'" ';
			}
			if ($search = JRequest::getString('term', false)){
				$search = '"%' . $this->_db->getEscaped( $search, true ) . '%"' ;
				$q .=  ' AND (`file_title` LIKE '.$search.' OR `file_description` LIKE '.$search.' OR `file_meta` LIKE '.$search.') ';
			}
			$q .= ' LIMIT '.(int)$page*$max.', '.(int)$max;


			$this->_db->setQuery($q);
			//		$result = $this->_db->loadAssocList();
			if ($virtuemart_media_ids = $this->_db->loadResultArray()) {
				$errMsg = $this->_db->getErrorMsg();
				$errs = $this->_db->getErrors();

				$model = VmModel::getModel('Media');

				$this->_db->setQuery('SELECT FOUND_ROWS()');
				$list['total'] = $this->_db->loadResult();

				$list['images'] = $model->createMediaByIds($virtuemart_media_ids, $type);

				if(!empty($errMsg)){
					$app = JFactory::getApplication();
					$errNum = $this->_db->getErrorNum();
					$app->enqueueMessage('SQL-Error: '.$errNum.' '.$errMsg);
				}

				if($errs){
					$app = JFactory::getApplication();
					foreach($errs as $err){
						$app->enqueueMessage($err);
					}
				}

				return $list;
			}
			else return array();
		}


		/**
		 * This displays a media handler. It displays the full and the thumb (icon) of the media.
		 * It also gives a possibility to upload/change/thumbnail media
		 *
		 * @param string $imageArgs html atttributes, Just for displaying the fullsized image
		 */
		public function displayFileHandler(){

			VmConfig::loadJLang('com_virtuemart_media');
			//VmConfig::loadJLang('com_virtuemart_media');
			$identify = ''; // ':'.$this->virtuemart_media_id;

			$this->addHiddenByType();

			$html = '<fieldset class="checkboxes">' ;
			$html .= '<legend>'.JText::_('COM_VIRTUEMART_IMAGE_INFORMATION').'</legend>';
			$html .= '<div class="vm__img_autocrop">';
			$imageArgs = 'id="vm_display_image" ';
			$html .=  $this->displayMediaFull($imageArgs,false,'',false).'</div>';

			//This makes problems, when there is already a form, and there would be form in a form. breaks js in some browsers
			//		$html .= '<form name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">';

			$html .= ' <table class="adminform"> ';

			if ($this->published || $this->virtuemart_media_id === 0){
				$checked = 1;
			} else {
				$checked = 0;
			}

			$html .= '<tr>';
			//  The following was removed bacause the check box (publish/unpublish) was not functioning...
			// 			$this->media_published = $this->published;
			$html .= '<td class="labelcell">
		<label for="published">'. JText::_('COM_VIRTUEMART_FILES_FORM_FILE_PUBLISHED') .'</label>
	</td>
	<td>';
		if(!class_exists('VmHTML')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'html.php');
		$html .= VmHTML::checkbox('media_published',$checked,1,0,'class="inputbox"','media_published'.$identify) ;
		//<input type="checkbox" class="inputbox" id="media_published'.$identify.'" name="media_published'.$identify.'" '.$checked.' size="16" value="1" />

	$html .='</td>';
			$html .= '<td rowspan = "8">';
			$html .= JHTML::image($this->file_url_thumb, 'thumbnail', 'id="vm_thumb_image" style="overflow: auto; float: right;"');
			// $html .= $this->displayMediaThumb('',false,'id="vm_thumb_image" style="overflow: auto; float: right;"');
			$html .= '</td>';

			$html .= '</tr>';

			// 			$html .= '<tr>
			// 	<td class="labelcell">'. JText::_('COM_VIRTUEMART_FILES_FORM_CURRENT_FILE') .'</td>
			// 	<td>'.$this->file_name.'.'.$this->file_extension .'</td>
			// </tr>';

			if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');
			if(!Permissions::getInstance()->check('admin') ) $readonly='readonly'; else $readonly ='';
			$html .= $this->displayRow('COM_VIRTUEMART_FILES_FORM_FILE_TITLE','file_title');
			$html .= $this->displayRow('COM_VIRTUEMART_FILES_FORM_FILE_DESCRIPTION','file_description');
			$html .= $this->displayRow('COM_VIRTUEMART_FILES_FORM_FILE_META','file_meta');

			$html .= $this->displayRow('COM_VIRTUEMART_FILES_FORM_FILE_URL','file_url',$readonly);
			$html .= $this->displayRow('COM_VIRTUEMART_FILES_FORM_FILE_URL_THUMB','file_url_thumb',$readonly);

			$this->addMediaAttributesByType();

			$html .= '<tr>
					<td class="labelcell">'.JText::_('COM_VIRTUEMART_FILES_FORM_ROLE').'</td>
					<td><fieldset class="checkboxes">'.JHTML::_('select.radiolist', $this->getOptions($this->_mRoles), 'media_roles'.$identify, '', 'value', 'text', $this->media_role).'</fieldset></td></tr>';

			// 			$html .= '<tr><td class="labelcell">'.VmHTML::checkbox('file_is_forSale', $this->file_is_forSale);
			// 			$html .= VmHTML::checkbox('file_is_downloadable', $this->file_is_downloadable);

			if(!empty($this->file_type)){

				$html .= '<tr>
						<td class="labelcell">'.JText::_('COM_VIRTUEMART_FILES_FORM_LOCATION').'</td>
						<td><fieldset class="checkboxes">'.JText::_('COM_VIRTUEMART_FORM_MEDIA_SET_'.strtoupper($this->file_type)).'</fieldset></td></tr>';
			} else {
				$mediaattribtemp = $this->media_attributes;
				if(empty($this->media_attributes)){
					$mediaattribtemp = 'product';
				}
				$html .= '<tr>
						<td class="labelcell">'.JText::_('COM_VIRTUEMART_FILES_FORM_LOCATION').'</td>
						<td><fieldset class="checkboxes">'.JHTML::_('select.radiolist', $this->getOptions($this->_mLocation), 'media_attributes'.$identify, '', 'value', 'text', $mediaattribtemp).'</fieldset></td></tr>';
			}
			
			// select language for image
			if (count(vmconfig::get('active_languages'))>1) {
				$selectedLangue = explode(",", $this->file_lang);
				$languages = JLanguageHelper::createLanguageList($selectedLangue, constant('JPATH_SITE'), true);
				$html .= '<tr>
						<td class="labelcell"><span class="hasTip" title="' . JText::_ ('COM_VIRTUEMART_FILES_FORM_LANGUAGE_TIP') . '">' . JText::_ ('COM_VIRTUEMART_FILES_FORM_LANGUAGE') . '</span></td>
						<td><fieldset class="inputbox">'.JHTML::_('select.genericlist',  $languages, 'vmlangimg[]', 'size="10" multiple="multiple"', 'value', 'text', $selectedLangue ).'</fieldset></td>
						</tr>';
			}

			$html .= '</table>';
			$html .='<br /></fieldset>';

			$this->addMediaActionByType();

			$html .= '<fieldset class="checkboxes">' ;
			$html .= '<legend>'.JText::_('COM_VIRTUEMART_FILE_UPLOAD').'</legend>';
			$html .= JText::_('COM_VIRTUEMART_IMAGE_ACTION'). JHTML::_('select.radiolist', $this->getOptions($this->_actions), 'media_action'.$identify, '', 'value', 'text', 0).'<br /><br style="clear:both" />';


			$html .= JText::_('COM_VIRTUEMART_FILE_UPLOAD').' <input type="file" name="upload" id="upload" size="50" class="inputbox" /><br />';

			$html .= '<br />'.$this->displaySupportedImageTypes();
			$html .='<br /></fieldset>';
			$html .= $this->displayFoldersWriteAble();

			$html .= $this->displayHidden();

			//		$html .= '</form>';

			return $html;
		}

		/**
		 * child classes can add their own options and you can get them with this function
		 *
		 * @param array $optionsarray Allowed values are $this->_actions and $this->_attributes
		 */
		private function getOptions($optionsarray){

			$options=array();
			foreach($optionsarray as $optionName=>$langkey){
				$options[] = JHTML::_('select.option',  $optionName, JText::_( $langkey ) );
			}
			return $options;
		}

		/**
		 * Just for creating simpel rows
		 *
		 * @author Max Milbers
		 * @param string $descr
		 * @param string $name
		 */
		private function displayRow($descr, $name,$readonly=''){
			$html = '<tr>
		<td class="labelcell">'.JText::_($descr).'</td>
		<td> <input type="text" '.$readonly.'class="inputbox" name="'.$name.'" size="70" value="'.$this->$name.'" /></td>
	</tr>';
			return $html;
		}

		/**
		 * renders the hiddenfields added in the layout before (used to make the displayFileHandle reusable)
		 * @author Max Milbers
		 */
		private function displayHidden(){
			$html='';
			foreach($this->_hidden as $k=>$v){
				$html .= '<input type="hidden" name="'.$k.'" value="'.$v.'" />';
			}
			return $html;
		}

}
