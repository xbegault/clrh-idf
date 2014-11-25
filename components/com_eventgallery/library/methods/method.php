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
 * Provides an abstract class with the base implementation for each method
 *
 * Class EventgalleryLibraryMethodsMethod
 */
abstract class EventgalleryLibraryMethodsMethod extends EventgalleryLibraryDatabaseObject implements EventgalleryLibraryInterfaceMethod
{

    protected $_object = null;
    protected $_object_id = null;
    protected $_data = null;
    protected $_ls_displayname = null;
    protected $_ls_description = null;
    /**
     * name of the sql table like #__foobar
     */
    protected $_methodtablename = null;
    /**
     * the name of the Table Class
     */
    protected $_methodtable = null;

    public function __construct($object)
    {
        if ($object instanceof stdClass) {
            $this->_object = $object;
            $this->_object_id = $object->id;
        } else {
            $this->_object_id = $object;
            $this->_loadMethodData();
        }

        $this->_ls_displayname = new EventgalleryLibraryDatabaseLocalizablestring($this->_object->displayname);
        $this->_ls_description = new EventgalleryLibraryDatabaseLocalizablestring($this->_object->description);

        parent::__construct();
    }

    /**
     * Load the image type by id
     */
    protected function _loadMethodData()
    {
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($this->_methodtablename);
        $query->where('id=' . $db->Quote($this->_object_id));

        $db->setQuery($query);
        $this->_object = $db->loadObject();
    }

    static public  function getClassName() {
        return "Abstract Method Class. Do overwrite this method.";
    }


    /**
     * @return string the id
     */
    public function getId()
    {
        return $this->_object->id;
    }

    /**
     * @return EventgalleryLibraryCommonMoney the price value
     */
    public function getPrice()
    {
        return new EventgalleryLibraryCommonMoney($this->_object->price, $this->_object->currency);
    }

    /**
     * @return string display name
     */
    public function getName()
    {
        return $this->_object->name;
    }

    public function isPublished() {
        return $this->_object->published==1;
    }

    /**
     * @return string display name
     */
    public function getDisplayName()
    {
        return $this->_ls_displayname->get();
    }

    /**
     * @return string display name
     */
    public function getDescription()
    {
        return $this->_ls_description->get();
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->_object->default == 1 ? true : false;
    }

    /**
     * @return stdClass|null
     */
    public function getData()
    {
        if (null == $this->_data) {
            $this->_data = json_decode($this->_object->data);
        }

        return $this->_data;
    }

    /**
     * sets a new data object
     *
     * @param stdClass $data
     */
    public function setData(stdClass $data) {

        $this->_object->data = json_encode($data);

        $this->_storeMethod();
        $this->_data = null;
    }

    /**
     * returns the amount of tax for this item
     *
     * @return float
     */
    public function getTax() {
        return $this->getPrice()*$this->getTaxrate()/100;
    }
    /**
     * @return float
     */
    public function getTaxrate() {
        return $this->_object->taxrate;
    }

    public function getOrdering() {
        return $this->_object->ordering;
    }

    public function processOnOrderSubmit($lineitemcontainer) {
        return true;
    }

    public function onIncomingExternalRequest() {

    }

    public function onPrepareAdminForm($form) {
        return $form;
    }


    public function onSaveAdminForm($validData) {
        return true;
    }

    public function getMethodReviewContent($lineitemcontainer) {
        return "";
    }


    public function getMethodConfirmContent($lineitemcontainer) {
        return "";
    }

    protected function _storeMethod()
    {
        $data = $this->_object;
        $this->store((array)$data, $this->_methodtable);
    }

}
