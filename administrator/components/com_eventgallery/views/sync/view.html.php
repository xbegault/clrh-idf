<?php 
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/** @noinspection PhpUndefinedClassInspection */
class EventgalleryViewSync extends EventgalleryLibraryCommonView
{

    protected $folders;

	function display($tpl = null)
	{

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('folder')
            ->from('#__eventgallery_folder');
        $db->setQuery($query);
        $this->folders = $db->loadColumn(0);

        $this->addToolbar();
		EventgalleryHelpersEventgallery::addSubmenu('overview');		
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolBarHelper::title(   JText::_('COM_EVENTGALLERY_SUBMENU_SYNC_DATABASE') );
		JToolBarHelper::cancel( 'sync.cancel', 'Close' );
	}
}

