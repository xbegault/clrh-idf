<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


//the name of the class must be the name of your component + InstallerScript
//for example: com_contentInstallerScript for com_content.
class com_eventgalleryInstallerScript
{
    /*
    * $parent is the class calling this method.
    * $type is the type of change (install, update or discover_install, not uninstall).
    * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
    * If preflight returns false, Joomla will abort the update and undo everything already done.
    */
    function preflight( $type, $parent ) {

        $folders = array(  
            JPATH_ROOT . '/administrator/components/com_eventgallery/controllers',
            #JPATH_ROOT . '/administrator/components/com_eventgallery/doc',
            JPATH_ROOT . '/administrator/components/com_eventgallery/media',
            JPATH_ROOT . '/administrator/components/com_eventgallery/models',
            JPATH_ROOT . '/administrator/components/com_eventgallery/views',
            JPATH_ROOT . '/administrator/components/com_eventgallery/sql',
            JPATH_ROOT . '/components/com_eventgallery/controllers',
            JPATH_ROOT . '/components/com_eventgallery/helpers',
            JPATH_ROOT . '/components/com_eventgallery/language',
            JPATH_ROOT . '/components/com_eventgallery/library',
            JPATH_ROOT . '/components/com_eventgallery/media',
            JPATH_ROOT . '/components/com_eventgallery/models',
            JPATH_ROOT . '/components/com_eventgallery/tests',
            JPATH_ROOT . '/components/com_eventgallery/views'
        );

        $files = array(
            JPATH_ROOT . '/language/en-GB/en-GB.com_eventgallery.ini',
            JPATH_ROOT . '/language/de-DE/de-DE.com_eventgallery.ini',
            JPATH_ROOT . '/administrator/language/en-GB/en-GB.com_eventgallery.ini',
            JPATH_ROOT . '/administrator/language/en-GB/en-GB.com_eventgallery.sys.ini'
        );

        foreach($folders as $folder) {
            if (JFolder::exists($folder)) {
                JFolder::delete($folder);
            }
        }

        foreach($files as $file) {
            if (JFolder::exists($file)) {
                JFolder::delete($file);
            }
        }
    }   
    
}