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
abstract class EventgalleryLibraryLineitemcontainer extends EventgalleryLibraryDatabaseObject
{


    /**
     * @var EventgalleryLibraryAddress
     */
    protected $_billingaddress = null;
    /**
     * @var TableCart
     */
    protected $_lineitemcontainer = null;
    /**
     * @var string
     */
    protected $_lineitemcontainer_table = null;
    /**
     * @var array
     */
    protected $_lineitems = null;

    /**
     * @var array
     */
    protected $_servicelineitems = null;
    /**
     * @var EventgalleryLibraryMethodsShipping
     */

    protected $_shippingaddress = null;
    /**
     * @var EventgalleryLibraryMethodsSurcharge
     */

    protected $_user_id = null;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Use this method never in your source code. This is only for managers.
     *
     * @return array
     */
    public function _getInternalDataObject()
    {
        return get_object_vars($this->_lineitemcontainer);
    }

    function deleteLineItem($lineitemid)
    {
        if ($lineitemid == null) {
            return;
        }


        if ($this->getLineItem($lineitemid) == null) {
            return;
        }

        $this->getLineItem($lineitemid)->delete();
        $this->_updateLineItemContainer();
    }

    /**
     * @param $lineitemid
     *
     * @return EventgalleryLibraryLineitem
     */
    public function getLineItem($lineitemid)
    {
        if (isset($this->_lineitems[$lineitemid])) {
            return $this->_lineitems[$lineitemid];
        } else {
            return null;
        }
    }

    /**
     * Updates the cart object stucture from the database
     */
    protected function _updateLineItemContainer()
    {
        $this->_loadLineItemContainer();

    }

    protected abstract function _loadLineItemContainer();

    /**
     * @return EventgalleryLibraryAddress
     */

    public function getBillingAddress()
    {
        if ($this->_billingaddress == null && $this->_lineitemcontainer->billingaddressid != null) {
            $this->_billingaddress = new EventgalleryLibraryAddress($this->_lineitemcontainer->billingaddressid);
        }
        return $this->_billingaddress;
    }

    /**
     * @return string
     */
    public function getEMail()
    {
        return $this->_lineitemcontainer->email;
    }

    /**
     * @return int returns the current number of line items in this cart
     */
    function getLineItemsCount()
    {
        return count($this->_lineitems);
    }

    /**
     * @return int the sum of all quantities in this cart
     */
    function getLineItemsTotalCount()
    {
        $count = 0;
        /* @var EventgalleryLibraryLineitem $lineitem */
        foreach ($this->getLineItems() as $lineitem) {

            $count += $lineitem->getQuantity();
        }
        return $count;
    }

    /**
     * @return array all lineitems from this container
     */
    function getLineItems()
    {
        return array_values($this->_lineitems);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->_lineitemcontainer->message;
    }


    /**
     * @return EventgalleryLibraryServicelineitem|null
     */
    public function getPaymentMethodServiceLineItem() {
        foreach ($this->_servicelineitems as $servicelineitem) {
            /**
             * @var EventgalleryLibraryServicelineitem $servicelineitem
             */
            if ($servicelineitem->isPaymentMethod()) {
                return $servicelineitem;
            }
        }

        return null;
    }
    /**
     * @return EventgalleryLibraryMethodsPayment|null
     */
    public function getPaymentMethod()
    {
        $sli = $this->getPaymentMethodServiceLineItem();
        if ($sli) {
            return $sli->getMethod();
        }
        return null;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->_lineitemcontainer->phone;
    }

    /**
     * @return EventgalleryLibraryAddress
     */
    public function getShippingAddress()
    {
        if ($this->_shippingaddress == null && $this->_lineitemcontainer->shippingaddressid != null) {
            $this->_shippingaddress = new EventgalleryLibraryAddress($this->_lineitemcontainer->shippingaddressid);
        }
        return $this->_shippingaddress;
    }

    /**
     * @return EventgalleryLibraryServicelineitem|null
     */
    public function getShippingMethodServiceLineItem() {
        foreach ($this->_servicelineitems as $servicelineitem) {
            /**
             * @var EventgalleryLibraryServicelineitem $servicelineitem
             */
            if ($servicelineitem->isShippingMethod()) {
                return $servicelineitem;
            }
        }

        return null;
    }
    /**
     * @return EventgalleryLibraryMethodsShipping|null
     */
    public function getShippingMethod()
    {
        $sli = $this->getShippingMethodServiceLineItem();
        if ($sli) {
            return $sli->getMethod();
        }
        return null;
    }

