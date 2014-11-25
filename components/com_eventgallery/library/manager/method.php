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

abstract class EventgalleryLibraryManagerMethod extends EventgalleryLibraryManagerManager
{


    /**
     * @var array
     */
    protected $_methods;

    protected $_tablename;

    /**
     * @var array
     */
    protected $_methods_published;



    function __construct()
    {

    }

    /**
     * @param bool $publishedOnly
     *
     * @return array
     */
    public function getMethods($publishedOnly = true)
    {

        if ($this->_methods == null) {

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from($this->_tablename);
            $query->order('ordering');
            $db->setQuery($query);
            $items = $db->loadObjectList();

            $this->_methods = array();
            $this->_methods_published = array();

            foreach ($items as $item) {
                /**
                 * @var EventgalleryLibraryInterfaceMethod $itemObject
                 */
                if (class_exists($item->classname)) {
                    $itemObject = new $item->classname($item);
                    if ($item->published == 1) {
                        $this->_methods_published[$itemObject->getId()] = $itemObject;
                    }
                    $this->_methods[$itemObject->getId()] = $itemObject;
                }
            }
        }
        if ($publishedOnly) {
            return $this->_methods_published;
        } else {
            return $this->_methods;
        }
    }

    /**
     * resets the cache
     */
    public function refreshMethods() {
        $this->_methods = NULL;
        $this->_methods_published = NULL;
    }

    /**
     * @return EventgalleryLibraryInterfaceMethod
     */
    public function getDefaultMethod()
    {
        $methods = $this->getMethods(true);
        foreach ($methods as $method) {
            /**
             * @var EventgalleryLibraryInterfaceMethod $method
             */
            if ($method->isDefault()) {
                return $method;
            }
        }

        $array_values = array_values($methods);
        if (isset($array_values[0])) {
            return $array_values[0];
        }
        
        return NULL;
    }

    /**
     * @param int  $methodid
     * @param bool $publishedOnly
     *
     * @return EventgalleryLibraryInterfaceMethod
     */
    public function getMethod($methodid, $publishedOnly)
    {

        $methods = $this->getMethods($publishedOnly);


        if (isset($methods[$methodid])) {
            return $methods[$methodid];
        }

        return null;
    }







}
