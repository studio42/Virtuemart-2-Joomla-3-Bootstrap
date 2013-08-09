<?php
/**
* class Image2Thumbnail
* Thumbnail creation with PHP4 and GDLib (recommended, but not mandatory: 2.0.1 !)
*
*
* @author     Andreas Martens <heyn@plautdietsch.de>
* @author     Patrick Teague <webdude@veslach.com>
* @author     Soeren Eberhardt <soeren|at|virtuemart.net>
*@version	1.0b
*@date       modified 11/22/2004
*@modifications
*   - added support for GDLib < 2.0.1
*	- added support for reading gif images
*	- makes jpg thumbnails
*	- changed several groups of 'if' statements to single 'switch' statements
*   - commented out original code so modification could be identified.
*/

class Img2Thumb	{
// New modification
/**
*	private variables - do not use
*
*	@var int $bg_red				0-255 - red color variable for background filler
*	@var int $bg_green				0-255 - green color variable for background filler
*	@var int $bg_blue				0-255 - blue color variable for background filler
*	@var int $maxSize				0-1 - true/false - should thumbnail be filled to max pixels
*/
	var $bg_red;
	var $bg_green;
	var $bg_blue;
	var $maxSize;
	/**
	 * @var string Filename for the thumbnail
	 */
	var $fileout;

/**
*   Constructor - requires following vars:
*
*	@param string $filename			image path
*
*	These are additional vars:
*
*	@param int $newxsize			new maximum image width
*	@param int $newysize			new maximum image height
*	@param string $fileout			output image path
*	@param int $thumbMaxSize		whether thumbnail should have background fill to make it exactly $newxsize x $newysize
*	@param int $bgred				0-255 - red color variable for background filler
*	@param int $bggreen				0-255 - green color variable for background filler
*	@param int $bgblue				0-255 - blue color variable for background filler
*
*/
	function Img2Thumb($filename, $newxsize=60, $newysize=60, $fileout='',
		$thumbMaxSize=0, $bgred=0, $bggreen=0, $bgblue=0)
	{

		//Some big pictures need that
		$memory_limit = (int) substr(ini_get('memory_limit'),0,-1);
		if($memory_limit<128)  @ini_set( 'memory_limit', '128M' );

		//	New modification - checks color int to be sure within range
		if($thumbMaxSize)
		{
			$this->maxSize = true;
		}
		else
		{
			$this->maxSize = false;
		}
		if($bgred>=0 || $bgred<=255)
		{
			$this->bg_red = $bgred;
		}
		else
		{
			$this->bg_red = 0;
		}
		if($bggreen>=0 || $bggreen<=255)
		{
			$this->bg_green = $bggreen;
		}
		else
		{
			$this->bg_green = 0;
		}
		if($bgblue>=0 || $bgblue<=255)
		{
			$this->bg_blue = $bgblue;
		}
		else
		{
			$this->bg_blue = 0;
		}

		$this->NewImgCreate($filename,$newxsize,$newysize,$fileout);
	}

/**
*
*	private function - do not call
*
*/
	private function NewImgCreate($filename,$newxsize,$newysize,$fileout)
	{
// 		if( !function_exists('imagecreatefromjpeg') ){
// 			$app = JFactory::getApplication();
// 			$app->enqueueMessage('This server does NOT suppport auto generating Thumbnails by jpg');
// 		}

		$type = $this->GetImgType($filename);

		$pathinfo = pathinfo( $fileout );
		if( empty( $pathinfo['extension'])) {
			$fileout .= '.'.$type;
		}
		$this->fileout = $fileout;

		switch($type){

			case "gif":
				// unfortunately this function does not work on windows
				// via the precompiled php installation :(
				// it should work on all other systems however.
				if( function_exists("imagecreatefromgif") ) {
					$orig_img = imagecreatefromgif($filename);
				} else {
					$app = JFactory::getApplication();
					$app->enqueueMessage('This server does NOT suppport auto generating Thumbnails by gif');
					exit;
				}
				break;
			case "jpg":
				if( function_exists("imagecreatefromjpeg") ) {
					$orig_img = imagecreatefromjpeg($filename);
				} else {
					$app = JFactory::getApplication();
					$app->enqueueMessage('This server does NOT suppport auto generating Thumbnails by jpg');
					exit;
				}
				break;
			case "png":
				if( function_exists("imagecreatefrompng") ) {
					$orig_img = imagecreatefrompng($filename);
				} else {
					$app = JFactory::getApplication();
					$app->enqueueMessage('This server does NOT suppport auto generating Thumbnails by png');
					exit;
				}
				break;

		}

		$new_img =$this->NewImgResize($orig_img,$newxsize,$newysize,$filename);

		if (!empty($fileout))
		{
			 $this-> NewImgSave($new_img,$fileout,$type);
		}
		else
		{
			 $this->NewImgShow($new_img,$type);
		}

		ImageDestroy($new_img);
		ImageDestroy($orig_img);
	}