    /**
     * @return EventgalleryLibraryCommonMoney
     */
    public function getSubTotal()
    {
        return new EventgalleryLibraryCommonMoney($this->_lineitemcontainer->subtotal, $this->_lineitemcontainer->subtotalcurrency);
    }


    /**
     * @return EventgalleryLibraryServicelineitem|null
     */
    public function getSurchargeServiceLineItem() {
        foreach ($this->_servicelineitems as $servicelineitem) {
            /**
             * @var EventgalleryLibraryServicelineitem $servicelineitem
             */
            if ($servicelineitem->isSurcharge()) {
                return $servicelineitem;
            }
        }

        return null;
    }
    /**
     * @return EventgalleryLibraryMethodsSurcharge|null
     */
    public function getSurcharge()
    {
        $sli = $this->getSurchargeServiceLineItem();
        if ($sli) {
            return $sli->getMethod();
        }
        return null;
    }

    /**
     * sets a surcharge
     *
     * @param EventgalleryLibraryMethodsSurcharge $surcharge
     */
    public function setSurcharge($surcharge)
    {

        $this->_deleteMethodByType(EventgalleryLibraryServicelineitem::TYPE_SURCHARGE);

        if ($surcharge == null) {
            return;
        }

        /* @var EventgalleryLibraryFactoryServicelineitem $serviceLineItemFactory */
        $serviceLineItemFactory = EventgalleryLibraryFactoryServicelineitem::getInstance();
        $serviceLineItemFactory->createLineItem($this->getId(), $surcharge);
        $this->_loadServiceLineItems();
    }

    /**
     * @param int $methodtypeid
     */
    protected function _deleteMethodByType($methodtypeid)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->delete('#__eventgallery_servicelineitem');
        $query->where('type=' . $db->quote($methodtypeid));
        $query->where('lineitemcontainerid=' . $db->quote($this->getId()));
        $db->setQuery($query);
        $db->execute();

