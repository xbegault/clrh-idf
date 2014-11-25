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

/**
 * @property mixed cart
 */
class EventgalleryLibraryOrder extends EventgalleryLibraryLineitemcontainer
{

    protected $_lineitemstatus = EventgalleryLibraryLineitem::TYPE_CART;
    /**
     * @var TableOrder
     */
    protected $_lineitemcontainer = NULL;
    /**
     * @var string
     */
    protected $_lineitemcontainer_table = "Order";

    protected $_orderstatus = NULL;
    protected $_shippingstatus = NULL;
    protected $_paymentstatus = NULL;

    public function __construct($orderid)
    {
        $this->_lineitemcontainer_id = $orderid;
        $this->_loadLineItemContainer();
        parent::__construct();
    }

    /**
     * @throws Exception
     */
    protected function _loadLineItemContainer()
    {

        $this->_lineitemcontainer = NULL;
        $this->_lineitems = NULL;

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('o.*');
        $query->from('#__eventgallery_order as o');
        $query->where('o.id = ' . $db->quote($this->_lineitemcontainer_id));
        $db->setQuery($query);

        $this->_lineitemcontainer = $db->loadObject();

        if ($this->_lineitemcontainer == NULL) {
            throw new Exception("no order found for id " . $this->_lineitemcontainer_id);
        }

        $this->_loadLineItems();
        $this->_loadServiceLineItems();
    }

    /**
     * @param EventgalleryLibraryOrderStatus $orderStatus
     */
    public function setOrderStatus($orderStatus)
    {
        if ($orderStatus == NULL) {
            return;
        }
        $this->_lineitemcontainer->orderstatusid = $orderStatus->getId();
        $this->_storeLineItemContainer();
        $this->_paymentstatus = null;
    }

    /**
     * @return EventgalleryLibraryOrderstatus
     */
    public function getOrderStatus() {
        if (null==$this->_orderstatus) {
            /**
             * @var EventgalleryLibraryManagerOrderstatus $orderstatusMgr
             */
            $orderstatusMgr = EventgalleryLibraryManagerOrderstatus::getInstance();
            $this->_orderstatus = $orderstatusMgr->getOrderStatus($this->_lineitemcontainer->orderstatusid);
        }
        return $this->_orderstatus;
    }

    /**
     * @return EventgalleryLibraryOrderstatus
     */
    public function getPaymentStatus() {
        if (null == $this->_paymentstatus) {
            /**
             * @var EventgalleryLibraryManagerOrderstatus $orderstatusMgr
             */
            $orderstatusMgr = EventgalleryLibraryManagerOrderstatus::getInstance();
            $this->_paymentstatus = $orderstatusMgr->getOrderStatus($this->_lineitemcontainer->paymentstatusid);
        }
        return $this->_paymentstatus;
    }

    /**
     * @param EventgalleryLibraryOrderstatus $paymentstatus
     */
    public function setPaymentStatus($paymentstatus) {
        $this->_lineitemcontainer->paymentstatusid = $paymentstatus->getId();
        $this->_storeLineItemContainer();
        $this->_paymentstatus = null;
    }

    /**
     * @return EventgalleryLibraryOrderstatus
     */
    public function getShippingStatus() {
        if (null==$this->_shippingstatus) {
            /**
             * @var EventgalleryLibraryManagerOrderstatus $orderstatusMgr
             */
            $orderstatusMgr = EventgalleryLibraryManagerOrderstatus::getInstance();
            $this->_shippingstatus = $orderstatusMgr->getOrderStatus($this->_lineitemcontainer->shippingstatusid);
        }
        return $this->_shippingstatus;
    }

    /**
     * @param EventgalleryLibraryOrderstatus $shippingstatus
     */
    public function setShippingStatus($shippingstatus) {
        $this->_lineitemcontainer->shippingstatusid = $shippingstatus->getId();
        $this->_storeLineItemContainer();
        $this->_shippingstatus = null;

    }


}
