<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');

/** @noinspection PhpUndefinedClassInspection */
class EventgalleryController extends JControllerLegacy
{
	
	protected $default_view = 'eventgallery';


    /**
     * constructor (registers additional tasks to methods)
     * @return \EventgalleryController
     */
	function __construct()
	{
		parent::__construct();	
	}
	/*
	 * Standard display method
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy  A JControllerLegacy object to support chaining.

	 */
	public function display($cachable = false, $urlparams = false)
	{
		parent::display($cachable, $urlparams);
	}


	/**
	 * function so remove every cache-entry
	 */
	function clearCache()
	{
		
		$path=JPATH_BASE.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_eventgallery_images';
		rrmDir($path);
		$path=JPATH_BASE.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_eventgallery_picasa';
	    rrmDir($path);
		$path=JPATH_BASE.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'com_eventgallery';
		rrmDir($path);
		$msg = JText::_( 'COM_EVENTGALLERY_CLEAR_CACHE_DONE' );
		$this->setRedirect( 'index.php?option=com_eventgallery', $msg );
	}

    /**
     * removes carts which are older than XX days.
     */
    function removeOldCarts() {

        $db = JFactory::getDBO();

        // remove the old carts
        $query = $db->getQuery(true);
        $query->delete('#__eventgallery_cart');
        $query->where($db->quoteName(modified).' < DATE_SUB(NOW(), INTERVAL 30 DAY)');
        $db->setQuery($query);
        $db->execute();
        $removedCartsCount = $db->getAffectedRows();


        // remove the old imagelineitems
        $query = $db->getQuery(true);
        $query->delete('#__eventgallery_imagelineitem');
        $query->where('lineitemcontainerid not in (select id from #__eventgallery_cart)');
        $query->where('lineitemcontainerid not in (select id from #__eventgallery_order)');
        $db->setQuery($query);
        $db->execute();

        // remove the old servicelineitems
        $query = $db->getQuery(true);
        $query->delete('#__eventgallery_servicelineitem');
        $query->where('lineitemcontainerid not in (select id from #__eventgallery_cart)');
        $query->where('lineitemcontainerid not in (select id from #__eventgallery_order)');
        $db->setQuery($query);
        $db->execute();

        // do the redirect
        $msg = JText::sprintf('COM_EVENTGALLERY_CART_CLEANUP_DONE', $removedCartsCount );
        $this->setRedirect( 'index.php?option=com_eventgallery', $msg );

    }
	
}

function rrmdir($dir) {
    foreach(glob($dir . '/*') as $file) {
        if(is_dir($file))
            rrmdir($file);
        else
            unlink($file);
    }
    rmdir($dir);
}
