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

/** @noinspection PhpUndefinedClassInspection */
class EventgalleryControllerEvent extends JControllerForm
{

	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param   EventgalleryModelEvent  $model      The data model object.
	 * @param   array         $validData  The validated data.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function postSaveHook(EventgalleryModelEvent $model, $validData = array())
	{

		
        $oldFolder = JRequest::getVar("oldfolder", null);
		$newFolder = $validData['folder'];
		
		# Rename folder now:
		$basedir=JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'eventgallery'.DIRECTORY_SEPARATOR ;

		if ($oldFolder!=null && strcmp($oldFolder, $newFolder)!=0)
		{
			rename($basedir.$oldFolder, $basedir.$newFolder);
			$model->changeFolderName($oldFolder, $newFolder);
		}

		if ($this->task == 'save')
		{
			$this->setRedirect(JRoute::_('index.php?option=com_eventgallery&view=events', false));
		}
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since   2.5
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Event', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_eventgallery&view=events' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}
}
