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


abstract class EventgalleryLibraryFolder extends EventgalleryLibraryDatabaseObject
{

    /**
     * @var string
     */
    protected $_foldername = NULL;

    /**
     * @var TableFolder
     */
    protected $_folder = NULL;

    /**
     * @var EventgalleryLibraryImagetypeset
     */
    protected $_imagetypeset = NULL;

    protected $_filecount = NULL;

    protected $_attribs = NULL;



    /**
     * $creates the lineitem object. $dblineitem is the database object of this line item
     */
    public function __construct($foldername)
    {
        if (is_object($foldername)) {
            $this->_folder = $foldername;
            $foldername = $this->_folder->folder;
        }

        $this->_foldername = $foldername;
        if ($this->_folder == null) {
            $this->_loadFolder();
        }
        parent::__construct();
    }


    /**
     * use this method to sync new folders to the database
     */
    public static function addNewFolders() {

    }

    /**
     * defines if this class can handle the given folder
     *
     * @param $folder
     * @return bool
     */
    public static function canHandle($folder) {
        return false;
    }

    public static function getFileHandlerClassname() {
        return null;
    }

    /**
     * loads a folder from the databas
     */
    protected function _loadFolder()
    {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__eventgallery_folder');
        $query->where('folder=' . $db->Quote($this->_foldername));

        $db->setQuery($query);
        $folderObject = $db->loadObject();

        $this->_folder = $folderObject;

        if ($this->_folder == null) {
            return;
        }
        /**
         * @var EventgalleryLibraryManagerImagetypeset $imagetypesetMgr
         */
        $imagetypesetMgr = EventgalleryLibraryManagerImagetypeset::getInstance();

        if ($this->_folder->imagetypesetid == null) {
            $this->_imagetypeset = $imagetypesetMgr->getDefaultImageTypeSet(true);
        } else {
            $this->_imagetypeset = $imagetypesetMgr->getImageTypeSet($this->_folder->imagetypesetid);
            if (!$this->_imagetypeset->isPublished()) {
                $this->_imagetypeset = $imagetypesetMgr->getDefaultImageTypeSet(true);
            }
        }

    }

    /**
     * @return string
     */
    public function getFolderName()
    {
        return $this->_folder->folder;
    }

    /**
     * @return EventgalleryLibraryImagetypeset
     */
    public function getImageTypeSet()
    {
        return $this->_imagetypeset;
    }

    /**
     * @return bool
     */
    public function isCartable()
    {
        return $this->_folder->cartable == 1;
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        if (!isset($this->_folder)) {
            return false;
        }
        return $this->_folder->published == 1;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_folder->password;
    }

    public function getUserGroupIds()
    {
        return $this->_folder->usergroupids;
    }

    /**
     * returns a set of attributes
     *
     * @return JRegistry
     */
    public function getAttribs() {

        if ($this->_attribs == NULL) {
            $registry = new JRegistry;
            $registry->loadString($this->_folder->attribs);
            $this->_attribs = $registry;
        }

        return $this->_attribs;
    }

