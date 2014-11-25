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

class EventgalleryLibraryManagerWatermark extends  EventgalleryLibraryManagerManager
{

    protected $_watermarks;
    protected $_watermarks_published;


    /**
     * Return all watermarks
     *
     * @param $publishedOnly
     * @return array
     */
    public function getWatermarks($publishedOnly) {
        if ($this->_watermarks == null) {

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('id');
            $query->from('#__eventgallery_watermark');            
            $query->order('ordering');
            $db->setQuery($query);
            $items = $db->loadObjectList();

            $this->_watermarks = array();
            $this->_watermarks_published = array();

            foreach ($items as $item) {
                /**
                 * @var EventgalleryLibraryWatermark $itemObject
                 */
                $itemObject = new EventgalleryLibraryWatermark($item->id);
                if ($itemObject->isPublished()) {
                    $this->_watermarks_published[$itemObject->getId()] = $itemObject;
                }
                $this->_watermarks[$itemObject->getId()] = $itemObject;
            }
        }
        if ($publishedOnly) {
            return $this->_watermarks_published;
        } else {
            return $this->_watermarks;
        }
    }


    public function getWatermark($id) {
        $sets = $this->getWatermarks(false);
        if (isset($sets[$id]))
        {
            return $sets[$id];
        }
        return null;
    }


}
