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

class EventgalleryLibraryManagerCart extends EventgalleryLibraryManagerManager
{

    protected $_carts = array();

    function __construct()
    {

    }


    /**
     * Updates line item quantities and types.
     *
     * Syntax:
     *  - quantity_[lineitemid]=[quantity}
     *  - type_[lineitemid]=[imagetypeid]
     *
     *
     * @param EventgalleryLibraryCart $cart
     *
     * @return array Errors
     */
    public function updateLineItems(EventgalleryLibraryCart $cart = NULL)
    {
        $errors = array();

        if ($cart == NULL) {
            $cart = $this->getCart();
        }

        /**
         * LINEITEM UPDATES
         */

        /* @var EventgalleryLibraryImagelineitem $lineitem */
        foreach ($cart->getLineItems() as $lineitem) {

            /* Quantity Update*/
            $quantity = JRequest::getString('quantity_' . $lineitem->getId(), NULL);
            if ($quantity != NULL) {

                if ($quantity > 0) {
                    $lineitem->setQuantity($quantity);
                } else {
                    $cart->deleteLineItem($lineitem->getId());
                }
            }

            /* type update */

            $imagetypeid = JRequest::getString('type_' . $lineitem->getId(), NULL);

            if (NULL != $imagetypeid) {
                $lineitem->setImageType($imagetypeid);
            }

        }

        return $errors;
    }

    /**
     * get the cart from the database.
     *
     * @return EventgalleryLibraryCart
     */
    public function getCart()
    {

        /* try to get the right user id for the cart. This can also be the session id */
        $session = JFactory::getSession();
        $user_id = $session->getId();
        /** @noinspection PhpUndefinedMethodInspection */
        if (!isset($this->_carts[$user_id]) || $this->_carts[$user_id]->getStatus()!=0) {
            $this->_carts[$user_id] = new EventgalleryLibraryCart($user_id);
        }
        return $this->_carts[$user_id];
    }

    /**
     *
     * @param EventgalleryLibraryCart $cart
     *
     * @return array Errors
     */
    public function updateShippingMethod(EventgalleryLibraryCart $cart = NULL)
    {
        $errors = array();

        if ($cart == NULL) {
            $cart = $this->getCart();
        }

        /**
         * SHIPPING UPDATE
         */

        $shippingmethodid = JRequest::getString('shippingid', NULL);

        if ($shippingmethodid != NULL || $cart->getShippingMethod() == NULL) {
            /**
             * @var EventgalleryLibraryManagerShipping $shippingMgr
             * @var EventgalleryLibraryMethodsShipping $method
             */
            $shippingMgr = EventgalleryLibraryManagerShipping::getInstance();
            $method = $shippingMgr->getMethod($shippingmethodid, true);
            if ($method == NULL || $method->isEligible($cart)==false ) {
                if ($shippingMgr->getDefaultMethod()->isEligible($cart)) {
                    $method = $shippingMgr->getDefaultMethod();
                }
            }
            $cart->setShippingMethod($method);
        }

        if ($cart->getShippingMethod() == null) {

            $errors[] = new Exception(JText::_('COM_EVENTGALLERY_CART_CHECKOUT_FORM_SHIPPINGMETHOD_INVALID'));
        }

        return $errors;
    }

    /**
     *
     * @param EventgalleryLibraryCart $cart
     *
     * @return array Errors
     */
    public function updatePaymentMethod(EventgalleryLibraryCart $cart = NULL)
    {
        $errors = array();

        if ($cart == NULL) {
            $cart = $this->getCart();
        }

        /**
         * PAYMENT UPDATES
         */

        $paymentmethodid = JRequest::getString('paymentid', NULL);


        if ($paymentmethodid != NULL || $cart->getPaymentMethod() == NULL) {
            /**
             * @var EventgalleryLibraryManagerPayment $paymentMgr
             * @var EventgalleryLibraryMethodsPayment $method
             */
            $paymentMgr = EventgalleryLibraryManagerPayment::getInstance();
            $method = $paymentMgr->getMethod($paymentmethodid, true);
            if ($method == NULL) {
                $method = $paymentMgr->getDefaultMethod();
            }
            $cart->setPaymentMethod($method);
        }

        return $errors;
    }

