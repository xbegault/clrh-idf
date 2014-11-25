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

jimport( 'joomla.application.component.view');
jimport( 'joomla.html.pagination');


/** @noinspection PhpUndefinedClassInspection */
class EventgalleryViewEvents extends EventgalleryLibraryCommonView
{

	protected $items;
	protected $pagination;
    protected $state;
    protected $foldertags;

	function display($tpl = null)
	{				
		// Get data from the model
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->foldertags = $this->get('Tags');
        $this->pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();
        EventgalleryHelpersEventgallery::addSubmenu('events');
        $this->sidebar = JHtmlSidebar::render();

        return parent::display($tpl);
	}

	protected function addToolbar() {

		JToolBarHelper::title(   JText::_( 'COM_EVENTGALLERY_EVENTS' ), 'generic.png' );
		//JToolBarHelper::deleteList();
		JToolBarHelper::addNew('event.add');
		JToolBarHelper::editList('event.edit');
		JToolBarHelper::publishList('events.publish');
		JToolBarHelper::unpublishList('events.unpublish');
        JToolBarHelper::publishList('events.cartable','COM_EVENTGALLERY_EVENT_CARTABLE');
        JToolBarHelper::unpublishList('events.notcartable','COM_EVENTGALLERY_EVENT_NOT_CARTABLE');
		JToolBarHelper::deleteList('Remove all selected Events?','events.delete','Remove');
		JToolBarHelper::preferences('com_eventgallery', '550');

		JToolBarHelper::spacer(100);

		$bar = JToolbar::getInstance('toolbar');

		// Add a trash button.
				
		$bar->appendButton('Confirm', 'COM_EVENTGALLERY_CLEAR_CACHE_ALERT', 'trash', 'COM_EVENTGALLERY_SUBMENU_CLEAR_CACHE',  'clearCache', false);
		$bar->appendButton('Link', 'checkin', 'COM_EVENTGALLERY_SUBMENU_SYNC_DATABASE',  JRoute::_('index.php?option=com_eventgallery&view=sync'), false);

        $version =  new JVersion();
        if ($version->isCompatible('3.0')) {
            JHtml::_('bootstrap.modal', 'collapseModal');
            $title = JText::_('JTOOLBAR_BATCH');

            // Instantiate a new JLayoutFile instance and render the batch button
            $layout = new JLayoutFile('joomla.toolbar.batch');

            $dhtml = $layout->render(array('title' => $title, 'height' => 200));
            $bar->appendButton('Custom', $dhtml, 'batch');
        }

        $options= array();
        foreach($this->foldertags as $key=>$value) {
            $options[] = JHtml::_('select.option', $key, $value);
        }
        $options[] = JHtml::_('select.option', '------', 'JNONE');
        $options[] = JHtml::_('select.option', '*', 'JALL');


        JHtmlSidebar::addFilter(
            JText::_('COM_EVENTGALLERY_EVENT_FILTER_TAG'),
            'filter_tag',
            JHtml::_('select.options', $options, 'value', 'text', $this->state->get('filter.tag'), true)
        );


        $options= array();
        $options[] = JHtml::_('select.option', 'local', JText::_('COM_EVENTGALLERY_EVENT_TYPE_LOCAL'));
        $options[] = JHtml::_('select.option', 'picasa', JText::_('COM_EVENTGALLERY_EVENT_TYPE_PICASA'));
        $options[] = JHtml::_('select.option', '*', 'JALL');

        JHtmlSidebar::addFilter(
            JText::_('COM_EVENTGALLERY_EVENT_FILTER_TYPE'),
            'filter_type',
            JHtml::_('select.options', $options, 'value', 'text', $this->state->get('filter.type'), true)
        );

        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_CATEGORY'),
            'filter_category',
            JHtml::_('select.options', JHtml::_('category.options', 'com_eventgallery'), 'value', 'text', $this->state->get('filter.category'))
        );



	}

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     *
     * @since   3.0
     */
    protected function getSortFields()
    {
        return array(
            'folder' => JText::_('COM_EVENTGALLERY_EVENTS_FOLDERNAME'),
            'date' => JText::_('COM_EVENTGALLERY_EVENTS_EVENT_DATE'),
            'ordering' => JText::_('COM_EVENTGALLERY_EVENTS_ORDER'),
            'published' => JText::_('COM_EVENTGALLERY_EVENTS_PUBLISHED'),
            'cartable' => JText::_('COM_EVENTGALLERY_EVENTS_CARTABLE')
        );
    }
}

