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


/** @noinspection PhpUndefinedClassInspection */
class EventModelEvent extends JModelLegacy
{
    protected $_pagination;

    function __construct()
    {
        parent::__construct();

        $app = JFactory::getApplication();

        $limitstart = JRequest::getInt('limitstart', 0);
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $this->setState('limit', $limit);
        $this->setState('com_eventgallery.event.limitstart', $limitstart);
    }

    function getEntries($foldername = '', $limitstart = 0, $limit = 0, $imagesForEvents = 0)
    {
        if ($limit == 0) {
            $limit = $this->getState('limit');
        }

        if ($limitstart == 0) {
            $limitstart = $this->getState('com_eventgallery.event.limitstart');
        }

        // fix issue with events list where paging was working
        if ($limitstart < 0) {
            $limitstart = 0;
        }
        // do the picasa web handling here

        /**
         * @var EventgalleryLibraryManagerFolder $folderMgr
         */
        $folderMgr = EventgalleryLibraryManagerFolder::getInstance();
        $folder = $folderMgr->getFolder($foldername);

        if ($folder == null) {
        	return Array();
        }

        return $folder->getFiles($limitstart, $limit, $imagesForEvents);

    }

    function getPagination($folder = '')
    {

        if (empty($this->_pagination)) {

            $total = $this->getTotal($folder);
            $limit = (integer)$this->getState('limit');
            $limitstart = $this->getState('com_eventgallery.event.limitstart');


            if ($limitstart > $total || JRequest::getVar('limitstart', '0') == 0) {
                $limitstart = 0;
                $this->setState('com_eventgallery.event.limitstart', $limitstart);
            }

            $this->_pagination = new JPagination($total, $limitstart, $limit);
        }

        return $this->_pagination;

    }

    function getTotal($folder = '')
    {
        /**
         * @var EventgalleryLibraryManagerFolder $folderMgr
         */
        $folderMgr = EventgalleryLibraryManagerFolder::getInstance();
        $folder = $folderMgr->getFolder($folder);
        if ($folder == null) {
        	return 0;
        }
        return $folder->getFileCount(true);
    }


}
