<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.component.controllerform' );

require_once(__DIR__.'/../controller.php');

class EventgalleryControllerSync extends JControllerForm
{


    /**
     * The root folder for the physical images
     *
     * @var string
     */

    protected $default_view = 'sync';

    public function __construct($config = array())
    {

        parent::__construct($config);
    }

	public function getModel($name = 'Event', $prefix ='EventgalleryModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * just cancels this view
     */
	public function cancel($key = NULL) {
		$this->setRedirect( 'index.php?option=com_eventgallery&view=events');
	}

    /**
     * starts the syncronization.
     *
     * @param bool $cachable
     * @param array $urlparams
     */
    public function start($cachable = false, $urlparams = array()) {
        JSession::checkToken();

        // make sure the database contains the right stuff
        $this->addNewFolders();

        JRequest::setVar('layout', 'sync');
        $this->display($cachable, $urlparams);
    }

    /**
     * Syncs one folder
     *
     * @param bool $cachable
     * @param array $urlparams
     */
    public function process($cachable = false, $urlparams = array()) {
        JSession::checkToken();
        $folder = JRequest::getString('folder','');
        /**
         * @var EventgalleryLibraryManagerFolder $folderMgr
         */
        $folderMgr = EventgalleryLibraryManagerFolder::getInstance();
        $syncResult = $folderMgr->syncFolder($folder);

        $result = Array();
        $result['folder'] = htmlspecialchars($folder);

        if ($syncResult == EventgalleryLibraryManagerFolder::$SYNC_STATUS_NOSYNC) {
            $result['status'] = "nosync";
        }

        if ($syncResult == EventgalleryLibraryManagerFolder::$SYNC_STATUS_SYNC)  {
            $result['status'] = "sync";
        }

        if ($syncResult == EventgalleryLibraryManagerFolder::$SYNC_STATUS_DELTED)  {
            $result['status'] = "deleted";
        }

        echo json_encode($result);
    }


    /**
     * adds new folders to the databases
     */
    public function addNewFolders() {
        /**
         * @var EventgalleryLibraryManagerFolder $folderMgr
         */
        $folderMgr = EventgalleryLibraryManagerFolder::getInstance();
        $folderMgr->addNewFolders();

    }

}
