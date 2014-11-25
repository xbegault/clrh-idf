<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

class EventController extends JControllerLegacy
{
    public function display($cachable = false, $urlparams = array())
    {

        $password = JRequest::getString('password', '');
        $folder = JRequest::getString('folder', '');
        /**
         * @var EventgalleryLibraryManagerFolder $folderMgr
         */
        $folderMgr = EventgalleryLibraryManagerFolder::getInstance();
        $folder = $folderMgr->getFolder($folder);


        // we need to do this only if someone entered a password.
        // the views will protect themselfs
        $accessAllowed = EventgalleryHelpersFolderprotection::isAccessAllowed($folder, $password);
        if (strlen($password) > 0 && !$accessAllowed) {
            $msg = JText::_('COM_EVENTGALLERY_PASSWORD_FORM_ERROR');
            $this->setRedirect(JRoute::_("index.php?option=com_eventgallery&view=password&folder=" . $folder->getFolderName(), false), $msg);
            $this->redirect();
        }

        parent::display($cachable, $urlparams);
    }

}
