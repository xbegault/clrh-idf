<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');


class EventgalleryViewWatermarkpreview extends JViewLegacy
{

    function display($tpl=null)
    {
        $image_file = JPATH_BASE . '/components/com_eventgallery/media/img/watermark-small.jpg';
        
        $im_original = imagecreatefromjpeg($image_file);

        /**
         * @var EventgalleryLibraryManagerWatermark $watermarkMgr
         * @var EventgalleryLibraryWatermark $watermark
         */
        $watermarkMgr = EventgalleryLibraryManagerWatermark::getInstance();

        $watermark = $watermarkMgr->getWatermark(JRequest::getInt('id'));
        if (null != $watermark) {
            $watermark->addWatermark($im_original);
        }

        header("Content-Type: image/jpeg");

        imagejpeg($im_original);
        
        $app->close();
    }
}
