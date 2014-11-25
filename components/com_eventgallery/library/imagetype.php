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

class EventgalleryLibraryImagetype extends EventgalleryLibraryDatabaseObject
{

    protected $_imagetype = NULL;
    protected $_imagetype_id = NULL;
    protected $_ls_displayname = NULL;
    protected $_ls_description = NULL;

    public function __construct($dbimagetype)
    {
        if ($dbimagetype instanceof stdClass) {
            $this->_imagetype = $dbimagetype;
            $this->_imagetype_id = $dbimagetype->id;
        } else {
            $this->_imagetype_id = $dbimagetype;
            $this->_loadImageType();
        }

        $this->_ls_displayname = new EventgalleryLibraryDatabaseLocalizablestring($this->_imagetype->displayname);
        $this->_ls_description = new EventgalleryLibraryDatabaseLocalizablestring($this->_imagetype->description);

        parent::__construct();
    }

    /**
     * Load the image type by id
     */
    protected function _loadImageType()
    {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__eventgallery_imagetype');
        $query->where('id=' . $db->Quote($this->_imagetype_id));

        $db->setQuery($query);
        $this->_imagetype = $db->loadObject();
    }

    /**
     * @return string the id of the image type
     */
    public function getId()
    {
        return $this->_imagetype->id;
    }

	/**
	* @return float
	*/
    public function getTaxrate() {
        return $this->_imagetype->taxrate;
    }
    /**
     * @return EventgalleryLibraryCommonMoney the price value of the image type
     */
    public function getPrice()
    {
        return new EventgalleryLibraryCommonMoney($this->_imagetype->price, $this->_imagetype->currency);
    }

    /**
     * @return string display name of the image type
     */
    public function getName()
    {
        return $this->_imagetype->name;
    }

    /**
     * @return string display name of the image type
     */
    public function getDisplayName()
    {
        return $this->_ls_displayname->get();
    }

    /**
     * @return string description name of the image type
     */
    public function getDescription()
    {
        return $this->_ls_description->get();
    }

    /**
     * @return string
     */
    public function getNote() {
        return $this->_imagetype->note;
    }

    /**
     * Defines if this image type is a digital one. The oposite is a format which has to be shipped physically.
     *
     * @return bool
     */
    public function isDigital() {
        return $this->_imagetype->isdigital==1;
    }

    /**
     * @return bool
     */
    public function isPublished() {
        return $this->_imagetype->published==1;
    }

    /**
     * @return string
     */
    public function getSize() {
        return $this->_imagetype->size;
    }


}
