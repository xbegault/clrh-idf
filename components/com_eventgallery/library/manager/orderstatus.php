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

class EventgalleryLibraryManagerOrderstatus extends EventgalleryLibraryManagerManager
{

    protected $_orderstatusesByType = array();
    protected $_defaultorderstatus = array();
    protected $_orderstatuses = array();

    function __construct()
    {

    }

    /**
     * Returns all orderstatuses of the given type ordered.
     *
     * @param $typeid
     * @return array|null
     */
    public function getOrderStatuses($typeid) {

        if (!isset($this->_orderstatusesByType[$typeid])) {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('s.*');
            $query->from('#__eventgallery_orderstatus s');
            $query->where('type='.$db->quote($typeid));
            $query->order('ordering');
            $db->setQuery($query);
            $items = $db->loadObjectList();

            if (count($items) == 0) {
                return NULL;
            }

            $orderstatuses = array();
            foreach($items as $item) {
                $orderstatuses[] = new EventgalleryLibraryOrderstatus($item);
            }


            $this->_orderstatusesByType[$typeid] =  $orderstatuses;
        }

        return $this->_orderstatusesByType[$typeid];
    }

    /**
     * returns an order status by id
     *
     * @param $id int the id of the order status
     * @return EventgalleryLibraryOrderstatus
     */
    public function getOrderStatus($id) {
        if (!isset($this->_orderstatuses[$id])) {
            
            $this->_orderstatuses[$id] = new EventgalleryLibraryOrderstatus($id);
        }

        return $this->_orderstatuses[$id];
    }

    /**
     * returns the default order status for the given type
     *
     * @param $typeid
     * @return EventgalleryLibraryOrderstatus|null
     */
    public function getDefaultOrderStatus($typeid)
    {
        if (!isset($this->_defaultorderstatus[$typeid])) {

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('s.*');
            $query->from('#__eventgallery_orderstatus s');
            $query->where('type='.$db->quote($typeid));
            $query->order($db->quoteName('default') .' DESC');
            $query->order('ordering');
            $db->setQuery($query);
            $items = $db->loadObjectList();

            if (count($items) == 0) {
                return NULL;
            }

            $item = $items[0];
            $this->_defaultorderstatus[$typeid] = new EventgalleryLibraryOrderstatus($item);

        }

        return $this->_defaultorderstatus[$typeid];
    }

    /**
     * resets the order status cache.
     */
    public function clearCache() {
        $this->_defaultorderstatus = array();
        $this->_orderstatuses = array();
        $this->_orderstatusesByType = array();
    }


}
