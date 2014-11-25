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

jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pagination');


class EventgalleryViewComments extends EventgalleryLibraryCommonView
{

	protected $items;
	protected $paginations;
	protected $filter;

	function display($tpl = null)
	{
		
		$app = JFactory::getApplication();
		


		
		$model = $this->getModel();		
		
		
		$this->filter = $app->getUserStateFromRequest('com_eventgallery.comments.filter','filter');
        if (is_array($this->filter)) {
            $this->filter = implode(";", $this->filter);
        }


		$model->setState('com_eventgallery.comments.filter',$this->filter);
				   
		$this->pagination = $model->getPagination();		
		$this->items = $model->getItems();

        JToolBarHelper::title(   JText::_( 'COM_EVENTGALLERY_COMMENTS' ), 'generic.png' );
        JToolBarHelper::deleteList('Remove all comments?','comments.delete','Remove');
        JToolBarHelper::editList('comment.edit','Edit');
        //JToolBarHelper::addNewX('editComment','New');
        JToolBarHelper::publishList('comments.publish');
        JToolBarHelper::unpublishList('comments.unpublish');

		EventgalleryHelpersEventgallery::addSubmenu('comments');		
		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

}
