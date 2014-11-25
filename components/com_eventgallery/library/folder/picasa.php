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


class EventgalleryLibraryFolderPicasa extends EventgalleryLibraryFolder
{

    /**
     * the picasa album data container
     */
    protected $_album;

    /**
     * $creates the folder object.
     */
    function __construct($foldername)
    {
        parent::__construct($foldername);
    }

    /**
     * defines if this class can handle the given folder
     *
     * @param $foldername
     * @return bool
     */
    public static function canHandle($foldername) {

        if (strpos($foldername,'@' ) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Returns the number of files in this album.
     *
     * @param bool $publishedOnly
     * @return int
     */
    public function getFileCount($publishedOnly = true) {
        return $this->getAlbum()->overallCount;
    }

    /**
     * @param int $limitstart
     * @param int $limit
     * @param int $imagesForEvents if true load the main images at the first position
     * @return array
     */
    public function getFiles($limitstart = 0, $limit = 0, $imagesForEvents = 0) {

        if ($limitstart < 0) {
            $limitstart = 0;
        }

        $album = $this->getAlbum();

        $entries = $album->photos;

        if ( $imagesForEvents && $album->overallCount>0) {
            array_unshift($entries, new EventgalleryLibraryFilePicasa($album));
        }

        $entries = $limit > 0 ? array_slice($entries, $limitstart, $limit) : $entries;

        $result = Array();
        foreach($entries as $entry) {
            if (is_array($entry)) {
                $result[] = new EventgalleryLibraryFilePicasa($entry['folder'], $entry['file']);
            } else {
                $result[] = $entry;
            }
        }

        return $result;

    }

    /**
     * returns the picasa key
     *
     * @return string
     */
    public function getPicasaKey() {
        if ($this->_folder == null) {
            return "";
        }
        return $this->_folder->picasakey;
    }

    /**
     * returns the picasa user
     *
     * @return string
     */
    public function getUserId() {
        $values = explode("@", $this->_foldername, 2);
        return $values[0];
    }

    /**
     * returns the picasa album id
     *
     * @return string
     */
    public function getAlbumId() {
        $values = explode("@", $this->_foldername, 2);
        return $values[1];
    }

    /**
     * Returns the parsed picasa album object.
     *
     * @return object
     */
    public function getAlbum() {
        if ($this->_album == NULL) {
            $this->_album = EventgalleryHelpersImageHelper::picasaweb_ListAlbum($this->getUserId(), $this->getAlbumId(), $this->getPicasaKey());
        }

        return $this->_album;
    }

    /**
     * @return bool
     */
    public function isCommentingAllowed() {
        return true;
    }

    public static function syncFolder($foldername) {
        return EventgalleryLibraryManagerFolder::$SYNC_STATUS_NOSYNC;
    }

    public static function addNewFolders() {

    }

    public static function getFileHandlerClassname() {
        return 'EventgalleryLibraryFilePicasa';
    }
}
