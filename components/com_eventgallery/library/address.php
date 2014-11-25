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

class EventgalleryLibraryAddress extends EventgalleryLibraryDatabaseObject
{

    /**
     * @var TableStaticaddress
     */
    protected $_object = NULL;
    protected $_object_id = NULL;

    public function __construct($object)
    {
        if ($object instanceof stdClass OR $object instanceof JTable) {
            $this->_object = $object;
            $this->_object_id = $object->id;
        } else {
            $this->_object_id = $object;
            $this->_loadAddress();
        }

        parent::__construct();
    }

    /**
     * Load the address by id
     */
    protected function _loadAddress()
    {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__eventgallery_staticaddress');
        $query->where('id=' . $db->Quote($this->_object_id));

        $db->setQuery($query);
        $this->_object = $db->loadObject();
    }

    /**
     * @param string $prefix
     *
     * @return array
     */
    public function _getData($prefix)
    {
        $result = array();
        foreach (get_object_vars($this->_object) as $key => $value) {
            $result[$prefix . $key] = $value;
        }
        return $result;
    }

    /**
     * @return string the id
     */
    public function getId()
    {
        return $this->_object->id;
    }

    public function getFirstName()
    {
        return $this->_object->firstname;
    }

    public function getLastName()
    {
        return $this->_object->lastname;
    }

    public function getAddress1()
    {
        return $this->_object->address1;
    }

    public function getAddress2()
    {
        return $this->_object->address2;
    }

    public function getAddress3()
    {
        return $this->_object->address3;
    }

    public function getCity()
    {
        return $this->_object->city;
    }

    public function getZip()
    {
        return $this->_object->zip;
    }

    public function getCountry()
    {
        return $this->_object->country;
    }

}
