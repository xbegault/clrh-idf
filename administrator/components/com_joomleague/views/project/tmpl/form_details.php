<?php defined('_JEXEC')or die('Restricted access');
?>
		<fieldset class="adminform">
			<legend><?php echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECT_LEGEND_DETAILS','<i>'.$this->form->getValue('name').'</i>'); ?></legend>
			<table class="admintable">
				<tr>
					<td class="key"><?php echo $this->form->getLabel('name'); ?></td>
					<td><?php echo $this->form->getInput('name'); ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo $this->form->getLabel('alias'); ?></td>
					<td><?php echo $this->form->getInput('alias'); ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo $this->form->getLabel('published'); ?></td>
					<td><?php echo $this->form->getInput('published'); ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo $this->form->getLabel('sports_type_id'); ?></td>
					<td><?php echo $this->form->getInput('sports_type_id'); ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo $this->form->getLabel('league_id'); ?></td>
					<td><?php echo $this->form->getInput('league_id'); ?>
						<?php
						if (!$this->edit)
						{
							echo '<input type="checkbox" name="newLeagueCheck" value="1"';
							echo ' onclick="if(this.checked){$(\'adminForm\').league_id.disabled=true;';
							echo '$(\'adminForm\').leagueNew.disabled=false;';
							echo '$(\'adminForm\').leagueNew.value='.''.'$(\'adminForm\').name.value} ';
							echo 'else {$(\'adminForm\').league_id.disabled=false;$(\'adminForm\').leagueNew.disabled=true}" />';
							echo JText::_('COM_JOOMLEAGUE_ADMIN_PROJECT_LEAGUE_NEW').'&nbsp;';
							echo '<input type="text" name="leagueNew" id="leagueNew" size="16" disabled / >';
						}
						?>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo $this->form->getLabel('season_id'); ?></td>
					<td><?php echo $this->form->getInput('season_id'); ?>
						<?php
						if (!$this->edit)
						{
							 echo '<input type="checkbox" name="newSeasonCheck" value="1"';
							 echo ' onclick="if(this.checked){$(\'adminForm\').season_id.disabled=true;';
							 echo '$(\'adminForm\').seasonNew.disabled=false} ';
							 echo ' else {$(\'adminForm\').season_id.disabled=false;';
							 echo '$(\'adminForm\').seasonNew.disabled=true}" />';
							 echo JText::_('COM_JOOMLEAGUE_ADMIN_PROJECT_SEASON_NEW'). "&nbsp;";
							 echo '<input type="text" name="seasonNew" id="seasonNew" disabled />';
						}
						?>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo $this->form->getLabel('project_type'); ?></td>
					<td><?php echo $this->form->getInput('project_type'); ?></td>
				</tr>
				<tr>
					<td class="key"><?php echo $this->form->getLabel('master_template'); ?></td>
					<td><?php echo $this->form->getInput('master_template'); ?></td>
				</tr>
	 			<tr>
					<td class="key"><?php echo $this->form->getLabel('extension'); ?></td>
					<td><?php echo $this->form->getInput('extension'); ?></td>
				</tr>
			</table>
		</fieldset>