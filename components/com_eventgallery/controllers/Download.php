<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

require_once JPATH_ROOT.'/components/com_eventgallery/config.php';
require_once 'Resizeimage.php';

class DownloadController extends ResizeimageController
{
    public function display($cachable = false, $urlparams = array())
    {
        /**
         * @var JApplicationSite $app
         */
        $app = JFactory::getApplication();
        $params = $app->getParams();

        $str_folder = JRequest::getVar('folder', null);
        $str_file = JRequest::getVar('file', null);

        /**
         * @var EventgalleryLibraryManagerFile $fileMgr
         */
        $fileMgr = EventgalleryLibraryManagerFile::getInstance();

        $file = $fileMgr->getFile($str_folder, $str_file);

        if (!is_object($file) || !$file->isPublished()) {
            JError::raiseError(404, JText::_('COM_EVENTGALLERY_SINGLEIMAGE_NO_PUBLISHED_MESSAGE'));
        }

        $folder = $file->getFolder();

        if (!$folder->isPublished() || !$folder->isVisible()) {
            JError::raiseError(404, JText::_('COM_EVENTGALLERY_EVENT_NO_PUBLISHED_MESSAGE'));
        }

        // deny downloads if the social sharing option is disabled
        if (    $params->get('use_social_sharing_button', 0)==0  ) {
            JError::raiseError(404, JText::_('COM_EVENTGALLERY_FILE_NOT_DOWNLOADABLE_MESSAGE'));    
        } 
                
        // allow the download if at least one sharing type is enabled both global and for the event
        if (        
                ($params->get('use_social_sharing_facebook', 0)==1 && $folder->getAttribs()->get('use_social_sharing_facebook', 1)==1)
            ||  ($params->get('use_social_sharing_google', 0)==1 && $folder->getAttribs()->get('use_social_sharing_google', 1)==1)
            ||  ($params->get('use_social_sharing_twitter', 0)==1 && $folder->getAttribs()->get('use_social_sharing_twitter', 1)==1)
            ||  ($params->get('use_social_sharing_pinterest', 0)==1 && $folder->getAttribs()->get('use_social_sharing_pinterest', 1)==1)
            ||  ($params->get('use_social_sharing_email', 0)==1 && $folder->getAttribs()->get('use_social_sharing_email', 1)==1)
            ||  ($params->get('use_social_sharing_download', 0)==1 && $folder->getAttribs()->get('use_social_sharing_download', 1)==1)
            
            ) {
        	
        } else {
            JError::raiseError(404, JText::_('COM_EVENTGALLERY_FILE_NOT_DOWNLOADABLE_MESSAGE'));    
        }



        $basename = COM_EVENTGALLERY_IMAGE_FOLDER_PATH . $folder->getFolderName() . '/';
        $filename = $basename . $file->getFileName();

        if ($params->get('download_original_images', 0)==1 ) {
            $mime = ($mime = getimagesize($filename)) ? $mime['mime'] : $mime;
            $size = filesize($filename);
            $fp   = fopen($filename, "rb");
            if (!($mime && $size && $fp)) {
                // Error.
                return;
            }


            header("Content-type: " . $mime);
            header("Content-Length: " . $size);
            header("Content-Disposition: attachment; filename=" . $file->getFileName());
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            fpassthru($fp);
            die();
        } else {
            header("Content-Disposition: attachment; filename=" . $file->getFileName());
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            $this->resize($file->getFolderName(), $file->getFileName(), COM_EVENTGALLERY_IMAGE_ORIGINAL_MAX_WIDTH , null, null);
            die();
        }

    }

}
