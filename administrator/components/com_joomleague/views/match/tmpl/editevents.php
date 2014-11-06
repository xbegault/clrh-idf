<?php
/**
 * @copyright	Copyright (C) 2005-2014 joomleague.at. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
#echo '#<pre>'; print_r($this->rosters); echo '</pre>#';

/**
 * Match Form
 *
 * @author	Marco Vaninetti <martizva@tiscali.it>
 * @package	JoomLeague
 * @since	0.1
 */
?>
<script type="text/javascript">
<!--
var matchid = <?php echo $this->teams->id; ?>;
var baseajaxurl='<?php echo JUri::root();?>administrator/index.php?option=com_joomleague&<?php echo JUtility::getToken() ?>=1';
var homeroster = new Array;
<?php
$i = 0;
foreach ($this->rosters['home'] as $player)
{
	$obj = new stdclass();
	$obj->value = $player->value;
	switch ($this->default_name_dropdown_list_order)
	{
		case 'lastname':
			$obj->text  = JoomleagueHelper::formatName(null, $player->firstname, $player->nickname, $player->lastname, $this->default_name_format);
			break;

		case 'firstname':
			$obj->text  = JoomleagueHelper::formatName(null, $player->firstname, $player->nickname, $player->lastname, $this->default_name_format);
			break;

		case 'position':
			$obj->text  = '('.JText::_($player->positionname).') - '.JoomleagueHelper::formatName(null, $player->firstname, $player->nickname, $player->lastname, $this->default_name_format);
			break;
	}
	echo 'homeroster['.($i++).']='.json_encode($obj).";\n";
}
?>
var awayroster = new Array;
<?php
$i = 0;
foreach ($this->rosters['away'] as $player)
{
	$obj = new stdclass();
	$obj->value = $player->value;
	switch ($this->default_name_dropdown_list_order)
	{
		case 'lastname':
			$obj->text  = JoomleagueHelper::formatName(null, $player->firstname, $player->nickname, $player->lastname, $this->default_name_format);
			break;

		case 'firstname':
			$obj->text  = JoomleagueHelper::formatName(null, $player->firstname, $player->nickname, $player->lastname, $this->default_name_format);
			break;

		case 'position':
			$obj->text  = '('.JText::_($player->positionname).') - '.JoomleagueHelper::formatName(null, $player->firstname, $player->nickname, $player->lastname, $this->default_name_format);
			break;
	}
	echo 'awayroster['.($i++).']='.json_encode($obj).";\n";
}
?>
var rosters = Array(homeroster, awayroster);
var str_delete = "<?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_DELETE'); ?>";

//-->
</script>

