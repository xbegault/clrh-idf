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
 * Class EventgalleryLibraryDatabaseLocalizablestring handles a string which contains multiple languages.
 * As of now this string has to be json encoded and contains key value pairs where the key is the locale like en_US or de_DE
 */

class EventgalleryLibraryDatabaseLocalizablestring
{

    var $_entries = NULL;

    function __construct($jsonstring)
    {
        $this->_entries = json_decode($jsonstring);
    }

    /**
     * @return string returns the encoded entries so you can store them into a database.
     */
    public function getEncodedString()
    {
        return json_encode($this->_entries);
    }

    /**
     * @param string $langTag the language tag you want to get the value for. If the tag is null, the current lang is used.
     *
     * @return string the value for the given language.
     */
    public function get($langTag = NULL)
    {

        if ($langTag == NULL) {
            $lang = JFactory::getLanguage();
            $langTag = $lang->getTag();
        }

        if (isset($this->_entries->$langTag)) {
            return $this->_entries->$langTag;
        }

        return NULL;
    }

    /**
     * @param string $langTag the language tag like en_US or de_DE
     * @param string $value   the value for the specified language
     */
    public function set($langTag, $value)
    {
        if ($value == NULL || $langTag == NULL) {
            return;
        }
        $this->_entries->$langTag = $value;
    }

}