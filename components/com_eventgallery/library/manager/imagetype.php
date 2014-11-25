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

class EventgalleryLibraryManagerImagetype extends  EventgalleryLibraryManagerManager
{

    protected $_imagetypes;
    protected $_imagetypes_published;


    /**
     * Return all imagetypes
     *
     * @param $publishedOnly
     * @return array
     */
    public function getImageTypes($publishedOnly) {
        if ($this->_imagetypes == null) {

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('id');
            $query->from('#__eventgallery_imagetype');
            $db->setQuery($query);
            $items = $db->loadObjectList();

            $this->_imagetypes = array();
            $this->_imagetypes_published = array();

            foreach ($items as $item) {
                /**
                 * @var EventgalleryLibraryImagetype $itemObject
                 */
                $itemObject = new EventgalleryLibraryImagetype($item->id);
                if ($itemObject->isPublished()) {
                    $this->_imagetypes_published[$itemObject->getId()] = $itemObject;
                }
                $this->_imagetypes[$itemObject->getId()] = $itemObject;
            }
        }
        if ($publishedOnly) {
            return $this->_imagetypes_published;
        } else {
            return $this->_imagetypes;
        }
    }

    


}