        $this->_loadServiceLineItems();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_lineitemcontainer->id;
    }

    /**
     * returns EventgalleryLibraryCommonMoney the tax amount
     */
    public function getTax() {
        $tax = 0;

        foreach($this->getLineItems() as $lineitem) {
            /**
             * @var EventgalleryLibraryLineitem $lineitem
             */
            $tax += $lineitem->getTax();
        }

        if ($this->getShippingMethodServiceLineItem()) {
            $tax += $this->getShippingMethodServiceLineItem()->getTax();
        }

        if ($this->getPaymentMethodServiceLineItem()) {
            $tax += $this->getPaymentMethodServiceLineItem()->getTax();
        }

        if ($this->getSurchargeServiceLineItem()) {
            $tax += $this->getSurchargeServiceLineItem()->getTax();
        }

        return new EventgalleryLibraryCommonMoney($tax, $this->_lineitemcontainer->subtotalcurrency);
    }

    /**
     * @return EventgalleryLibraryCommonMoney
     */
    public function getTotal()
    {
        return new EventgalleryLibraryCommonMoney($this->_lineitemcontainer->total, $this->_lineitemcontainer->totalcurrency);
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->_lineitemcontainer->userid;
    }

    /**
     * @param EventgalleryLibraryAddress $billingAddress
     */
    public function setBillingAddress($billingAddress)
    {
        if ($billingAddress == null) {
            return;
        }
        $this->_billingaddress = $billingAddress;
        $this->_lineitemcontainer->billingaddressid = $billingAddress->getId();
        $this->_storeLineItemContainer();
    }

    protected function _storeLineItemContainer()
    {
        $data = $this->_lineitemcontainer;
        $this->store((array)$data, $this->_lineitemcontainer_table);
    }

    /**
     * @param string $email
     */
    public function setEMail($email)
    {
        $this->_lineitemcontainer->email = $email;
        $this->_storeLineItemContainer();
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->_lineitemcontainer->message = $message;
        $this->_storeLineItemContainer();
    }

    /**
     * sets a Payment
     *
     * @param EventgalleryLibraryMethodsPayment $payment
     */
    public function setPaymentMethod($payment)
    {

        $this->_deleteMethodByType(EventgalleryLibraryServicelineitem::TYPE_PAYMENTMETHOD);

        if ($payment == null) {
            return;
        }

        /* @var EventgalleryLibraryFactoryServicelineitem $serviceLineItemFactory */
        $serviceLineItemFactory = EventgalleryLibraryFactoryServicelineitem::getInstance();
        $serviceLineItemFactory->createLineItem($this->getId(),  $payment);
        $this->_loadServiceLineItems();
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->_lineitemcontainer->phone = $phone;
        $this->_storeLineItemContainer();
    }

    /**
     * @param EventgalleryLibraryAddress $shippingAddress
     */
    public function setShippingAddress($shippingAddress)
    {
        if ($shippingAddress == null) {
            return;
        }
        $this->_shippingaddress = $shippingAddress;
        $this->_lineitemcontainer->shippingaddressid = $shippingAddress->getId();
        $this->_storeLineItemContainer();
    }

    /**
     * @param $documentNumber
     */
    public function setDocumentNumber($documentNumber)
    {
        $this->_lineitemcontainer->documentno = $documentNumber;
        $this->_storeLineItemContainer();
    }

    /**
     * sets a shipping
     *
     * @param EventgalleryLibraryMethodsShipping $shipping
     */
    public function setShippingMethod($shipping)
    {

        $this->_deleteMethodByType(EventgalleryLibraryServicelineitem::TYPE_SHIPINGMETHOD);

        if ($shipping == null) {
            return;
        }

        /* @var EventgalleryLibraryFactoryServicelineitem $serviceLineItemFactory */
        $serviceLineItemFactory = EventgalleryLibraryFactoryServicelineitem::getInstance();
        $serviceLineItemFactory->createLineItem($this->getId(), $shipping);
        $this->_loadServiceLineItems();
    }

    /**
     * @param EventgalleryLibraryCommonMoney $price
     */
    public function setSubTotal($price)
    {
    	
        $this->_lineitemcontainer->subtotal = $price->getAmount();
        $this->_lineitemcontainer->subtotalcurrency = $price->getCurrency();
        $this->_storeLineItemContainer();
    }


    /**
     * @param EventgalleryLibraryCommonMoney $price
     */
    public function setTotal($price)
    {
        $this->_lineitemcontainer->total = $price->getAmount();
        $this->_lineitemcontainer->totalcurrency = $price->getCurrency();
        $this->_storeLineItemContainer();
    }

    /**
     * loads lineitems from the database
     *
     */
    protected function _loadLineItems()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('ili.*');
        $query->from('#__eventgallery_imagelineitem as ili');
        $query->where('ili.lineitemcontainerid = ' . $db->quote($this->getId()));
        $query->order('ili.id');

        $db->setQuery($query);

        $dbLineItems = $db->loadObjectList();

        $lineitems = array();
        foreach ($dbLineItems as $dbLineItem) {
            $lineitems[$dbLineItem->id] = new EventgalleryLibraryImagelineitem($dbLineItem);
        }

        $this->_lineitems = $lineitems;
    }

    /**
     */
    protected function _loadServiceLineItems()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__eventgallery_servicelineitem');
        $query->where('lineitemcontainerid = ' . $db->quote($this->getId()));

        $db->setQuery($query);

        $dbServiceLineItems = $db->loadObjectList();

        $servicelineitems = array();
        foreach ($dbServiceLineItems as $dbServiceLineItem) {
            $servicelineitems[$dbServiceLineItem->id] = new EventgalleryLibraryServicelineitem($dbServiceLineItem);
        }

        $this->_servicelineitems = $servicelineitems;
    }

    /**
     * @return array|null
     */
    public function getServiceLineItems() {
        return $this->_servicelineitems;
    }

    public function getCreationDate() {
        return $this->_lineitemcontainer->created;
    }

    public function getModificationDate() {
        return $this->_lineitemcontainer->modified;
    }

    public function getDocumentNumber() {
        return $this->_lineitemcontainer->documentno;
    }
}
