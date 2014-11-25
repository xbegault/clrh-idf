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

class EventgalleryLibraryFactoryOrder extends EventgalleryLibraryFactoryFactory
{


    /**
     * @param EventgalleryLibraryCart $cart
     * @return EventgalleryLibraryOrder
     */
    public function createOrder($cart) {

        $db = JFactory::getDBO();
        $data = $cart->_getInternalDataObject();

        $uuid = uniqid("", true);
        $uuid = base_convert($uuid,16,10);

        $query = $db->getQuery(true);
        $query->insert("#__eventgallery_order");
        $query->columns('id');
        $query->values($db->quote($uuid));
        $db->setQuery($query);
        $db->execute();


        $user = JFactory::getUser();

        $data['id'] = $uuid;
        if (!$user->guest) {
            $data['userid'] = $user->id;
        }

        /**
         * @var TableOrder $orderTable
         */
        $orderTable = $this->store($data, 'Order');
        /**
         * @var EventgalleryLibraryImagelineitem $lineitem
         * @var EventgalleryLibraryFactoryImagelineitem $imageLineItemFactory
         */

        $imageLineItemFactory = EventgalleryLibraryFactoryImagelineitem::getInstance();

        foreach ($cart->getLineItems() as $lineitem) {
            $imageLineItemFactory->copyLineItem($orderTable->id, $lineitem);
        }

        /**
         * @var EventgalleryLibraryServicelineitem $lineitem
         * @var EventgalleryLibraryFactoryServicelineitem $serviceLineItemFactory
         */
        $serviceLineItemFactory = EventgalleryLibraryFactoryServicelineitem::getInstance();

        foreach ($cart->getServiceLineItems() as $lineitem) {
            $serviceLineItemFactory->copyLineItem($orderTable->id, $lineitem);
        }


        /**
         * @var EventgalleryLibraryManagerOrderstatus $orderstatusMgr
         */
        $orderstatusMgr = EventgalleryLibraryManagerOrderstatus::getInstance();

        /**
         * @var EventgalleryLibraryOrder $order
         */
        $order = new EventgalleryLibraryOrder($orderTable->id);
        $order->setOrderStatus($orderstatusMgr->getDefaultOrderStatus(EventgalleryLibraryOrderstatus::TYPE_ORDER));
        $order->setPaymentStatus($orderstatusMgr->getDefaultOrderStatus(EventgalleryLibraryOrderstatus::TYPE_PAYMENT));
        $order->setShippingStatus($orderstatusMgr->getDefaultOrderStatus(EventgalleryLibraryOrderstatus::TYPE_SHIPPING));
        $order->setDocumentNumber(EventgalleryLibraryDatabaseSequence::generateNewId());

        return $order;
    }

    public function getOrdersByUserId($userid) {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);
        $query->select('id');
        $query->from("#__eventgallery_order");
        $query->where("userid=".$db->quote($userid));
        $query->order("created desc");

        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $orders = array();
        foreach ($rows as $row) {
            array_push($orders, new EventgalleryLibraryOrder($row->id));
        }

        return $orders;
    }

    /**
     * @param string $documentNo
     * @return EventgalleryLibraryOrder
     */
    public function getOrdersByDocumentNumber($documentNo)
    {
        if ($documentNo<0) {
            return null;
        }

        $db = JFactory::getDBO();

        $query = $db->getQuery(true);
        $query->select('id');
        $query->from("#__eventgallery_order");
        $query->where("documentno=".$db->quote($documentNo));


        $db->setQuery($query);
        $row = $db->loadObject();

        if ($row == null) {
            return null;
        }

        $order = new EventgalleryLibraryOrder($row->id);

        return $order;
    }

    /**
     * Returns the order oject for a given ID
     *
     * @param $id
     * @return EventgalleryLibraryOrder
     */
    public function getOrderById($id) {
        $order = new EventgalleryLibraryOrder($id);
        return $order;
    }


}
