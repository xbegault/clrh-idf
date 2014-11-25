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

jimport('joomla.application.component.model');
jimport('joomla.html.pagination');


class OrderModelOrder extends JModelLegacy
{
    

    public function getItem() {

        $app = JFactory::getApplication();


        $orderid = $app->input->getString('id', '-1');


        $order = new EventgalleryLibraryOrder($orderid);

        /**
         * check of the current user is allowed to view this order
         */
        $user = JFactory::getUser();
        if ($user->id != $order->getUserId()) {
            return null;
        }

        return $order;
    }

}
