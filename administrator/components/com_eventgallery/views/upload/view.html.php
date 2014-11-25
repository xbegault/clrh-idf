<?php 
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');
jimport( 'joomla.html.pagination');
jimport( 'joomla.html.html');


/** @noinspection PhpUndefinedClassInspection */
class EventgalleryViewUpload extends EventgalleryLibraryCommonView
{
    protected $item;

	function display($tpl = null)
	{

		$this->item		= $this->get('Item');

		$this->addToolbar();
		EventgalleryHelpersEventgallery::addSubmenu('events');		
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	protected function addToolbar() {
		JToolBarHelper::title(   JText::_( 'Event' ).': <small><small>[ upload ]</small></small>' );
		JToolBarHelper::cancel( 'upload.cancel', 'Close' );
		$bar = JToolbar::getInstance('toolbar');
		
		if (EventgalleryLibraryFolderLocal::canHandle($this->item->folder)) {
			JToolBarHelper::spacer(100);
			$bar->appendButton('Link', 'folder', 'COM_EVENTGALLERY_BUTTON_FILES_DESC',  JRoute::_('index.php?option=com_eventgallery&view=files&folderid='.$this->item->id), false);
			$bar->appendButton('Link', 'edit', 'COM_EVENTGALLERY_BUTTON_EDIT_DESC',  JRoute::_('index.php?option=com_eventgallery&task=event.edit&id='.$this->item->id), false);
		}
	}
}

