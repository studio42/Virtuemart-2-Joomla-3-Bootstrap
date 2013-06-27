<?php
/**
 *
 * Media table
 *
 * @package    VirtueMart
 * @subpackage Media
 * @author Max Milbers
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: medias.php 6468 2012-09-18 22:00:43Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');

if (!class_exists ('VmTable')) {
	require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmtable.php');
}

/**
 * Media table class
 * The class is is used to manage the countries in the shop.
 *
 * @author Max Milbers
 * @package        VirtueMart
 */
class TableMedias extends VmTable {

	/** @var int Primary key */
	var $virtuemart_media_id = 0;
	var $virtuemart_vendor_id = 0;

	/** @var string File title */
	var $file_title = '';
	/** @var string File description */
	var $file_description = '';
	/** @var string File Meta or alt  */
	var $file_meta = '';

	/** @var string File mime type */
	var $file_mimetype = '';
	/** @var string File typ, this determines where a media is stored */
	var $file_type = '';
	/** @var string File URL */
	var $file_url = '';
	var $file_url_thumb = '';

	/** @var int File published or not */
	var $published = 0;
	/** @var int File is an image or other */
	var $file_is_downloadable = 0;
	var $file_is_forSale = 0;
	var $file_is_product_image = 0;


	var $shared = 0;
	var $file_params = '';
	var $file_lang = '';

	/**
	 * @author Max Milbers
	 * @param $db A database connector object
	 */
	function __construct (&$db) {

		parent::__construct ('#__virtuemart_medias', 'virtuemart_media_id', $db);

		//In a VmTable the primary key is the same as the _tbl_key and therefore not needed
// 		$this->setPrimaryKey('virtuemart_media_id');
//		$this->setUniqueName('file_title');

		$this->setLoggable ();

	}

