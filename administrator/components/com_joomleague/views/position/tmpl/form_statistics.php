<?php defined('_JEXEC') or die('Restricted access');
?>
<fieldset class="adminform">
	<legend><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_STATISTICS_LEGEND'); ?></legend>
	<table class="admintable">
		<tr>
			<td style="width:auto;"><b><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_EXISTING_STATISTICS'); ?></b><br /><?php echo $this->lists['statistic']; ?></td>
			<td style="width:auto;">
				<input  type="button" class="inputbox"
						onclick="moveLeftToRightStats();"
						value="&gt;&gt;" />
				<br /><br />
				<input  type="button" class="inputbox"
						onclick="moveRightToLeftStats();"
						value="&lt;&lt;" />
			</td>
			<td style="width:auto;"><b><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_ASSIGNED_STATS_TO_POS'); ?></b><br /><?php echo $this->lists['position_statistic']; ?></td>
			<td align='center' style="width:auto;">
				<input  type="button" class="inputbox"
						onclick="$('statschanges_check').value=1;moveOptionUp('position_statistic');"
						value="<?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_UP'); ?>" />
				<br /><br />
				<input type="button" class="inputbox"
					   onclick="$('statschanges_check').value=1;moveOptionDown('position_statistic');"
					   value="<?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_DOWN'); ?>" />
			</td>
			<td style="width:auto;">
			<fieldset class="adminform">
					<?php echo JText::_('COM_JOOMLEAGUE_ADMIN_POSITION_STATS_HINT'); ?>
			</fieldset>
			</td>			
		</tr>
	</table>
</fieldset>