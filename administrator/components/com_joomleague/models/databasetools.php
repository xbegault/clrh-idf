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

jimport('joomla.application.component.model');

/**
 * Joomleague Component DatabaseTools Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.0a
 */

class JoomleagueModelDatabaseTools extends JModelLegacy
{
	public function optimize()
	{
		$query="SHOW TABLES LIKE '%_joomleague%'";
		$this->_db->setQuery($query);
		$results=$this->_db->loadColumn();
		foreach ($results as $result)
		{
			$query='OPTIMIZE TABLE `'.$result.'`'; $this->_db->setQuery($query);
		}		
		
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	return true;
	}

	public function repair()
	{
		$query="SHOW TABLES LIKE '%_joomleague%'";
		$this->_db->setQuery($query);
		$results=$this->_db->loadColumn();
		foreach ($results as $result)
		{
			$query='REPAIR TABLE `'.$result.'`'; $this->_db->setQuery($query);
		}		
		
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
	
	public static function ImportTables()
	{
		$db = JFactory::getDbo();
	
		$imports=file_get_contents(JPATH_ADMINISTRATOR.'/components/com_joomleague/sql/install.mysql.utf8.sql');
		$imports=preg_replace("%/\*(.*)\*/%Us",'',$imports);
		$imports=preg_replace("%^--(.*)\n%mU",'',$imports);
		$imports=preg_replace("%^$\n%mU",'',$imports);
	
		$imports=explode(';',$imports);
		$cntPanel=0;
		echo JHtml::_('sliders.start','tables',array(
						'allowAllClose' => true,
						'startTransition' => true,
						true));
		foreach ($imports as $import)
		{
			$import=trim($import);
			if (!empty($import))
			{
				$DummyStr=$import;
				$DummyStr=substr($DummyStr,strpos($DummyStr,'`')+1);
				$DummyStr=substr($DummyStr,0,strpos($DummyStr,'`'));
				$db->setQuery($import);
				$panelName = substr(str_replace('joomleague','',str_replace('_','',$DummyStr)),1);
				echo JHtml::_('sliders.panel',$DummyStr,'panel-'.$panelName);
					
				echo '<table class="adminlist" style="width:100%; " border="0"><thead><tr><td colspan="2" class="key" style="text-align:center;"><h3>';
				echo "Checking existence of table [$DummyStr] - <span style='color:";
				if ($db->query()){echo "green'>".JText::_('Success');}else{echo "red'>".JText::_('Failed');}
				echo '</span>';
				echo '</h3></td></tr></thead><tbody>';
				$DummyStr=$import;
				$DummyStr=substr($DummyStr,strpos($DummyStr,'`')+1);
				$tableName=substr($DummyStr,0,strpos($DummyStr,'`'));
	
				$DummyStr=substr($DummyStr,strpos($DummyStr,'(')+1);
				$DummyStr=substr($DummyStr,0,strpos($DummyStr,'ENGINE'));
				$keysIndexes=trim(trim(substr($DummyStr,strpos($DummyStr,'PRIMARY KEY'))),')');
				$indexes=explode("\r\n",$keysIndexes);
				if ($indexes[0]==$keysIndexes)
				{
					$indexes=explode("\n",$keysIndexes);
					if ($indexes[0]==$keysIndexes)
					{
						$indexes=explode("\r",$keysIndexes);
					}
				}
	
				$DummyStr=trim(trim(substr($DummyStr,0,strpos($DummyStr,'PRIMARY KEY'))),',');
				$fields=explode("\r\n",$DummyStr);
				if ($fields[0]==$DummyStr)
				{
					$fields=explode("\n",$DummyStr);
					if ($fields[0]==$DummyStr){$fields=explode("\r",$DummyStr);}
				}
	
				$newIndexes=array();
				$i=(-1);
				foreach ($indexes AS $index)
				{
					$dummy=trim($index,' ,');
					if (!empty($dummy))
					{
						$i++;
						$newIndexes[$i]=$dummy;
					}
				}
	
				$newFields=array();
				$i=(-1);
				foreach ($fields AS $field)
				{
					$dummy=trim($field,' ,');
					if (!empty($dummy))
					{
						$i++;
						$newFields[$i]=$dummy;
					}
				}
	
				$rows=count($newIndexes)+1;
				echo '<tr><th class="key" style="vertical-align:top; width:10; white-space:nowrap; " rowspan="'.$rows.'">';
				echo JText::sprintf('Table needs following<br />keys/indexes:',$tableName);
				echo '</th></tr>';
				$k=0;
				foreach ($newIndexes AS $index)
				{
					$index=trim($index);
					echo '<tr class="row'.$k.'"><td>';
					if (!empty($index)){echo $index;}
					echo '</td></tr>';
					$k=(1-$k);
				}
	
				$rows=count($newIndexes)+1;
				echo '<tr><th class="key" style="vertical-align:top; width:10; white-space:nowrap; " rowspan="'.$rows.'">';
				echo JText::_('Dropping keys/indexes:');
				echo '</th></tr>';
				$keys = $db->getTableKeys($tableName);
				foreach ($newIndexes AS $index)
				{
					$query='';
					$index=trim($index);
					echo '<tr class="row'.$k.'"><td>';
					if (substr($index,0,11)!='PRIMARY KEY')
					{
						$keyName='';
						$queryDelete='';
						if (substr($index,0,3)=='KEY')
						{
							$keyName=substr($index,0,strpos($index,'('));
							$queryDelete="ALTER TABLE `$tableName` DROP $keyName";
						}
						elseif (substr($index,0,5)=='INDEX')
						{
							$keyName=substr($index,0,strpos($index,'('));
							$queryDelete="ALTER TABLE `$tableName` DROP $keyName";
						}
						elseif (substr($index,0,6)=='UNIQUE')
						{
							$keyName=trim(substr($index,6));
							$keyName=substr($keyName,0,strpos($keyName,'('));
							$queryDelete="ALTER TABLE `$tableName` DROP $keyName";
						}
						$skip = false;
						foreach($keys as $key) {
							preg_match('/`(.*?)`/', $keyName, $reg);
							if(strcasecmp($key->Key_name, $reg[1])!==0) {
								echo "<span style='color:orange; '>".JText::sprintf('Skipping handling of %1$s',$queryDelete).'</span>';
								$skip = true;
								break;
							}
						}
						if($skip) continue;
						$db->setQuery($queryDelete);
						echo "$queryDelete - <span style='color:";
						if ($db->query()){echo "green'>".JText::_('Success');}else{echo "red'>".JText::_('Failed');}
						echo '</span>';
					}
					else
					{
						echo "<span style='color:orange; '>".JText::sprintf('Skipping handling of %1$s',$index).'</span>';
					}
					echo '&nbsp;</td></tr>';
					$k=(1-$k);
				}
	
				$rows=count($newFields)+1;
				echo '<tr><th class="key" style="vertical-align:top; width:10; white-space:nowrap; " rowspan="'.$rows.'">';
				echo JText::_('Updating fields:');
				echo '</th></tr>';
				$columns = $db->getTableColumns($tableName, false);
				foreach ($newFields AS $field)
				{
					$dFfieldName=substr($field,strpos($field,'`')+1);
					$fieldName=substr($dFfieldName,0,strpos($dFfieldName,'`'));
					$dFieldSetting=substr($dFfieldName,strpos($dFfieldName,'`')+1);
					echo '<tr class="row'.$k.'"><td>';
					$add = true;
					$query="ALTER TABLE `$tableName` ADD `$fieldName` $dFieldSetting";
					if(array_key_exists($fieldName, $columns) && 
						(strcasecmp($fieldName,$columns[$fieldName]->Field)===0) && 
						strpos(strtolower($dFieldSetting), $columns[$fieldName]->Type)) {
						echo "<span style='color:orange; '>".JText::sprintf('Skipping handling of %1$s',$query).'</span>';
						continue;
					} else {
						if(isset($columns[$fieldName])) {
							if(strpos(strtolower($dFieldSetting), $columns[$fieldName]->Type)) {
								$add = true;
							} else {
								$add = false;
							}	
						} 
					}
					if($add) {
						$db->setQuery($query);
						$db->query();
						echo "$query - <span style='color:";
						if ($db->query()){echo "green'>".JText::_('Success');}else{echo "red'>".JText::_('Failed');} //fehlgeschlagen
						echo '</span>';
					} else {
						if(array_key_exists($fieldName, $columns)) {
							$query="ALTER TABLE `$tableName` CHANGE `$fieldName` `$fieldName` $dFieldSetting";
						}
						$db->setQuery($query);
						echo "$query - <span style='color:";
						if ($db->query()){echo "green'>".JText::_('Success');}else{echo "red'>".JText::_('Failed');} //fehlgeschlagen
						echo '</span>';
					}
					echo '&nbsp;</td></tr>';
					$k=(1-$k);
				}
	
				$rows=count($newIndexes)+1;
				echo '<tr><th class="key" style="vertical-align:top; width:10; white-space:nowrap; " rowspan="'.$rows.'">';
				echo JText::_('Adding keys/indexes:');
				echo '</th></tr>';
				$keys = $db->getTableKeys($tableName);
				foreach ($newIndexes AS $index)
				{
					$query='';
					$index=trim($index);
					echo '<tr class="row'.$k.'"><td>';
					if (substr($index,0,11)!='PRIMARY KEY')
					{
						$keyName='';
						$queryAdd='';
						if (substr($index,0,3)=='KEY')
						{
							$keyName=substr($index,0,strpos($index,'('));
							$queryAdd="ALTER TABLE `$tableName` ADD $index";
						}
						elseif (substr($index,0,5)=='INDEX')
						{
							$keyName=substr($index,0,strpos($index,'('));
							$queryAdd="ALTER TABLE `$tableName` ADD $index";
						}
						elseif (substr($index,0,6)=='UNIQUE')
						{
							$keyName=trim(substr($index,6));
							$keyName=substr($keyName,0,strpos($keyName,'('));
							$queryAdd="ALTER TABLE `$tableName` ADD $index";
						}
						$skip = false;
						foreach($keys as $key) {
							preg_match('/`(.*?)`/', $keyName, $reg);
							if(strcasecmp($key->Key_name, $reg[1])===0) {
								echo "<span style='color:orange; '>".JText::sprintf('Skipping handling of %1$s',$queryDelete).'</span>';
								$skip = true;
								break;
							}
						}
						if($skip) continue;
						$db->setQuery($queryAdd);
						echo "$queryAdd - <span style='color:";
						if ($db->query()){echo "green'>".JText::_('Success');}else{echo "red'>".JText::_('Failed');}
						echo '</span>';
					}
					else
					{
						echo "<span style='color:orange; '>".JText::sprintf('Skipping handling of %1$s',$index).'</span>';
					}
					echo '&nbsp;</td></tr>';
					$k=(1-$k);
				}
				echo '</tbody></table>';
				unset($newIndexes);
				unset($newFields);
					
			}
			unset($import);
		}
		echo JHtml::_('sliders.end');
		return '';
	}
	
	public static function migratePicturePath() {
		$arrQueries = array();
	
		$query = "update #__joomleague_club set logo_big = replace(logo_big, 'media/com_joomleague/clubs/large', 'images/com_joomleague/database/clubs/large')";
		array_push($arrQueries, $query);
	
		$query = "update #__joomleague_club set logo_middle = replace(logo_middle, 'media/com_joomleague/clubs/medium', 'images/com_joomleague/database/clubs/medium')";
		array_push($arrQueries, $query);
	
		$query = "update #__joomleague_club set logo_small = replace(logo_small, 'media/com_joomleague/clubs/small', 'images/com_joomleague/database/clubs/small')";
		array_push($arrQueries, $query);
	
		$query = "update #__joomleague_eventtype set icon = replace(icon, 'media/com_joomleague/event_icons', 'images/com_joomleague/database/events')";
		array_push($arrQueries, $query);
	
		$query = "update #__joomleague_person set picture = replace(picture, 'media/com_joomleague/persons', 'images/com_joomleague/database/persons')";
		array_push($arrQueries, $query);
	
		$query = "update #__joomleague_team_player set picture = replace(picture, 'media/com_joomleague/persons', 'images/com_joomleague/database/persons')";
		array_push($arrQueries, $query);
	
		$query = "update #__joomleague_project set picture = replace(picture, 'media/com_joomleague/projects', 'images/com_joomleague/database/projects')";
		array_push($arrQueries, $query);
	
		$query = "update #__joomleague_playground set picture = replace(picture, 'media/com_joomleague/playgrounds', 'images/com_joomleague/database/playgrounds')";
		array_push($arrQueries, $query);
	
		$query = "update #__joomleague_sports_type set icon = replace(icon, 'media/com_joomleague/sportstypes', 'images/com_joomleague/database/sport_types')";
		array_push($arrQueries, $query);
	
		$query = "update #__joomleague_team set picture = replace(picture, 'media/com_joomleague/teams', 'images/com_joomleague/database/teams')";
		array_push($arrQueries, $query);
	
		$query = "update #__joomleague_project_team set picture = replace(picture, 'media/com_joomleague/teams', 'images/com_joomleague/database/teams')";
		array_push($arrQueries, $query);
	
		$query = "update #__joomleague_statistic set icon = replace(icon, 'media/com_joomleague/statistics', 'images/com_joomleague/database/statistics')";
		array_push($arrQueries, $query);
	
		$db = JFactory::getDbo();
		$query="SHOW TABLES LIKE '%_joomleague%'";
			
		$db->setQuery($query);
		$results = $db->loadColumn();
		if(is_array($results)) {
			echo JText::_('Database Tables Picture Path Migration');
			foreach ($arrQueries as $key=>$value) {
				$db->setQuery($value);
				if (!$db->query())
				{
					echo '-> '.JText::_('Failed').'! <br>';
					echo $db->getErrorMsg();
					//return false;
				} else {
					//echo "-> done !<br>";
				}
	
			}
			echo ' - <span style="color:green">'.JText::_('Success').'</span><br />';
		} else {
			echo JText::_('No Picture Path Migration neccessary!');
		}
	}
	
	public static function updateEventtypeSuspensions() {
		$arrQueries = array();
	
		$query = "update #__joomleague_eventtype set `suspension` = '1' where ((suspension = 0) AND (name = 'COM_JOOMLEAGUE_E_YELLOW-RED_CARD'))";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_eventtype set `suspension` = '1' where ((suspension = 0) AND (name = 'COM_JOOMLEAGUE_E_RED_CARD'))";
		array_push($arrQueries, $query);
		
		$query = "update #__joomleague_eventtype set `suspension` = '1' where ((suspension = 0) AND (name = 'COM_JOOMLEAGUE_E_BLUE_RED_CARD'))";
		array_push($arrQueries, $query);
		
		$db = JFactory::getDbo();
		$query="SHOW TABLES LIKE '%_joomleague%'";
			
		$db->setQuery($query);
		$results = $db->loadColumn();
		if(is_array($results)) {
			echo JText::_('Database Tables update suspensions of event types for soccer (e.g. red card)');
			foreach ($arrQueries as $key=>$value) {
				$db->setQuery($value);
				if (!$db->query())
				{
					echo '-> '.JText::_('Failed').'! <br>';
					echo $db->getErrorMsg();
					//return false;
				} else {
					//echo "-> done !<br>";
				}
	
			}
			echo ' - <span style="color:green">'.JText::_('Success').'</span><br />';
		} else {
			echo JText::_('No update of event types for soccer neccessary!');
		}
		
	}
	
	public static function dropJoomLeagueTables()
	{
		$query="SHOW TABLES LIKE '%_joomleague%'";
		$db = JFactory::getDbo();
			
		$db->setQuery($query);
		$results = $db->loadColumn();
		foreach ($results as $result)
		{
			$query='DROP TABLE IF EXISTS `'.$result.'`';
			$db->setQuery($query);
			if (!$db->query())
			{
				return false;
			}
		}
		return true;
	}
	
}
?>