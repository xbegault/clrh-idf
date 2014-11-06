<?php
/**
 * @copyright	Copyright (C) 2006-2014 joomleague.at. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_COMPONENT.DS.'models'.DS.'item.php');
jimport('joomla.application.component.modeladmin');

/**
 * Joomleague Component project Model
 *
 * @author	Marco Vaninetti <martizva@libero.it>
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueModelProject extends JoomleagueModelItem
{

	/**
	 * remove all players from a project
	 */
	function deleteProjectPlayers($project_id)
	{
		$result = false;
		if ($project_id > 0)
		{
			$query = "	DELETE
						FROM #__joomleague_team_player
						WHERE projectteam_id in (SELECT id FROM #__joomleague_project_team WHERE project_id=$project_id)";
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/**
	 * remove all staff from a project
	 */
	function deleteProjectStaff($project_id)
	{
		$result = false;
		if ($project_id > 0)
		{
			$query = "	DELETE
						FROM #__joomleague_team_staff
						WHERE projectteam_id in (SELECT id FROM #__joomleague_project_team WHERE project_id=$project_id)";
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/**
	* Method to return a season array (id, name)
	*
	* @access	public
	* @return	array seasons
	* @since	1.5.0a
	*/
	function getSeasons()
	{
		$query = 'SELECT id, name FROM #__joomleague_season AS s
					where s.published=1
					ORDER BY name ASC ';
		$this->_db->setQuery($query);
		if (!$result = $this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return array();
		}
		return $result;
	}

	/**
	* Method to return template independent projects (id, name)
	*
	* @access	public
	* @return	array
	* @since	1.5
	*/
	function getMasters()
	{
		$query = 'SELECT id, name FROM #__joomleague_project 
					WHERE master_template=0 
					ORDER BY name ASC ';
		$this->_db->setQuery($query);
		if (!$result = $this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}

	/**
	 * Method to load content project data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5.0a
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$this->_data = parent::getItem($this->_id);
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the project data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5.0a
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$project							= new stdClass();
			$project->id						= 0;

			$project->name						= null;
			$project->league_id					= 0;
			$project->season_id					= 0;
			$project->asset_id					= 0;
				
			$project->master_template			= 0;
			$project->sub_template_id			= 0;
			$project->extension				 	= null;
			$project->timezone                  = 'UTC';
			$project->project_type				= 0;

			$project->teams_as_referees		 	= 0;
			$project->sports_type_id			= 1;

			$project->start_date				= null;
			$project->start_time				= '15:30';

			$project->current_round_auto		= 1;
			$project->current_round			 	= 1;
			$project->auto_time				 	= 2880;

			$project->game_regular_time		 	= 90;

			$project->game_parts				= 2;
			$project->halftime					= 15;
			$project->points_after_regular_time	= '3,1,0';

			$project->use_legs					= null;

			$project->allow_add_time			= 0;
			$project->add_time					= 15;
			$project->points_after_add_time	 	= '3,1,0';
			$project->points_after_penalty		= '3,1,0';

			$project->fav_team					= null;
			$project->fav_team_color			= '';
			$project->fav_team_text_color		= '';
			$project->fav_team_highlight_type	= '';
			$project->fav_team_text_bold		= '';
			
			$project->template					= "default";

			$project->enable_sb				 	= null;
			$project->sb_catid					= 0;

			$project->published				 	= 0;
			$project->ordering					= 0;

			$project->checked_out				= 0;
			$project->checked_out_time			= 0;
			$project->ordering 					= 0;
			$project->alias						= null;
			$project->extended					= '';
			$project->modified					= null;
			$project->modified_by				= null;
			$project->picture					= null;
			$project->is_utc_converted			= 0;
				
			$this->_data						= $project;
			return (boolean) $this->_data;
		}

		return true;
	}

	/**
	* Method to return a project array (id, name)
	*
	* @access	public
	* @return	array project
	* @since	1.5.0a
	*/
	function getProjects()
	{
		$query = 'SELECT id, name 
					FROM #__joomleague_project AS p 
					where p.published=1
					ORDER BY ordering, name ASC ';
		$this->_db->setQuery($query);
		if (!$result = $this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}

	/**
	* Method to return a project array (id, name)
	*
	* @access	public
	* @return	array project
	* @since	1.5.0a
	*/
	function getProjectsBySportsType($sportstype_id, $season = null)
	{
		$query = "SELECT id, name FROM #__joomleague_project as p
					WHERE sports_type_id=$sportstype_id 
					AND p.published=1 ";
		if ($season) {
			$query .= ' AND season_id = '.(int) $season;
		}
		$query .=	" ORDER BY p.ordering, p.name ASC ";
		$this->_db->setQuery($query);
		if (!$result = $this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}

	/**
	* Method to return a project array (id, name)
	*
	* @access	public
	* @return	array project
	* @since	1.5.0a
	*/
	function getSeasonProjects($season = null)
	{
		$query = '	SELECT	p.id,
							concat(s.name," - ", p.name) AS name
					FROM #__joomleague_project AS p
					LEFT JOIN #__joomleague_season AS s ON p.season_id = s.id ';
		if ($season) {
			$query .= ' WHERE p.season_id = '.(int) $season;
		}
		$query .= ' ORDER BY p.ordering, p.name ASC ';
		$this->_db->setQuery($query);
		if (!$result = $this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}

	/**
	* Method to return the project teams array (id, name)
	*
	* @access	public
	* @return	array
	* @since 0.1
	*/
	function getProjectteams()
	{
		$query = "	SELECT	pt.id AS value,
							t.name AS text,
							t.notes
					FROM #__joomleague_team AS t
					LEFT JOIN #__joomleague_project_team AS pt ON pt.team_id=t.id
					WHERE pt.project_id=$this->_id
					ORDER BY name ASC ";
		$this->_db->setQuery($query);
		if (!$result = $this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}

	/**
	* Method to return the project teams array by team_id (team_id, name)
	*
	* @access	public
	* @return	array
	* @since	1.5.0a
	*/
	function getProjectteamsbyID()
	{
		if (empty($this->_id)){return false;}
		$query = "	SELECT	pt.team_id AS value,
							t.name AS text
							FROM #__joomleague_team AS t
							LEFT JOIN #__joomleague_project_team AS pt ON pt.team_id=t.id
							WHERE pt.project_id=$this->_id
							ORDER BY name ASC ";
		$this->_db->setQuery($query);
		if (!$result = $this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}

	/**
	 * returns associative array of parameters values from specified template
	 *
	 * @param string $template name
	 * @return array
	 */
	function getTemplateConfig ($template)
	{
		$result = '';
		$configvalues = array();
		$project =& $this->getData();

		// load template param associated to project, or to master template if none find.
		$query =	"	SELECT params
						FROM #__joomleague_template_config
						WHERE template = " . $this->_db->Quote($template) .
					"	AND project_id =" . (int) $project->id;
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadResult())
		{
			if ($project->master_template)
			{
				$query = '	SELECT	params
							FROM #__joomleague_template_config
							WHERE template = ' . $this->_db->Quote($template) . '
							AND project_id = ' . (int) $project->master_template;
				$this->_db->setQuery($query);
				if (!$result = $this->_db->loadResult())
				{
					JError::raiseWarning(	500,
											sprintf(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECT_MODEL_MISSING_MASTER_TEMPLATE'),
											$template));
					return array();
				}
			}
			else
			{
				JError::raiseWarning(	500,
										sprintf(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECT_MODEL_MISSING_TEMPLATE'),
										$template));
				return array();
			}
		}
		$params = explode("\n", trim($result));
		foreach ($params AS $param)
		{
			list($name, $value) = explode("=", $param);
			$configvalues[$name]=$value;
		}
		
		return $configvalues;
	}

	/**
	 * 
	 * @param $project_id
	 */
	function getProjectName($project_id)
	{
		$query = 'SELECT name 
					FROM #__joomleague_project 
					WHERE id='.$project_id;
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		return $result;
	}
	
	/**
	 * 
	 * Checks if an id is a valid Project
	 * @param $project_id
	 */
	function exists($project_id)
	{
		$query = 'SELECT id 
					FROM #__joomleague_project 
					WHERE id='.$project_id;
		$this->_db->setQuery($query);
		return (boolean)$this->_db->loadResult();
	}

	/**
	* Method to return the query that will obtain all ordering versus projects
	* It can be used to fill a list box with value/text data.
	*
	* @access  public
	* @return  string
	* @since 1.5
	*/
	function getOrderingAndProjectQuery()
	{
		return 'SELECT ordering AS value, name AS text FROM #__joomleague_project ORDER BY ordering';	
	}	
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'project', $prefix = 'table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.7
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_joomleague.project', 'project',
				array('load_data' => $loadData) );
		if (empty($form))
		{
			return false;
		}
		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.7
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_joomleague.edit.project.data', array());
		if (empty($data))
		{
			$data = $this->getData();
		}
		return $data;
	}
	
	/**
	 * Convert all games dates from specified project to utc
	 * 
	 * this assumes they were originally saved in tz set in project settings
	 */
	public function utc_fix_dates($project_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('m.id, m.match_date, p.timezone');
		$query->from('#__joomleague_match AS m');
		$query->join('INNER', '#__joomleague_round AS r ON r.id = m.round_id');
		$query->join('INNER', '#__joomleague_project AS p ON p.id = r.project_id');
		$query->where('p.id = '.(int) $project_id);
		$query->where('NOT (m.match_date is null OR m.match_date = \'0000-00-00 00:00:00\')');
		$db->setQuery($query);
		$res = $db->loadObjectList();
		$bConverted = false;
		foreach ($res as $match) 
		{
			if (is_numeric($match->timezone)) {
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_WRONG_TIMEZONE_FORMAT'));
				return false;
			}
			$utc_date = JFactory::getDate($match->match_date, $match->timezone)
			          ->setTimezone(new DateTimeZone('UTC'))
			          ->toSql();
			
			$query = $db->getQuery(true);
			
			$query->update('#__joomleague_match AS m');
			$query->set('m.match_date = '.$db->quote($utc_date));
			$query->where('m.id = '.$match->id);
			$db->setQuery($query);
			if (!$db->query()) {
				$this->setError($db->getError());
				return false;
			} else {
				$bConverted = true;
			}
		}
		if($bConverted) {
			$tblProject = new stdClass();
			$tblProject->id = $project_id;
			$tblProject->is_utc_converted = 1;
			if (!$db->updateObject('#__joomleague_project', $tblProject, 'id')) {
				$this->setError($db->getError());
				return false;
			}
		}
		return true;
	}
}