    /**
     * Updates the addresses of the cart
     *
     * validate billing address first. If this address is okay,
     * continue with the shipping address. This works for the customer
     * since there is also client side validation available
     *
     * @param EventgalleryLibraryCart $cart
     *
     * @return array Errors
     */
    public function updateAddresses(EventgalleryLibraryCart $cart = NULL)
    {
        $errors = array();

        if ($cart == NULL) {
            $cart = $this->getCart();
        }


        $xmlPath = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_eventgallery'
            . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR;

        /**
         * USERDATA UPDATES
         */

        $userdataform = JForm::getInstance('billing', $xmlPath . 'billingaddress.xml');
        $userdataform->bind(JRequest::get('post'));
        $userdatavalidation = $userdataform->validate(JRequest::get('post'));
        if ($userdatavalidation !== true) {
            $errors = array_merge($errors, $userdataform->getErrors());
        } else {

            $phone = JRequest::getString('phone', NULL);
            if ($phone != NULL) {
                $cart->setPhone($phone);
            }

            $email = JRequest::getString('email', NULL);
            if ($email != NULL) {
                $cart->setEMail($email);
            }

            $message = JRequest::getString('message', NULL);
            if ($message != NULL) {
                $cart->setMessage($message);
            }
        }

        /**
         * ADDRESS UPDATE
         *
         */


        $addressFactory = new EventgalleryLibraryFactoryAddress();

        /**
         * @var JForm $billingform
         */
        $billingform = JForm::getInstance('billing', $xmlPath . 'billingaddress.xml');
        $billingform->bind(JRequest::get('post'));
        $billingvalidation = $billingform->validate(JRequest::get('post'));
        if ($billingvalidation !== true) {
            $errors = array_merge($errors, $billingform->getErrors());
        } else {

            $billingdata = array();
            foreach ($billingform->getFieldset() as $field) {
                $billingdata[$field->name] = $field->value;
            }

            /**
             * @var EventgalleryLibraryAddress $billingAddress
             */
            $billingAddress = $cart->getBillingAddress();
            if ($billingAddress != NULL) {
                $billingdata['id'] = $billingAddress->getId();
            }

            $billingAddress = $addressFactory->createStaticAddress($billingdata, 'billing_');

            $cart->setBillingAddress($billingAddress);


            $shiptodifferentaddress = JRequest::getString('shiptodifferentaddress', NULL);
            if ($shiptodifferentaddress == 'true') {
                /**
                 * @var JForm $shippingform
                 */
                $shippingform = JForm::getInstance('shipping', $xmlPath . 'shippingaddress.xml');
                $shippingform->bind(JRequest::get('post'));
                $shippingvalidation = $shippingform->validate(JRequest::get('post'));
                if ($shippingvalidation !== true) {
                    $errors = array_merge($errors, $shippingform->getErrors());
                } else {
                    $shippingdata = array();
                    foreach ($shippingform->getFieldset() as $field) {
                        $shippingdata[$field->name] = $field->value;
                    }

                    $shippingAddress = $cart->getShippingAddress();
                    if ($shippingAddress != NULL && $shippingAddress->getId() != $billingAddress->getId()) {
                        $shippingdata['id'] = $shippingAddress->getId();
                    }

                    /**
                     * @var EventgalleryLibraryAddress $shippingAddress
                     */
                    $shippingAddress = $addressFactory->createStaticAddress($shippingdata, 'shipping_');

                    $cart->setShippingAddress($shippingAddress);
                }
            } elseif ($shiptodifferentaddress == 'false') {
                $cart->setShippingAddress($billingAddress);
            }
        }

        return $errors;
    }

    public function calculateCart()
    {
        $cart = $this->getCart();

        if ($cart->getShippingMethod() && $cart->getShippingMethod()->isEligible($cart)==false) {
            $cart->setShippingMethod(null);
        }

        if ($cart->getPaymentMethod() && $cart->getPaymentMethod()->isEligible($cart)==false) {
            $cart->setPaymentMethod(null);
        }

        // set subtotal;
        /**
         * @var  float $subtotal
         */
        $subtotal = 0;
        /**
         * @var EventgalleryLibraryImagelineitem $lineitem
         */

        $subtotalCurrency = "";

        foreach ($cart->getLineItems() as $lineitem) {
            $subtotal += $lineitem->getPrice()->getAmount();
            $subtotalCurrency = $lineitem->getPrice()->getCurrency();
        }

        $cart->setSubTotal(new EventgalleryLibraryCommonMoney($subtotal, $subtotalCurrency));



        /**
         * @var EventgalleryLibraryManagerSurcharge $surchargeMgr
         */
        $surchargeMgr = EventgalleryLibraryManagerSurcharge::getInstance();
        $cart->setSurcharge($surchargeMgr->calculateSurcharge($cart));

        /**
         * @var  float $total
         */
        $total = $subtotal;
        if ($cart->getSurcharge() != NULL) {
            $total += $cart->getSurcharge()->getPrice()->getAmount();
        }
        if ($cart->getShippingMethod() != NULL) {
            $total += $cart->getShippingMethod()->getPrice()->getAmount();
        }
        if ($cart->getPaymentMethod() != NULL) {
            $total += $cart->getPaymentMethod()->getPrice()->getAmount();
        }

        $cart->setTotal(new EventgalleryLibraryCommonMoney($total, $subtotalCurrency));

    }

}
