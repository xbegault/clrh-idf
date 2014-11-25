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

class EventgalleryLibraryFactoryAddress extends EventgalleryLibraryFactoryFactory
{


    /**
     * @param $data
     * @param $prefix
     *
     * @return EventgalleryLibraryAddress
     */
    public function createStaticAddress($data, $prefix)
    {
        $newData = array();
        foreach ($data as $key => $value) {
            $newData[str_replace($prefix, '', $key)] = $value;
        }

        $row = $this->store($newData, 'Staticaddress');
        return new EventgalleryLibraryAddress($row);
    }

}
