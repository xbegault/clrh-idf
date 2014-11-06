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
defined('_JEXEC') or die('Restricted access');

/*
Model class for the Joomleague component

@author		JoomLeague Team <www.joomleague.at>
@package	JoomLeague
@since		0.1
*/

jimport('joomla.application.component.model');

require_once( JLG_PATH_SITE . DS . 'models' . DS . 'project.php' );

class JoomleagueModelAbout extends JoomleagueModelProject
{
	function getAbout()
	{
		$about = new stdClass();
		
		//Translations Hosted by
		$about->translations = '<a href="https://opentranslators.transifex.com/projects/p/joomleague/">https://opentranslators.transifex.com/projects/p/joomleague/</a>';
		//Repository Hosted by
		$about->repository = '<a href="http://gitorious.org/joomleague">http://gitorious.org/joomleague</a>';
		//version
		$version = JoomleagueHelper::getVersion();
		$revision = explode('.', $version);
		$about->version = '<a href="http://gitorious.org/joomleague/joomleague/commits/'.$revision[0].'.'.$revision[1].'.0/">' . $version . '</a>';
		
		//author
		$about->author = '<a href="http://stats.joomleague.at/authors.html">Joomleague-Team</a>';

		//page
		$about->page = 'http://www.joomleague.at';

		//e-mail
		$about->email = 'http://www.joomleague.at/forum/index.php?action=contact';

		//forum
		$about->forum = 'http://forum.joomleague.at';
		
		//bugtracker
		$about->bugs = 'http://bugtracker.joomleague.at';
		
		//wiki
		$about->wiki = 'http://wiki.joomleague.at';
		
		//date
		$about->date = '2013-01-07';

		//developer
		$about->developer = '<a href="http://stats.joomleague.at/authors.html" target="_blank">JoomLeague-Team</a>';

		//designer
		$about->designer = 'Kasi';
		$about->designer .= ', <a href="http://www.cg-design.net" target="_blank">cg design</a>&nbsp;(Carsten Grob) ';

		//icons
		$about->icons = '<a href="http://www.hollandsevelden.nl/iconset/" target="_blank">Jersey Icons</a> (Hollandsevelden.nl)';
		$about->icons .= ', <a href="http://www.famfamfam.com/lab/icons/silk/" target="_blank">Silk / Flags Icons</a> (Mark James)';
		$about->icons .= ', Panel images (Kasi)';

		//flash
		$about->flash = '<a href="http://teethgrinder.co.uk/open-flash-chart-2/" target="_blank">Open Flash Chart 2.x</a>';

		//graphoc library
		$about->graphic_library = '<a href="http://www.walterzorn.com" target="_blank">www.walterzorn.com</a>';
		
		//phpthumb class
		$about->phpthumb = '<a href="http://phpthumb.gxdlabs.com/" target="_blank">phpthumb.gxdlabs.com</a>';


		$this->_about = $about;

		return $this->_about;
	}

}
?>