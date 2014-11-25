<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
require_once JPATH_BASE . '/components/com_eventgallery/helpers/vendors/class.jpeg_icc.php';
/* Load the required PEL files for handling JPEG images. */
require_once JPATH_BASE.'/components/com_eventgallery/helpers/vendors/pel/src/PelJpeg.php';
require_once JPATH_ROOT.'/components/com_eventgallery/config.php';


class ResizeimageController extends JControllerLegacy
{
    public function display($cachable = false, $urlparams = array()) {
        $file = JRequest::getString('file');
        $folder = JRequest::getString('folder');

        $width = JRequest::getInt('width', -1);
        $height = JRequest::getInt('height', -1);

        $mode = JRequest::getString('mode', 'nocrop');

        $this->resize($folder, $file, $width, $height, $mode);
    }

    /**
     * This method calculates the image and delivers it to the client.
     *
     * @param $folder
     * @param $file
     * @param $width
     * @param $height
     * @param $mode
     */
    public function resize($folder, $file, $width = -1, $height = -1, $mode = 'nocrop')
    {

        $jpeg_orientation_translation = Array(
            1 => 0,
            2 => 0,
            3 => 180,
            4 => 0,
            5 => 0,
            6 => -90,
            7 => 0,
            8 => 90
        );


        /**
         * @var JApplicationSite $app
         */
        $app = JFactory::getApplication();
        $params = $app->getParams();


        if (strcmp($mode, 'full') == 0) {
            $mode = 'nocrop';
            $width = COM_EVENTGALLERY_IMAGE_ORIGINAL_MAX_WIDTH;
            $height = COM_EVENTGALLERY_IMAGE_ORIGINAL_MAX_WIDTH;
        }

        if ($height > $width) {
            $width = $height;
        }

        $sizeSet = new EventgalleryHelpersSizeset();
        $saveAsSize = $sizeSet->getMatchingSize($width);


        $file = STR_REPLACE("\.\.", "", $file);
        $folder = STR_REPLACE("\.\.", "", $folder);
        $width = STR_REPLACE("\.\.", "", $width);
        $mode = STR_REPLACE("\.\.", "", $mode);

        $file = STR_REPLACE("/", "", $file);
        $folder = STR_REPLACE("/", "", $folder);
        $width = STR_REPLACE("/", "", $width);
        $mode = STR_REPLACE("/", "", $mode);

        $file = STR_REPLACE("\\", "", $file);
        $folder = STR_REPLACE("\\", "", $folder);
        $width = STR_REPLACE("\\", "", $width);
        $mode = STR_REPLACE("\\", "", $mode);


        $basedir = COM_EVENTGALLERY_IMAGE_FOLDER_PATH;
            
        $sourcedir = $basedir . $folder;
        $cachebasedir = COM_EVENTGALLERY_IMAGE_CACHE_PATH;
        $cachedir = $cachebasedir . $folder;
        $cachedir_thumbs = $cachebasedir . $folder;

        if (!is_dir(JPATH_CACHE)) {
            //mkdir($cachebasedir, 0777);
            mkdir(JPATH_CACHE);

        }

        if (!is_dir($cachebasedir)) {
            //mkdir($cachebasedir, 0777);
            mkdir($cachebasedir);

        }

        if (!is_dir($cachedir)) {
            //mkdir($cachedir, 0777);
            mkdir($cachedir);
        }

        if (!is_dir($cachedir_thumbs)) {
            //mkdir($cachedir_thumbs, 0777);
            mkdir($cachedir_thumbs);

        }

        $image_file = $sourcedir . DIRECTORY_SEPARATOR . $file;
        $image_thumb_file = $cachedir_thumbs . DIRECTORY_SEPARATOR . $mode . $saveAsSize . $file;
        //$last_modified = gmdate('D, d M Y H:i:s T', filemtime ($image_file));
        $last_modified = gmdate('D, d M Y H:i:s T', mktime(0, 0, 0, 1, 1, 2100));
        #echo "<br>".$image_thumb_file."<br>";

        $debug = false;

        if ($debug || !file_exists($image_thumb_file)) {

            $ext = pathinfo($image_file, PATHINFO_EXTENSION);;
            $input_jpeg = null;
            $exif = null;


            if (strtolower($ext) == "gif") {
                if (!$im_original = imagecreatefromgif($image_file)) {
                    echo "Error opening $image_file!"; exit;
                }
            } else if(strtolower($ext) == "jpg" || strtolower($ext) == "jpeg") {

                // try to use PEL first. If things fail, use the php internal method to get the JPEG
                try {
                    $input_jpeg = new PelJpeg($image_file);

                    /* Retrieve the original Exif data in $jpeg (if any). */
                    $exif = $input_jpeg->getExif();


                    /* The input image is already loaded, so we can reuse the bytes stored
                     * in $input_jpeg when creating the Image resource. */
                    if (!$im_original = ImageCreateFromString($input_jpeg->getBytes())) {
                        echo "Error opening $image_file!"; exit;
                    }
                } catch (Exception $e){
                    if (!$im_original = imagecreatefromjpeg($image_file)) {
                        echo "Error opening $image_file!"; exit;
                    }
                }
                
            } else if(strtolower($ext) == "png") {
                if (!$im_original = imagecreatefrompng($image_file)) {
                    echo "Error opening $image_file!"; exit;
                }
            } else {
                die("$ext not supported");
            }

            if ($params->get('use_autorotate', 1)==1 && $exif!=NULL) {
                $tiff = $exif->getTiff();
                $ifd0 = $tiff->getIfd();
                $orientation = $ifd0->getEntry(PelTag::ORIENTATION);
                if ($orientation != null) {
                    $im_original = imagerotate($im_original, $jpeg_orientation_translation[$orientation->getValue()], 0);
                    $orientation->setValue(1);
                }

            }


            $orig_width = imagesx($im_original);
            $orig_height = imagesy($im_original);
            $orig_ratio = imagesx($im_original) / imagesy($im_original);

            $sizeCalc = new EventgalleryHelpersSizecalculator($orig_width, $orig_height, $width,
                strcmp('crop', $mode) == 0);
            $height = $sizeCalc->getHeight();
            $width = $sizeCalc->getWidth();
            //print_r($sizeCalc);
            // create canvas/border image

            //adjust height to not enlarge images
            if ($width > $orig_width) {
                $width = $orig_width;
            }

            if ($height > $orig_height) {
                $height = $orig_height;
            }

            if (strcmp('crop', $mode) != 0) {
                $canvasWidth = $width;
                $canvasHeight = ceil($width / $orig_ratio);

                if ($canvasHeight > $height) {
                    $canvasHeight = $height;
                    $canvasWidth = ceil($height * $orig_ratio);
                }

                $width = $canvasWidth;
                $height = $canvasHeight;
            } else {
                $height = $width;
            }

            $im_output = imagecreatetruecolor($width, $height);

            $resize_faktor = $orig_height / $height;
            $new_height = $height;
            $new_width = $orig_width / $resize_faktor;

            if ($new_width < $width) {
                $resize_faktor = $orig_width / $width;
                $new_width = $width;
                $new_height = $orig_height / $resize_faktor;
            }

        
            imagecopyresampled($im_output, $im_original,
                                 ($width/2)-($new_width/2),
                                 ($height/2)-($new_height/2),
                                 0,0,
                                 $new_width,$new_height,$orig_width,$orig_height);
            
            $use_sharpening = $params->get('use_sharpening',1);

            if ($use_sharpening==1) {
	            // configure the sharpening
	            $stringSharpenMatrix = $params->get('image_sharpenMatrix','[[-1,-1,-1],[-1,16,-1],[-1,-1,-1]]');

	        	$sharpenMatrix = json_decode($stringSharpenMatrix);
	        	if (null == $sharpenMatrix || count($sharpenMatrix)!=3) {
		            $sharpenMatrix = array(
		                                 array(-1,-1,-1),
		                                 array(-1,16,-1),
		                                 array(-1,-1,-1)
		                                 );
	        	}

	           $divisor = array_sum(array_map('array_sum', $sharpenMatrix));
	            $offset = 0;
	            
	            if (function_exists('imageconvolution'))
	            {
                    if (version_compare(phpversion(), '5.5.9', '=')) {
                        $this->imageconvolution($im_output, $sharpenMatrix, $divisor, $offset);
                    } else {
                        imageconvolution($im_output, $sharpenMatrix, $divisor, $offset);
                    }
	                
	            
	            }   
        	}

            /**
             * @var EventgalleryLibraryManagerFolder $folderMgr
             * @var EventgalleryLibraryFolder $folder
             */
            $folderMgr = EventgalleryLibraryManagerFolder::getInstance();
            $folder = $folderMgr->getFolder($folder);
            $watermark = $folder->getWatermark();
            if ( null != $watermark && $watermark->isPublished() ) {

                $watermark->addWatermark($im_output);

            }

			$image_quality = $params->get('image_quality',85);
			if ($input_jpeg != null) {
				Pel::setJPEGQuality($image_quality);
				/* We want the raw JPEG data from $scaled. Luckily, one can create a
				 * PelJpeg object from an image resource directly: */
				$output_jpeg = new PelJpeg($im_output);

				/* If no Exif data was present, then $exif is null. */
				if ($exif != null)
				  $output_jpeg->setExif($exif);

				/* We can now save the scaled image. */
				$writeSuccess = true;
				$output_jpeg->saveFile($image_thumb_file);
			} else {

	            $writeSuccess = imagejpeg($im_output,$image_thumb_file, $image_quality);     
	            if (!$writeSuccess) {
	            	die("Unable to write to file $image_thumb_file");
	            }
	        }       
            

            if (!$writeSuccess) {
                die("Unable to write to file $image_thumb_file");
            }

            $time = time() + 315360000;
            touch($image_thumb_file, $time);

            // add the ICC profile
            try {
                $o = new JPEG_ICC();
                $o->LoadFromJPEG($image_file);
                $o->SaveToJPEG($image_thumb_file);
            } catch (Exception $e) {

            }

        }

        if (!$debug) {
            header("Last-Modified: $last_modified");
            header("Content-Type: image/jpeg");

        }

        echo readfile($image_thumb_file);
        $app->close();
    }


