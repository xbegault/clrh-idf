<?php

/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class EventgalleryLibraryManagerFolder extends  EventgalleryLibraryManagerManager
{

    public static $SYNC_STATUS_NOSYNC = 0;
    public static $SYNC_STATUS_SYNC = 1;
    public static $SYNC_STATUS_DELTED = 2;


    protected $_folders;
    protected $_commentCount;

    protected $_folderTypes;

    public function __construct() {
        $this->_folderTypes = Array('EventgalleryLibraryFolderPicasa', 'EventgalleryLibraryFolderLocal');
    }

    /**
     * Returns a folder
     *
     * @param $foldername string|object
     * @return EventgalleryLibraryFolder
     */
    public function getFolder($foldername) {

        if (is_object($foldername)) {
            $currentFolder = $foldername->folder;
        } else {
            $currentFolder = $foldername;
        }


        if (!isset($this->_folders[$currentFolder])) {

            foreach($this->_folderTypes as $folderType) {
                $folderClass = $folderType;
                /**
                 * @var EventgalleryLibraryFolder $folderClass
                 * */
                if ($folderClass::canHandle($currentFolder)) {
                    $this->_folders[$currentFolder] = new $folderClass($foldername);
                }

            }

        }

        return $this->_folders[$currentFolder];
    }

    /**
     * returns the name of the file handler class for the given folder name
     *
     * @param $foldername string
     * @return null|string
     */
    public function getFileHandlerClassname($foldername) {
        foreach($this->_folderTypes as $folderType) {
            $folderClass = $folderType;
            /**
             * @var EventgalleryLibraryFolder $folderClass
             * */
            if ($folderClass::canHandle($foldername)) {
                return $folderClass::getFileHandlerClassname();
            }

        }

        return null;
    }

    public function getCommentCount($foldername)
    {
        if (!$this->_commentCount)
        {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true)
                ->select('folder, count(1) AS '.$db->quoteName('commentCount'))
                ->from($db->quoteName('#__eventgallery_comment'))
                ->where('published=1')
                ->group('folder');
            $db->setQuery($query);
            $comments = $db->loadObjectList();
            $this->_commentCount = array();
            foreach($comments as $comment)
            {
                $this->_commentCount[$comment->folder] = $comment->commentCount;
            }
        }

        if (isset($this->_commentCount[$foldername])) {
            return $this->_commentCount[$foldername];
        }

        return 0;
    }

    /**
     * scans the main dir and adds new folders to the database
     * Does not add Files!
     */
    public function addNewFolders() {

        foreach($this->_folderTypes as $folderType) {
            $folderClass = $folderType;
            /**
             * @var EventgalleryLibraryFolder $folderClass
             * */
            $folderClass::addNewFolders();
        }
    }

    /**
     * Syncs a folder. Includes deletion and adding/removing files
     *
     * return values:
     *
     * synced
     * notsynced
     * deleted
     *
     * @param $foldername string
     * @return string
     */
    public function syncFolder($foldername) {
        foreach($this->_folderTypes as $folderType) {
            $folderClass = $folderType;
            /**
             * @var EventgalleryLibraryFolder $folderClass
             * */
            if ($folderClass::canHandle($foldername)) {
                return $folderClass::syncFolder($foldername);
            }

        }

        return EventgalleryLibraryManagerFolder::$SYNC_STATUS_NOSYNC;
    }



}
