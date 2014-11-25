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

class EventgalleryLibraryFactoryServicelineitem extends EventgalleryLibraryFactoryFactory
{


    /**
     * @param $lineitemcontainerid
     * @param EventgalleryLibraryServicelineitem $lineitem
     *
     * @return EventgalleryLibraryServicelineitem
     */
    public function copyLineItem($lineitemcontainerid, $lineitem) {

        $data = get_object_vars($lineitem->_getInternalDataObject());
        unset($data['id']);
        $data['lineitemcontainerid'] = $lineitemcontainerid;
        $item = $this->store($data, 'Servicelineitem');

        return new EventgalleryLibraryServicelineitem($item);
    }


    /**
     * @param int $lineitemcontainerid
     * @param EventgalleryLibraryMethodsMethod $method
     */
    public function createLineitem($lineitemcontainerid, $method) {

        $quantity = 1;

        $item = array(
            'lineitemcontainerid' => $lineitemcontainerid,
            'quantity' => $quantity,
            'singleprice' => $method->getPrice()->getAmount(),
            'price' => $quantity * $method->getPrice()->getAmount(),
            'taxrate' => $method->getTaxrate(),
            'currency' => $method->getPrice()->getCurrency(),
            'methodid' => $method->getId(),
            'type' => $method->getTypeCode(),
            'name' => $method->getName()
        );

        $this->store($item, 'Servicelineitem');
    }
}
