<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('joomla.application.pathway');
jimport('joomla.html.pagination');


class EventgalleryViewPassword extends EventgalleryLibraryCommonView
{
    /**
     * @var JRegistry
     */
    protected $params;
    protected $state;

    /**
     * @var EventgalleryLibraryFile
     */
    protected $file;

    /**
     * @var EventgalleryLibraryFolder
     */
    protected $folder;
    protected $formaction;

    /**
     * @var JDocument
     */
    public $document;


    function display($tpl = NULL)
    {
        /**
         * @var JSite $app
         */
        $app = JFactory::getApplication();
        $this->state = $this->get('State');
        $this->params = $app->getParams();

        $file = JRequest::getString('file', '');
        $folder = JRequest::getString('folder', '');

        /**
         * @var EventgalleryLibraryManagerFolder $folderMgr
         */
        $folderMgr = EventgalleryLibraryManagerFolder::getInstance();
        $folder = $folderMgr->getFolder($folder);

        if (!is_object($folder)) {
            $app->redirect(JRoute::_("index.php?", false));
        }

        $formAction = JRoute::_("index.php?option=com_eventgallery&view=event&folder=" . $folder->getFolderName());

        $this->folder = $folder;
        $this->file = $file;
        $this->formaction = $formAction;

        $this->_prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument()
    {
        $app = JFactory::getApplication();
        $menus = $app->getMenu();

        $title = NULL;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        }


        $title = $this->params->get('page_title', '');

        if ($this->folder->getDescription()) {
            $title = $this->folder->getDescription();
        }


        // Check for empty title and add site name if param is set
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        if (empty($title)) {
            $title = $this->folder->getDescription();
        }
        $this->document->setTitle($title);

        if ($this->folder->getText()) {
            $this->document->setDescription($this->folder->getText());
        } elseif (!$this->folder->getText() && $this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }
    }
}