	/**
*	Maybe adding sharpening with
*            $sharpenMatrix = array
            (
                array(-1.2, -1, -1.2),
                array(-1, 20, -1),
                array(-1.2, -1, -1.2)
            );

            // calculate the sharpen divisor
            $divisor = array_sum(array_map('array_sum', $sharpenMatrix));

            $offset = 0;

            // apply the matrix
            imageconvolution($img, $sharpenMatrix, $divisor, $offset);
*
*	private function - do not call
*	includes function ImageCreateTrueColor and ImageCopyResampled which are available only under GD 2.0.1 or higher !
*/
	private function NewImgResize($orig_img,$newxsize,$newysize,$filename)
	{
		//getimagesize returns array
		// [0] = width in pixels
		// [1] = height in pixels
		// [2] = type
		// [3] = img tag "width=xx height=xx" values


		$orig_size = getimagesize($filename);

		$newxsize = (int)$newxsize;
		$newysize = (int)$newysize;
		if(empty($newxsize) and empty($newysize)){
			vmWarn('NewImgResize failed x,y = 0','NewImgResize failed x,y = 0');
			return false;
		}
		$maxX = $newxsize;
		$maxY = $newysize;

		if ($orig_size[0]<$orig_size[1])
		{
			$newxsize = (int)$newysize * ($orig_size[0]/$orig_size[1]);
			$adjustX = (int)($maxX - $newxsize)/2;
			$adjustY = 0;
		}
		else
		{
			$newysize = (int) $newxsize / ($orig_size[0]/$orig_size[1]);
			$adjustX = 0;
			$adjustY = (int)($maxY - $newysize)/2;
		}

		/* Original code removed to allow for maxSize thumbnails
		$im_out = ImageCreateTrueColor($newxsize,$newysize);
		ImageCopyResampled($im_out, $orig_img, 0, 0, 0, 0,
			$newxsize, $newysize,$orig_size[0], $orig_size[1]);
		*/

		//	New modification - creates new image at maxSize
		if( $this->maxSize )
		{
			if( function_exists("imagecreatetruecolor") )
			  $im_out = imagecreatetruecolor($maxX,$maxY);
			else
			  $im_out = imagecreate($maxX,$maxY);

			// Need to image fill just in case image is transparent, don't always want black background
			$bgfill = imagecolorallocate( $im_out, $this->bg_red, $this->bg_green, $this->bg_blue );

			if( function_exists( "imageAntiAlias" )) {
				imageAntiAlias($im_out,true);
			}
 		    imagealphablending($im_out, false);
		    if( function_exists( "imagesavealpha")) {
		    	imagesavealpha($im_out,true);
		    }
		    if( function_exists( "imagecolorallocatealpha")) {
		    	$transparent = imagecolorallocatealpha($im_out, 255, 255, 255, 127);
		    }

			//imagefill( $im_out, 0,0, $bgfill );
			if( function_exists("imagecopyresampled") ){
				ImageCopyResampled($im_out, $orig_img, $adjustX, $adjustY, 0, 0, $newxsize, $newysize,$orig_size[0], $orig_size[1]);
			}
			else {
				ImageCopyResized($im_out, $orig_img, $adjustX, $adjustY, 0, 0, $newxsize, $newysize,$orig_size[0], $orig_size[1]);
			}

		}
		else
		{

			if( function_exists("imagecreatetruecolor") )
			  $im_out = ImageCreateTrueColor($newxsize,$newysize);
			else
			  $im_out = imagecreate($newxsize,$newysize);

			if( function_exists( "imageAntiAlias" ))
			  imageAntiAlias($im_out,true);
 		    imagealphablending($im_out, false);
		    if( function_exists( "imagesavealpha"))
			  imagesavealpha($im_out,true);
		    if( function_exists( "imagecolorallocatealpha"))
			  $transparent = imagecolorallocatealpha($im_out, 255, 255, 255, 127);

			if( function_exists("imagecopyresampled") )
			  ImageCopyResampled($im_out, $orig_img, 0, 0, 0, 0, $newxsize, $newysize,$orig_size[0], $orig_size[1]);
			else
			  ImageCopyResized($im_out, $orig_img, 0, 0, 0, 0, $newxsize, $newysize,$orig_size[0], $orig_size[1]);
		}


		return $im_out;
	}

