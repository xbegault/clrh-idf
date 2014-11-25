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


class EventgalleryViewSingleimage extends EventgalleryLibraryCommonView
{
    /**
     * @var JRegistry
     */
    protected $params;
    protected $state;
    protected $use_comments;
    protected $currentItemid;
    /**
     * @var EventgalleryLibraryFolder
     */
    protected $folder;

    /**
     * @var EventgalleryLibraryFile
     */
    protected $file;

    protected $position;
    protected $imageset;
    protected $model;
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

        $model = $this->getModel('singleimage');
        $modelComment = JModelLegacy::getInstance('Comment', 'EventgalleryModel');
        $model->getData(JRequest::getString('folder'), JRequest::getString('file'));

        $this->model = $model;
        $this->file = $model->file;
        $this->folder = $this->file->getFolder();
        $this->position = $model->position;

        /** Default Page fallback
         * @var JMenu $active
        */
        $active = $app->getMenu()->getActive();
        if (NULL == $active) {
            $this->params->merge($app->getMenu()->getDefault()->params);
            $active = $app->getMenu()->getDefault();
        }

        $this->currentItemid = $active->id;

        $this->use_comments = $this->params->get('use_comments');

        if ($this->use_comments) {

            $this->commentform = $modelComment->getForm();

        }

        if (!is_object($this->folder) || $this->folder->isPublished() != 1) {
            JError::raiseError(404, JText::_('COM_EVENTGALLERY_EVENT_NO_PUBLISHED_MESSAGE'));
        }        


        if (!isset($this->file) || strlen($this->file->getFileName()) == 0 || $this->file->isPublished() != 1) {
            JError::raiseError(404, JText::_('COM_EVENTGALLERY_SINGLEIMAGE_NO_PUBLISHED_MESSAGE'));           
        }

        if (!$this->folder->isVisible()) {
            $user = JFactory::getUser();
            if ($user->guest) {

                $redirectUrl = JRoute::_("index.php?option=com_eventgallery&view=singleimage&folder=" . $this->folder->getFolderName()."&file=".$this->file->getFileName(), false);
                $redirectUrl = urlencode(base64_encode($redirectUrl));
                $redirectUrl = '&return='.$redirectUrl;
                $joomlaLoginUrl = 'index.php?option=com_users&view=login';
                $finalUrl = JRoute::_($joomlaLoginUrl . $redirectUrl, false);
                $app->redirect($finalUrl);
            } else {
                $this->setLayout('noaccess');
            }
        }

        $password = JRequest::getString('password', '');
        $accessAllowed = EventgalleryHelpersFolderprotection::isAccessAllowed($this->folder, $password);
        if (!$accessAllowed) {
            $app->redirect(
                JRoute::_("index.php?option=com_eventgallery&view=password&folder=" . $this->folder->getFolderName(), false)
            );
        }


        $this->imageset = $this->folder->getImageTypeSet();

        $pathway = $app->getPathWay();

        if ($active->query['view']=='categories') {
            EventgalleryHelpersCategories::addCategoryPathToPathway($pathway, JRequest::getInt('catid', 0), $this->folder->getCategoryId(), $this->currentItemid);
        }

        $pathway->addItem(        
            $this->folder->getDescription(), JRoute::_('index.php?option=com_eventgallery&view=event&folder=' . $this->folder->getFolderName())
        );
        $pathway->addItem($model->position . ' / ' . $model->overallcount);

        $this->_prepareDocument();

        parent::display($tpl);
    }


    /**
     * Prepares the document
     */
    protected function _prepareDocument()
    {
        $app    = JFactory::getApplication();
        $menus  = $app->getMenu();
        $title = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu)
        {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        }
        

        $title = $this->params->get('page_title', '');

        if ($this->folder->getDescription()) {
            $title = $this->folder->getDescription();
        }
        
        $title .= " - ".$this->position.' / '.$this->folder->getFileCount();


        // Check for empty title and add site name if param is set
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        if (empty($title)) {
            $title = $this->folder->getDescription();
        }
        
        if ($this->document) {

            $this->document->setTitle($title);

            if ($this->folder->getText())
            {
                $this->document->setDescription(strip_tags($this->folder->getText()));
            }
            elseif (!$this->folder->getText() && $this->params->get('menu-meta_description'))
            {
                $this->document->setDescription($this->params->get('menu-meta_description'));
            }

            if ($this->params->get('menu-meta_keywords'))
            {
                $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
            }

            if ($this->params->get('robots'))
            {
                $this->document->setMetadata('robots', $this->params->get('robots'));
            }
        }
    }

}
