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

class EventgalleryLibraryImagetypeset extends EventgalleryLibraryDatabaseObject
{

    /**
     * @var int
     */
    protected $_imagetypeset_id = NULL;

    /**
     * @var TableImagetypeset
     */
    protected $_imagetypeset = NULL;

    /**
     * @var array
     */
    protected $_imagetypes = NULL;

    /**
     * @var int
     */
    protected $_defaultimagetype_id = NULL;

    /**
     * @param int $imagetypeset_id
     */
    public function __construct($imagetypeset_id = -1)
    {
        $this->_imagetypeset_id = $imagetypeset_id;
        $this->_loadImageTypeSet();
        parent::__construct();
    }

    /**
     *
     */
    protected function _loadImageTypeSet()
    {

        if (null == $this->_imagetypeset_id) {
            return;
        }

        $this->_imagetypeset = NULL;
        $this->_imagetypes = NULL;

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('its.*');
        $query->from('#__eventgallery_imagetypeset as its');
        if ($this->_imagetypeset_id != -1) {
            $query->where('its.id=' . $db->quote($this->_imagetypeset_id));
        }
        $query->order('its.default DESC');
        $db->setQuery($query);
        $this->_imagetypeset = $db->loadObject();



        $this->_imagetypeset_id = $this->_imagetypeset->id;

        $this->_loadImageTypes();
    }

    /**
     * @throws Exception
     */
    protected function _loadImageTypes()
    {

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('t.*, tsta.default as defaultimagetype');
        $query->from(
            '#__eventgallery_imagetypeset_imagetype_assignment tsta left join #__eventgallery_imagetype t on tsta.imagetypeid=t.id'
        );
        $query->where('tsta.imagetypesetid=' . $db->quote($this->_imagetypeset->id));
        $query->order('tsta.ordering');
        $db->setQuery($query);
        $dbtypes = $db->loadObjectList();
        $types = array();

        $this->_defaultimagetype_id = NULL;

        foreach ($dbtypes as $dbtype) {

            if ($dbtype->defaultimagetype == 1) {
                $this->_defaultimagetype_id = $dbtype->id;
            }
            $types[$dbtype->id] = new EventgalleryLibraryImagetype($dbtype);
        }

        $this->_imagetypes = $types;


        

    }

    /**
     * @return array|null
     */
    public function getImageTypes()
    {
        return $this->_imagetypes;
    }

    /**
     * This method will return the imagetype with the given id.
     *
     * @param int $imagetypeid
     *
     * @return EventgalleryLibraryImagetype
     */
    public function getImageType($imagetypeid)
    {

        if (isset($this->_imagetypes[$imagetypeid])) {
            return $this->_imagetypes[$imagetypeid];
        }

        return null;

    }

    /**
     * @return EventgalleryLibraryImagetype
     */
    public function getDefaultImageType()
    {
        if ($this->_defaultimagetype_id == NULL) {
            $result = array_values($this->_imagetypes);
            if (isset($result[0])) {
                return $result[0];
            } else {
                return null;
            }
        } else {
            return $this->getImageType($this->_defaultimagetype_id);
        }
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->_imagetypeset->id;
    }

    /**
     * @return bool
     */
    public function isPublished() {
        return $this->_imagetypeset->published==1;
    }

    /**
     * @return bool
     */
    public function isDefault() {
        return $this->_imagetypeset->default==1;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->_imagetypeset->name;
    }

    /**
     * @return string
     */
    public function getNote() {
        return $this->_imagetypeset->note;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->_imagetypeset->description;
    }

    /**
     * @return int
     */
    public function getImageTypeCount() {
        return count($this->_imagetypes);
    }


}
