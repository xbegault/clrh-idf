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


class EventgalleryViewFiles extends EventgalleryLibraryCommonView
{

    protected $item;
    protected $items;
    protected $pagination;
    protected $state;

    function display($tpl = null)
	{
        $this->item = $this->get('Item');
        $this->state		= $this->get('State');
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();
        EventgalleryHelpersEventgallery::addSubmenu('files');      
        $this->sidebar = JHtmlSidebar::render();
        return parent::display($tpl);
	}

    protected function addToolbar() {
        $text = $this->item->folder;
        JToolBarHelper::title(   JText::_( 'COM_EVENTGALLERY_EVENTS' ).': <small><small>[ ' . $text.' ]</small></small>' );

        JToolBarHelper::cancel('files.cancel', 'Close');


        JToolBarHelper::custom('files.publish', 'eg-published');
        JToolBarHelper::custom('files.unpublish', 'eg-published-inactive');

        JToolBarHelper::custom('files.allowcomments', 'eg-comments');
        JToolBarHelper::custom('files.disallowcomments', 'eg-comments-inactive');


        JToolBarHelper::custom('files.ismainimage', 'eg-mainimage');
        JToolBarHelper::custom('files.isnotmainimage', 'eg-mainimage-inactive');

        JToolBarHelper::custom('files.isnotmainimageonly', 'eg-mainimageonly');
        JToolBarHelper::custom('files.ismainimageonly', 'eg-mainimageonly-inactive');

        JToolBarHelper::spacer(50);

        JToolBarHelper::deleteList(JText::_( 'COM_EVENTGALLERY_EVENT_IMAGE_ACTION_DELETE_ALERT' ), 'files.delete');
        $bar = JToolbar::getInstance('toolbar');
        $bar->appendButton('Confirm', 'COM_EVENTGALLERY_EVENT_CLEAR_ORDERING_ALERT', 'trash', 'COM_EVENTGALLERY_EVENT_CLEAR_ORDERING',  'files.clearOrdering', false);        
        
        if (EventgalleryLibraryFolderLocal::canHandle($this->item->folder)) {
            JToolBarHelper::spacer(100);
            $bar->appendButton('Link', 'edit', 'COM_EVENTGALLERY_BUTTON_EDIT_DESC',  JRoute::_('index.php?option=com_eventgallery&task=event.edit&id='.$this->item->id), false);
            $bar->appendButton('Link', 'upload', 'COM_EVENTGALLERY_BUTTON_UPLOAD_DESC',  JRoute::_('index.php?option=com_eventgallery&task=upload.upload&folderid='.$this->item->id), false);
        }
    }
}