	/**
	 *
	 * @author Max Milbers
	 * @return boolean True if the table buffer is contains valid data, false otherwise.
	 */
	function check () {

		$ok = TRUE;
		$notice = TRUE;

		if (empty($this->file_type) and empty($this->file_is_forSale)) {
			$ok = FALSE;
			vmError (JText::sprintf ('COM_VIRTUEMART_MEDIA_NO_TYPE'), $this->file_name);
		}

		if (!empty($this->file_url)) {
			if (function_exists ('mb_strlen')) {
				if (mb_strlen ($this->file_url) > 254) {
					vmError (JText::sprintf ('COM_VIRTUEMART_URL_TOO_LONG', mb_strlen ($this->file_url)));
				}
			}
			else {
				if (strlen ($this->file_url) > 254) {
					vmError (JText::sprintf ('COM_VIRTUEMART_URL_TOO_LONG', strlen ($this->file_url)));
				}
			}

			if (strpos ($this->file_url, '..') !== FALSE) {
				$ok = FALSE;
				vmError (JText::sprintf ('COM_VIRTUEMART_URL_NOT_VALID', $this->file_url));
			}

			if (empty($this->virtuemart_media_id)) {
				$q = 'SELECT `virtuemart_media_id`,`file_url` FROM `' . $this->_tbl . '` WHERE `file_url` = "' . $this->_db->getEscaped ($this->file_url) . '" ';
				$this->_db->setQuery ($q);
				$unique_id = $this->_db->loadAssocList ();

				$count = count ($unique_id);
				if ($count !== 0) {

					if ($count == 1) {
						if (empty($this->virtuemart_media_id)) {
							$this->virtuemart_media_id = $unique_id[0]['virtuemart_media_id'];
						}
						else {
							vmError (JText::_ ('COM_VIRTUEMART_MEDIA_IS_ALREADY_IN_DB'));
							$ok = FALSE;
						}
					}
					else {
						//      			vmError(JText::_('COM_VIRTUEMART_MEDIA_IS_DOUBLED_IN_DB'));
						vmError (JText::_ ('COM_VIRTUEMART_MEDIA_IS_DOUBLED_IN_DB'));
						$ok = FALSE;
					}
				}
			}

		}
		else {
			vmError (JText::_ ('COM_VIRTUEMART_MEDIA_MUST_HAVE_URL'));
			$ok = FALSE;
		}

		if (empty($this->file_title) && !empty($this->file_name)) {
			$this->file_title = $this->file_name;
		}

		if (!empty($this->file_title)) {
			if (strlen ($this->file_title) > 126) {
				vmError (JText::sprintf ('COM_VIRTUEMART_TITLE_TOO_LONG', strlen ($this->file_title)));
			}

			$q = 'SELECT * FROM `' . $this->_tbl . '` ';
			$q .= 'WHERE `file_title`="' . $this->_db->getEscaped ($this->file_title) . '" AND `file_type`="' . $this->_db->getEscaped ($this->file_type) . '"';
			$this->_db->setQuery ($q);
			$unique_id = $this->_db->loadAssocList ();

			$tblKey = 'virtuemart_media_id';

			if (!empty($unique_id)) {
				foreach ($unique_id as $item) {
					if ($item['virtuemart_media_id'] != $this->virtuemart_media_id) {
						$lastDir = substr ($this->file_url, 0, strrpos ($this->file_url, '/'));
						$lastDir = substr ($lastDir, strrpos ($lastDir, '/') + 1);
						if (!empty($lastDir)) {
							$this->file_title = $this->file_title . '_' . $lastDir;
						}
						else {
							$this->file_title = $this->file_title . '_' . rand (1, 9);
						}
					}
				}
			}
		}
		else {
			vmError (JText::_ ('COM_VIRTUEMART_MEDIA_MUST_HAVE_TITLE'));
			$ok = FALSE;
		}

		if (!empty($this->file_description)) {
			if (strlen ($this->file_description) > 254) {
				vmError (JText::sprintf ('COM_VIRTUEMART_DESCRIPTION_TOO_LONG', strlen ($this->file_description)));
			}
		}

//		$app = JFactory::getApplication();

		//vmError('Checking '.$this->file_url);

		if (empty($this->file_mimetype)) {

			$rel_path = str_replace ('/', DS, $this->file_url);

			//The function mime_content_type is deprecated, we may use
			/*function _mime_content_type($filename)
			{
				$result = new finfo();

				if (is_resource($result) === true)
				{
					return $result->file($filename, FILEINFO_MIME_TYPE);
				}

				return false;
			}
			if (function_exists ('mime_content_type')) {
				$ok = TRUE;
				$app = JFactory::getApplication ();

				if (!$this->file_is_forSale) {
					$this->file_mimetype = mime_content_type (JPATH_ROOT . DS . $rel_path);
				}
				else {
					$this->file_mimetype = mime_content_type ($rel_path);
				}

				if (!empty($this->file_mimetype)) {
					if ($this->file_mimetype == 'directory') {
						vmError ('cant store this media, is a directory ' . $rel_path);
						return FALSE;
					}
					else {
						if (strpos ($this->file_mimetype, 'corrupt') !== FALSE) {
							vmError ('cant store this media, Document corrupt: Cannot read summary info ' . $rel_path);
							return FALSE;
						}
					}
				}
				else {
					vmError ('Couldnt resolve mime ' . $rel_path);
					return FALSE;
				}

			}
			else {*/
				if (!class_exists ('JFile')) {
					require(JPATH_VM_LIBRARIES . DS . 'joomla' . DS . 'filesystem' . DS . 'file.php');
				}

				if (!$this->file_is_forSale) {
					$lastIndexOfSlash = strrpos ($this->file_url, '/');
					$name = substr ($this->file_url, $lastIndexOfSlash + 1);
					$file_extension = strtolower (JFile::getExt ($name));
				}
				else {
					$lastIndexOfSlash = strrpos ($this->file_url, DS);
					$name = substr ($this->file_url, $lastIndexOfSlash + 1);
					$file_extension = strtolower (JFile::getExt ($name));
				}

				if (empty($name)) {
					vmError (JText::_ ('COM_VIRTUEMART_NO_MEDIA'));
				}

				//images
				elseif($file_extension === 'jpg' or $file_extension === 'jpeg' or $file_extension === 'jpe'){
					$this->file_mimetype = 'image/jpeg';
				}
				elseif($file_extension === 'gif'){
					$this->file_mimetype = 'image/gif';
				}
				elseif($file_extension === 'png'){
					$this->file_mimetype = 'image/png';
				}
				elseif($file_extension === 'bmp'){
					vmInfo(JText::sprintf('COM_VIRTUEMART_MEDIA_SHOULD_NOT_BMP',$name));
					$notice = true;
				}

				//audio
				elseif($file_extension === 'mp3'){
					$this->file_mimetype = 'audio/mpeg';
				}
				elseif($file_extension === 'ogg'){
					$this->file_mimetype = 'audio/ogg';
				}
				elseif($file_extension === 'oga'){
					$this->file_mimetype = 'audio/vorbis';
				}
				elseif($file_extension === 'wma'){
					$this->file_mimetype = 'audio-/x-ms-wma';
				}

				//video
				//added missing mimetypes: m2v
				elseif( $file_extension === 'mp4' or $file_extension === 'mpe' or $file_extension === 'mpeg' or $file_extension === 'mpg' or $file_extension === 'mpga' or $file_extension === 'm2v'){
					$this->file_mimetype = 'video/mpeg';
				}
				elseif($file_extension === 'avi'){
					$this->file_mimetype = 'video/x-msvideo';
				}

				elseif($file_extension === 'qt' or $file_extension === 'mov'){
					$this->file_mimetype = 'video/quicktime';
				}
				elseif($file_extension === 'wmv'){
					$this->file_mimetype = 'video/x-ms-wmv';
				}

				//Added missing formats
				elseif($file_extension === '3gp'){
					$this->file_mimetype = 'video/3gpp';
				}
				elseif($file_extension === 'ogv'){
					$this->file_mimetype = 'video/ogg';
				}
				elseif($file_extension === 'flv'){
					$this->file_mimetype = 'video/x-flv';
				}

				elseif($file_extension === 'f4v'){
					$this->file_mimetype = 'video/x-f4v';
				}

				elseif($file_extension === 'm4v'){
					$this->file_mimetype = 'video/x-m4v';
				}

				elseif($file_extension === 'webm'){
					$this->file_mimetype = 'video/webm';
				}

				//applications
				elseif($file_extension === 'zip'){
					$this->file_mimetype = 'application/zip';
				}
				elseif($file_extension === 'pdf'){
					$this->file_mimetype = 'application/pdf';
				}
				elseif($file_extension === 'gz'){
					$this->file_mimetype = 'application/x-gzip';
				}
				elseif($file_extension === 'exe'){
					$this->file_mimetype = 'application/octet-stream';
				}
				elseif($file_extension === 'swf'){
					$this->file_mimetype = 'application/x-shockwave-flash';
				}
				//missing types
				elseif($file_extension === 'doc'){
					$this->file_mimetype = 'application/msword';
				}
				elseif($file_extension === 'docx'){
					$this->file_mimetype = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
				}
				elseif($file_extension === 'xls'){
					$this->file_mimetype = 'application/vnd.ms-excel';
				}
				elseif($file_extension === 'xlsx'){
					$this->file_mimetype = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
				}
				elseif($file_extension === 'ppt'){
					$this->file_mimetype = 'application/vnd.ms-powerpoint';
				}
				elseif($file_extension === 'pptx'){
					$this->file_mimetype = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
				}
				elseif($file_extension === 'txt'){
					$this->file_mimetype = 'text/plain';
				}
				elseif($file_extension === 'rar'){
					$this->file_mimetype = 'application/x-rar-compressed';
				}

				else {
					vmInfo (JText::sprintf ('COM_VIRTUEMART_MEDIA_SHOULD_HAVE_MIMETYPE', $name));
					$notice = TRUE;
				}
			//}
		}

		if ($ok) {
			return parent::check ();
		}
		else {
			return FALSE;
		}

	}

	/**
	 * We need a customised error handler to catch the errors maybe thrown by
	 * mime_content_type
	 *
	 * @author Max Milbers derived from Philippe Gerber
	 */
	function handleError ($errno, $errstr) {

		// error was suppressed with the @-operator
		if (0 === error_reporting ()) {
			return FALSE;
		}
		throw new ErrorException($errstr, 0);
		//echo 'I throw exception';
		//throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}

}
// pure php no closing tag
