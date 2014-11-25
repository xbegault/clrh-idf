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


class EventgalleryViewPublishFileByMail extends EventgalleryLibraryCommonView
{
	function display($tpl = null)
	{		
		
		$model = $this->getModel();

		$cids = JRequest::getVar( 'cid', array(0), '', 'array' );
		if (count( $cids ))
		{
			foreach($cids as $cid) {
				
					$model->setId($cid);
					$file      = $model->getData();
					
			}
		}

		

		$this->assignRef('file',		$file);

		parent::display();
	}
}