    /**
     * @return bool
     */
    public function isAccessible()
    {

        if (strlen($this->getPassword()) > 0) {
            $session = JFactory::getSession();
            $unlockedFoldersJson = $session->get("eventgallery_unlockedFolders", "");

            $unlockedFolders = array();
            if (strlen($unlockedFoldersJson) > 0) {
                $unlockedFolders = json_decode($unlockedFoldersJson, true);
            }

            if (!in_array($this->_foldername, $unlockedFolders)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns false if the current user is not allowed to see this item
     *
     * @return bool
     */
    public function isVisible() {
        $user = JFactory::getUser();

        $params = JComponentHelper::getParams('com_eventgallery');
        $minUserGroups = $params->get('eventgallery_default_usergroup');

        // if no user groups are set at all
        if (strlen($this->getUserGroupIds())==0 && count($minUserGroups)==0 ) {
            return true;
        }

        // use the default usergroups if the folder does not define any
        if (strlen($this->getUserGroupIds())==0) {
            $folderUserGroups = $minUserGroups;
        } else {
            $folderUserGroups = explode(',', $this->getUserGroupIds());
        }

        // if the public user group is part of the folder user groups
        if (in_array(1, $folderUserGroups)) {
            return true;
        }



        $userUserGroups = JUserHelper::getUserGroups($user->id);
        foreach($userUserGroups as $userUserGroup) {

            if (count(array_intersect(EventgalleryHelpersUsergroups::getGroupPath($userUserGroup), $folderUserGroups))>0 ) {
                return true;
            }
        }

        return false;
    }

    /**
     * returns the text for the folder.
     *
     * @return String
     */
    public function getText() {
        $splittedText = EventgalleryHelpersTextsplitter::split($this->_folder->text);
        return $splittedText->fulltext;
    }

    /**
     * returns the intro text for the folder if there is a splitter in the text.
     * Otherwise the introtext is the same as the text.
     *
     * @return String
     */
    public function getIntroText() {

        $splittedText = EventgalleryHelpersTextsplitter::split($this->_folder->text);
        return $splittedText->introtext;
    }

    /**
     * Returns the description of this folder
     *
     * @return string
     */
    public function getDescription() {
        return $this->_folder->description;
    }

    /**
     * returns the date field
     *
     * @return string
     */
    public function getDate() {
        return $this->_folder->date;
    }

    /**
     * returns the number of comments for an event.
     *
     * @return mixed
     */
    public function getCommentCount() {

        /**
         * @var EventgalleryLibraryManagerFolder $folderMgr
         */
        $folderMgr = EventgalleryLibraryManagerFolder::getInstance();
        return $folderMgr->getCommentCount($this->_foldername);

    }

    /**
     * returns the number of files in this folder
     *
     * @param bool $publishedOnly defines is the return value contains unpublished files.
     * @return int
     */
    public function getFileCount($publishedOnly = true) {

        // this value might be part of a sql query
        if (isset($this->_folder->overallCount)) {
            return $this->_folder->overallCount;
        }

        if ($this->_filecount === NULL) {

            $db = JFactory::getDBO();
            $query = $db->getQuery(true)
                ->select('count(1)')
                ->from($db->quoteName('#__eventgallery_file') . ' AS file')
                ->where('folder='.$db->quote($this->_foldername))
                ->where('(file.ismainimageonly IS NULL OR file.ismainimageonly=0)');
            if ($publishedOnly) {
                $query->where('file.published=1');
            }
            $db->setQuery( $query );
            $this->_filecount = $db->loadResult();

        }

        return $this->_filecount;

    }

    /**
     * @param int $limitstart
     * @param int $limit
     * @param int $imagesForEvents if true load the main images at the first position
     * @return array
     */
    public abstract function getFiles($limitstart = 0, $limit = 0, $imagesForEvents = 0);


    public function getFolderTags() {
        return $this->_folder->foldertags;
    }

    /**
     * @return bool
     */
    public function isCommentingAllowed() {
        return true;
    }


    /**
     * syncs a folder with the used data structure
     *
     * @param $foldername string
     */
    public static function syncFolder($foldername) {

    }

    /**
     * returns the watermark object for this folder
     *
     * @return EventgalleryLibraryWatermark|null
     */
    public function getWatermark() {

        /**
         * @var EventgalleryLibraryManagerWatermark $watermarkMgr
         * @var EventgalleryLibraryWatermark $watermark
         */
        $watermarkMgr = EventgalleryLibraryManagerWatermark::getInstance();

        $watermark = $watermarkMgr->getWatermark($this->_folder->watermarkid);

        return $watermark;
    }

    /**
    * Returns the category id of this folder
    *
    * @return int|null
    */
    public function getCategoryId() {
        return $this->_folder->catid;
    }

	/**
	* Returns the number of hits for this folder.
	* @return int
	*/
    public function getHits() {
    	return $this->_folder->hits;
    }

     /**
     * increases the hit counter in the database
     */
    public function countHits() {
        /**
         * @var TableFolder $table
         */
        $table = JTable::getInstance('Folder', 'Table');


        $table->bind($this->_folder);
        $table->hits++;
        $table->store();


    }
}