    //include this file whenever you have to use imageconvolution...
    //you can use in your project, but keep the comment below :)
    //great for any image manipulation library
    //Made by Chao Xu(Mgccl) 2/28/07
    //www.webdevlogs.com
    //V 1.0
    
    protected function imageconvolution($src, $filter, $filter_div, $offset){
        if ($src==NULL) {
            return 0;
        }
        
        $sx = imagesx($src);
        $sy = imagesy($src);
        $srcback = ImageCreateTrueColor ($sx, $sy);
        ImageCopy($srcback, $src,0,0,0,0,$sx,$sy);
        
        if($srcback==NULL){
            return 0;
        }
            
        #FIX HERE
        #$pxl array was the problem so simply set it with very low values
        $pxl = array(1,1);
        #this little fix worked for me as the undefined array threw out errors

        for ($y=0; $y<$sy; ++$y){
            for($x=0; $x<$sx; ++$x){
                $new_r = $new_g = $new_b = 0;
                $alpha = imagecolorat($srcback, $pxl[0], $pxl[1]);
                $new_a = $alpha >> 24;
                
                for ($j=0; $j<3; ++$j) {
                    $yv = min(max($y - 1 + $j, 0), $sy - 1);
                    for ($i=0; $i<3; ++$i) {
                            $pxl = array(min(max($x - 1 + $i, 0), $sx - 1), $yv);
                        $rgb = imagecolorat($srcback, $pxl[0], $pxl[1]);
                        $new_r += (($rgb >> 16) & 0xFF) * $filter[$j][$i];
                        $new_g += (($rgb >> 8) & 0xFF) * $filter[$j][$i];
                        $new_b += ($rgb & 0xFF) * $filter[$j][$i];
                    }
                }

                $new_r = ($new_r/$filter_div)+$offset;
                $new_g = ($new_g/$filter_div)+$offset;
                $new_b = ($new_b/$filter_div)+$offset;

                $new_r = ($new_r > 255)? 255 : (($new_r < 0)? 0:$new_r);
                $new_g = ($new_g > 255)? 255 : (($new_g < 0)? 0:$new_g);
                $new_b = ($new_b > 255)? 255 : (($new_b < 0)? 0:$new_b);

                $new_pxl = ImageColorAllocateAlpha($src, (int)$new_r, (int)$new_g, (int)$new_b, $new_a);
                if ($new_pxl == -1) {
                    $new_pxl = ImageColorClosestAlpha($src, (int)$new_r, (int)$new_g, (int)$new_b, $new_a);
                }
                if (($y >= 0) && ($y < $sy)) {
                    imagesetpixel($src, $x, $y, $new_pxl);
                }
            }
        }
        imagedestroy($srcback);
        return 1;
    }
    
}


