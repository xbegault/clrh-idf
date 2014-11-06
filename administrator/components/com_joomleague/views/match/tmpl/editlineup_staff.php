<?php defined('_JEXEC') or die('Restricted access');
?>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_ELUS'); ?></legend>
			<table class='adminlist'>
			<thead>
				<tr>
					<th>
						<?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_ELUS_STAFF'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_ELUS_ASSIGNED'); ?>
					</th>					
				</tr>
			</thead>				
				<tr>
					<td colspan="2">
				</tr>
				<tr>
					<td style="text-align:center; vertical-align:top; ">
						<?php
						// echo select list of non assigned players from team roster
						echo $this->lists['team_staffs'];
						?>
					</td>
					<td style="text-align:center; vertical-align:top; ">
						<table border='0'>
							<?php
							foreach ($this->staffpositions AS $position_id => $pos)
							{
								?>
								<tr>
									<td style='text-align:center; vertical-align:middle; '>
										<!-- left / right buttons -->
										<br />
										<input	type="button" id="smoveright-<?php echo $position_id;?>" class="inputbox smove-right"
												value="&gt;&gt;" /><br />
										&nbsp;&nbsp;
										<input	type="button" id="smoveleft-<?php echo $position_id;?>" class="inputbox smove-left"
												value="&lt;&lt;" />
										&nbsp;&nbsp;
									</td>
									<td>
										<!-- player affected to this position -->
										<b><?php echo JText::_($pos->text); ?></b><br />
										<?php echo $this->lists['team_staffs'.$position_id]; ?>
									</td>
									<td style='text-align:center; vertical-align:middle; '>
										<!-- up/down buttons -->
										<br />
										<input	type="button" id="smoveup-<?php echo $position_id;?>" class="inputbox smove-up"
												value="<?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_UP'); ?>" /><br />
										<input	type="button" id="smovedown-<?php echo $position_id;?>" class="inputbox smove-down"
												value="<?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_DOWN'); ?>" />
									</td>
								</tr>
								<?php
							}
							?>
						</table>
					</td>
				</tr>
			</table>
		</fieldset>