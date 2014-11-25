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

class EventgalleryLibraryManagerImagetypeset extends  EventgalleryLibraryManagerManager
{

    protected $_imagetypesets;
    protected $_imagetypesets_published;


    /**
     * Return all imagetypesets
     *
     * @param $publishedOnly
     * @return array
     */
    public function getImageTypeSets($publishedOnly) {
        if ($this->_imagetypesets == null) {

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('id');
            $query->from('#__eventgallery_imagetypeset');
            $query->order($db->quoteName('default') . ' DESC');
            $query->order('ordering');
            $db->setQuery($query);
            $items = $db->loadObjectList();

            $this->_imagetypesets = array();
            $this->_imagetypesets_published = array();

            foreach ($items as $item) {
                /**
                 * @var EventgalleryLibraryImagetypeset $itemObject
                 */
                $itemObject = new EventgalleryLibraryImagetypeset($item->id);
                if ($itemObject->isPublished()) {
                    $this->_imagetypesets_published[$itemObject->getId()] = $itemObject;
                }
                $this->_imagetypesets[$itemObject->getId()] = $itemObject;
            }
        }
        if ($publishedOnly) {
            return $this->_imagetypesets_published;
        } else {
            return $this->_imagetypesets;
        }
    }

    /**
     * Returns the default image type set
     *
     * @param bool $publishedOnly returns only  published imagetypeset
     * @return EventgalleryLibraryImagetypeset
     */
    public function getDefaultImageTypeSet($publishedOnly) {
        $sets = array_values($this->getImageTypeSets($publishedOnly));
        if (isset($sets[0])) {
            return $sets[0];
        }
        return null;

    }


    public function getImageTypeSet($id) {
        $sets = $this->getImageTypeSets(false);
        if (isset($sets[$id]))
        {
            return $sets[$id];
        }
        return $this->getDefaultImageTypeSet(true);
    }


}
