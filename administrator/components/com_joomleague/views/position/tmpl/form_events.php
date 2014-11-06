<?php defined('_JEXEC') or die('Restricted access');
?>
<fieldset class="adminform">	
	<legend><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_EVENTS_LEGEND'); ?></legend>
	<table class="admintable">
		<tr>
			<td style="width:auto;"><b><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_EXISTING_EVENTS'); ?></b><br /><?php echo $this->lists['events']; ?></td>
			<td style="width:auto;">
				<input  type="button" class="inputbox"
						onclick="moveLeftToRightEvents();"
						value="&gt;&gt;" />
				<br /><br />
				<input  type="button" class="inputbox"
						onclick="moveRightToLeftEvents();"
						value="&lt;&lt;" />
			</td>
			<td style="width:auto;"><b><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_ASSIGNED_EVENTS_TO_POS'); ?></b><br /><?php echo $this->lists['position_events']; ?></td>
			<td align='center' style="width:auto;">
				<input  type="button" class="inputbox"
						onclick="$('eventschanges_check').value=1;moveOptionUp('position_eventslist');"
						value="<?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_UP'); ?>" />
				<br /><br />
				<input type="button" class="inputbox"
					   onclick="$('eventschanges_check').value=1;moveOptionDown('position_eventslist');"
					   value="<?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_DOWN'); ?>" />
			</td>
			<td style="width:auto;">
			<fieldset class="adminform">
					<?php echo JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_EVENTS_HINT'); ?>
			</fieldset>
			</td>
		</tr>
	</table>
</fieldset>
