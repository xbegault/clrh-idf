<?php
/**
 * @copyright	Copyright (C) 2006-2014 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.application.component.view' );
jimport ( 'joomla.html.parameter.element.timezones' );

/**
 * HTML View class for the Joomleague component
 *
 * @static
 *
 *
 * @package JoomLeague
 * @since 0.1
 */
class JoomleagueViewProject extends JLGView {
	function display($tpl = null) {
		$this->form = $this->get ( 'form' );
		
		$isNew = ($this->form->getValue ( 'id' ) < 1);
		if ($isNew) {
			$this->form->setValue ( 'is_utc_converted', null, 1 );
		}
		$edit = JRequest::getVar ( 'edit' );
		$copy = JRequest::getVar ( 'copy' );
		
		// add javascript
		$document = JFactory::getDocument ();
		$version = urlencode ( JoomleagueHelper::getVersion () );
		$document->addScript ( JUri::root () . 'administrator/components/com_joomleague/models/forms/project.js?v=' . $version );
		
		$this->assignRef ( 'edit', $edit );
		$this->assignRef ( 'copy', $copy );
		
		$extended = $this->getExtended ( $this->form->getValue ( 'extended'), 'project' );
		$this->assignRef ( 'extended', $extended );
		$this->addToolbar ();
		parent::display ( $tpl );
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since 1.7
	 */
	protected function addToolbar() {
		// Set toolbar items for the page
		if ($this->copy) {
			$toolbarTitle = JText::_ ( 'COM_JOOMLEAGUE_ADMIN_PROJECT_COPY_PROJECT' );
		} else {
			$toolbarTitle = (! $this->edit) ? JText::_ ( 'COM_JOOMLEAGUE_ADMIN_PROJECT_ADD_NEW' ) : JText::_ ( 'COM_JOOMLEAGUE_ADMIN_PROJECT_EDIT' ) . ': ' . $this->form->getValue ( 'name' );
			JToolBarHelper::divider ();
		}
		JToolBarHelper::title ( $toolbarTitle, 'ProjectSettings' );
		
		if (! $this->copy) {
			JLToolBarHelper::apply ( 'project.apply' );
			JLToolBarHelper::save ( 'project.save' );
		} else {
			JLToolBarHelper::save ( 'project.copysave' );
		}
		JToolBarHelper::divider ();
		if ((! $this->edit) || ($this->copy)) {
			JLToolBarHelper::cancel ( 'project.cancel' );
		} else {
			// for existing items the button is renamed `close`
			JLToolBarHelper::cancel ( 'project.cancel', 'COM_JOOMLEAGUE_GLOBAL_CLOSE' );
		}
		JToolBarHelper::help ( 'screen.joomleague', true );
	}
}
?>