<?php
if(isset($this->preFillSuccess) && $this->preFillSuccess) {
	JFactory::getApplication()->enqueueMessage(JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_PREFILL_DONE'), 'message');
}
?>

<div id="gamesevents">
	<fieldset>
		<div class="fltrt">
			<button id="cancel" type="button" onclick="<?php echo JRequest::getBool('refresh', 0) ? 'window.parent.location.href=window.parent.location.href;' : '';?>  window.parent.SqueezeBox.close();">
				<?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_CLOSE');?></button>
		</div>
		<div class="configuration" >
			<?php echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_MATCH_EE_TITLE', $this->teams->team1, $this->teams->team2); ?>
		</div>
	</fieldset>
	
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_DESCR'); ?></legend>
			<!-- Don't remove this "<div id"ajaxresponse"></div> as it is neede for ajax changings -->
			<div id="ajaxresponse"></div>
			<table class='adminlist'>
				<thead>
					<tr>
						<th><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_TEAM'); ?></th>
						<th><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_PLAYER'); ?></th>
						<th><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_EVENT'); ?></th>
						<th><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_VALUE_SUM'); ?></th>
						<th>
							<?php
							echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_TIME');
							#echo JText::_('Hrs') . ' ' . JText::_('Mins') . ' ' . JText::_('Secs');
							?>
						</th>
						<th><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_MATCH_NOTICE'); ?></th>
						<th><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_EVENT_ACTION'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$k=0;
					if (isset($this->matchevents))
					{
						foreach ($this->matchevents as $event)
						{
							if ($event->event_type_id != 0) {
							?>
							<tr id="row-<?php echo $event->id; ?>" class="<?php echo "row$k"; ?>">
								<td><?php echo $event->team; ?></td>
								<td>
								<?php
								// TODO: now remove the empty nickname quotes, but that should probably be solved differently
								echo preg_replace('/\'\' /', "", $event->player1);
								?>
								</td>
								<td style='text-align:center; ' ><?php echo JText::_($event->event); ?></td>
								<td style='text-align:center; ' ><?php echo $event->event_sum; ?></td>
								<td style='text-align:center; ' ><?php echo $event->event_time; ?></td>
								<td title="" class="hasTip">
									<?php echo (strlen($event->notice) > 20) ? substr($event->notice, 0, 17).'...' : $event->notice; ?>
								</td>
								<td style='text-align:center; ' >
									<input	id="delete-<?php echo $event->id; ?>" type="button" class="inputbox button-delete"
											value="<?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_DELETE'); ?>" />
								</td>
							</tr>
							<?php
							}
						$k=1 - $k;
						}
					}
					?>
					<tr id="row-new">
						<td><?php echo $this->lists['teams']; ?></td>
						<td id="cell-player">&nbsp;</td>
						<td><?php echo $this->lists['events']; ?></td>
						<td style='text-align:center; ' ><input type="text" size="3" value="" id="event_sum" name="event_sum" class="inputbox" /></td>
						<td style='text-align:center; ' ><input type="text" size="3" value="" id="event_time" name="event_time" class="inputbox" /></td>
						<td style='text-align:center; ' ><input type="text" size="20" value="" id="notice" name="notice" class="inputbox" /></td>
						<td style='text-align:center; ' >
							<?php echo JHtml::_('form.token'); ?>
							<input id="save-new" type="button" class="inputbox button-save" value="<?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_SAVE'); ?>" />
						</td>
					</tr>
				</tbody>
			</table>
			
			<br>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_LIVE_COMMENTARY_DESCR'); ?></legend>		
		<table class='adminlist' >
			<thead>
				<tr>
					<th><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_LIVE_TYPE' ); ?></th>
					<th>
						<?php
						echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_TIME' );
						#echo JText::_( 'Hrs' ) . ' ' . JText::_( 'Mins' ) . ' ' . JText::_( 'Secs' );
						?>
					</th>
					<th><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_LIVE_NOTES' ); ?></th>
					<th><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_EVENT_ACTION' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$k=0;
				if ( isset( $this->matchevents ) )
				{
					foreach ( $this->matchevents as $event )
					{
						if ($event->event_type_id == 0) {
						?>
						<tr class="<?php echo "row$k"; ?>">
							<td>
								<?php 
								switch ($event->event_sum) {
                                    case 2:
                                        echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_LIVE_TYPE_2' );
                                        break;
                                    case 1:
                                        echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_LIVE_TYPE_1' );
                                        break;
		                        } ?>
							</td>

							<td style='text-align:center; ' >
								<?php
								echo $event->event_time;
								?>
							</td>
							<td title='' class='hasTip' style="width: 500px;">
								<?php
								echo $event->notes;
								?>
							</td>
							<td style='text-align:center; ' >
								<input	id="delete-<?php echo $event->id; ?>" type="button" class="inputbox button-delete"
										value="<?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_DELETE' ); ?>" />
							</td>
						</tr>
						<?php
						}
					$k=1 - $k;
					}
				}
				?>
				<tr id="row-new-comment">

					<td>
						<select name="ctype" id="ctype" class="inputbox select-commenttype">
                            <option value="1"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_LIVE_TYPE_1' ); ?></option>
                            <option value="2"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_LIVE_TYPE_2' ); ?></option>
                        </select> 
					</td>
					<td style='text-align:center; ' >
						<input type="text" size="3" value="" id="c_event_time" name="c_event_time" class="inputbox" />
					</td>
					<td style='text-align:center; ' >
						<textarea rows="2" cols="70" id="notes" name="notes" ></textarea>
					</td>
					<td style='text-align:center; ' >
						<input id="save-new-comment" type="button" class="inputbox button-save-c" value="<?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_SAVE' ); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
			
		</fieldset>
</div>
<div style="clear: both"></div>