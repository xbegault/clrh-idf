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
 * This interface provides a general interface for all methods. All methods are configured using the database.
 *
 * Class EventgalleryLibraryInterfaceMethod
 */
interface EventgalleryLibraryInterfaceMethod
{
    /**
     * Returns the id of the current method.
     *
     * @return string the id
     */
    public function getId();

    /**
     * Returns the price of the method
     *
     * @return EventgalleryLibraryCommonMoney the price value
     */
    public function getPrice();

    /**
     * Returns the name of the method
     *
     * @return string display name
     */
    public function getName();

    /**
     * Returns the display name of the method for the current locale
     *
     * @return string display name
     */
    public function getDisplayName();

    /**
     * Returns the description of the method for the current locale
     *
     * @return string display name
     */
    public function getDescription();

    /**
     * returns true if this is the default method.
     *
     * @return bool
     */
    public function isDefault();

    /**
     * Returns an object containing the data of a method.
     *
     * @return mixed|null
     */
    public function getData();

    /**
     * @param stdClass $data
     */
    public function setData(stdClass $data);

    /**
     * calculates the included tax.
     *
     *
     * returns the amount of tax for this item
     *
     * @return float
     */
    public function getTax();

    /**
     * Return the tax rate. 100 ==  100%
     *
     * @return int
     */
    public function getTaxrate();

    /**
     * Returns if this method can be used with the current cart.
     *
     * @param EventgalleryLibraryLineitemcontainer $cart
     *
     * @return bool
     */
    public function isEligible($cart);


    /**
     * returns the type code of this method
     *
     * @return int
     */
    public function getTypeCode();


    /**
     *
     * Event which is called if an order is about to be submitted
     *
     * @param $lineitemcontainer EventgalleryLibraryLineitemcontainer
     *
     * @return bool|array true or array with errors
     */
    public function processOnOrderSubmit($lineitemcontainer);

    /**
     * is called if there is an incoming request from an external system for a payment method.
     *
     * @return
     */
    public function onIncomingExternalRequest();

    /**
     * Call this method if you want to prepare the form to edit this method
     *
     * @param JForm $form
     * @return JForm
     */
    public function onPrepareAdminForm($form);

    /**
     * Once the editing is done any the data has to be saved, call this method
     *
     * @param array $validData
     * @return boolean
     */
    public function onSaveAdminForm($validData);

    /**
     * This method returns content which can be shown if the method is displayed on a review page.
     * A review page is a page where the method is attached to a cart instead of an order
     *
     * @param $lineitemcontainer EventgalleryLibraryLineitemcontainer
     * @return String
     */
    public function getMethodReviewContent($lineitemcontainer);


    /**
     * This method returns content which can be shown if the method is displayed on a review page.
     * A review page is a page where the method is attached to an order instead of a cart
     * Keep in mind that this content is also part of emails.
     *
     * @param $lineitemcontainer EventgalleryLibraryLineitemcontainer
     * @return String
     */
    public function getMethodConfirmContent($lineitemcontainer);

}