	/**
*
*	private function - do not call
*
*/
	private function NewImgSave($new_img,$fileout,$type)
	{
		if( !@is_dir( dirname($fileout))) {
			@mkdir( dirname($fileout) );
		}
		switch($type)
		{
			case "gif":
				if( !function_exists("imagegif") )
				{
					if (strtolower(substr($fileout,strlen($fileout)-4,4))!=".gif") {
						$fileout .= ".png";
					}
					return imagepng($new_img,$fileout);

				}
				else {
					if (strtolower(substr($fileout,strlen($fileout)-4,4))!=".gif") {
						$fileout .= '.gif';
					}
					return imagegif( $new_img, $fileout );

				}
				break;
			case "jpg":
				if (strtolower(substr($fileout,strlen($fileout)-4,4))!=".jpg")
					$fileout .= ".jpg";
				return imagejpeg($new_img, $fileout, 100);
				break;
			case "png":
				if (strtolower(substr($fileout,strlen($fileout)-4,4))!=".png")
					$fileout .= ".png";
				return imagepng($new_img,$fileout);
				break;
		}
	}

	/**
*
*	private function - do not call
*
*/
	private function NewImgShow($new_img,$type)
	{
		/* Original code removed in favor of 'switch' statement
		if ($type=="png")
		{
			header ("Content-type: image/png");
			 return imagepng($new_img);
		}
		if ($type=="jpg")
		{
			header ("Content-type: image/jpeg");
			 return imagejpeg($new_img);
		}
		*/
		switch($type)
		{
			case "gif":
				if( function_exists("imagegif") )
				{
					header ("Content-type: image/gif");
					return imagegif($new_img);
					break;
				}
				//either there is missing a break or the else $this->NewImgShow is unecessary
				else
					$this->NewImgShow( $new_img, "jpg" );

			case "jpg":
				header ("Content-type: image/jpeg");
				return imagejpeg($new_img);
				break;
			case "png":
				header ("Content-type: image/png");
				return imagepng($new_img);
				break;
		}
	}

	/**
*
*	private function - do not call
*
*   1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF,
*   5 = PSD, 6 = BMP,
*   7 = TIFF(intel byte order),
*   8 = TIFF(motorola byte order),
*   9 = JPC, 10 = JP2, 11 = JPX,
*   12 = JB2, 13 = SWC, 14 = IFF
*/
	private function GetImgType($filename)
	{
		$info = getimagesize($filename);
		/* Original code removed in favor of 'switch' statement
		if($size[2]==2)
			return "jpg";
		elseif($size[2]==3)
			return "png";
		*/
		switch($info[2]) {
			case 1:
				return "gif";
				break;
			case 2:
				return "jpg";
				break;
			case 3:
				return "png";
				break;
			default:
				return false;
		}
	}

}