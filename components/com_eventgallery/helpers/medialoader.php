<?php

/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class EventgalleryHelpersMedialoader
{

    static function load()
    {

        $document = JFactory::getDocument();

        JHtml::_('behavior.framework', true);
        JHtml::_('behavior.formvalidation');

        $params = JComponentHelper::getParams('com_eventgallery');
        
        $doDebug = $params->get('debug',0)==1;
        $doManualDebug = JRequest::getString('debug', '') == 'true';


        // load script and styles in debug mode or compressed
        if ($doDebug || $doManualDebug) {
        

            $CSSs = Array('eventgallery.css', 
                          'mediaboxAdvBlack21.css');
            
           

            $joomlaVersion =  new JVersion();
            if (!$joomlaVersion->isCompatible('3.0')) {
                array_push($CSSs, 'legacy.css');
            } 

            $JSs = Array(                
                'SizeCalculator.js',
                'EventgalleryToolbox.js',
                'EventgalleryImage.js',
                'EventgalleryRow.js',
                'EventgalleryImageList.js',
                'EventgalleryEventsList.js',
                'EventgalleryEventsTiles.js',
                'EventgalleryGridCollection.js',
                'EventgalleryTilesCollection.js',
                'EventgalleryCart.js',                
                'SocialShareButton.js',
                'mediaboxAdv-1.3.4b.js',
                'JSGallery2.js',
                'LazyLoad.js',
            );

        } else {
            
            $joomlaVersion =  new JVersion();
            if (!$joomlaVersion->isCompatible('3.0')) {
                $CSSs = Array('eg-l-compressed.css');
            } else {
                $CSSs = Array('eg-compressed.css');
            }
            
            $JSs = Array('eg-compressed.js');

        }

        foreach($CSSs as $css) {
            $script = JURI::base() . 'components/com_eventgallery/media/css/'.$css.'?v=' . EVENTGALLERY_VERSION;
            $document->addStyleSheet($script);
        }

        foreach($JSs as $js) {
            $script = JURI::base() . 'components/com_eventgallery/media/js/'.$js.'?v=' . EVENTGALLERY_VERSION;
            $document->addScript($script);
        }


    }


}

	
	
