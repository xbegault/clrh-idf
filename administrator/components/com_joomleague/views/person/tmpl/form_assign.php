<?php defined('_JEXEC') or die('Restricted access');
?>


<fieldset class="adminform">
	<legend>
		<?php
		echo JText::_('COM_JOOMLEAGUE_ADMIN_PERSON_ASSIGN_DESCR');
		?>
	</legend>
	<table class="admintable">
		<tr>
			<td colspan="2">
				<div class="button2-left" style="display: inline">
					<div class="readmore">
						<?php
						//create the button code to use in form while selecting a project and team to assign a new person to
						$button = '<a class="modal-button" title="Select" ';
						$button .= 'href="index.php?option=com_joomleague&view=person&task=person.personassign" ';
						$button .= 'rel="{handler: \'iframe\', size: {x: 600, y: 400}}">' . JText::_('Select') . '</a>';
						echo $button;
						?>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="key"><label for="project_id"> <?php
			echo JText::_('COM_JOOMLEAGUE_ADMIN_PERSON_ASSIGN_PID');
			?>
			</label>
			</td>
			<td><input onblur="$('project_name').value=''" type="text" name="project_id" id="project_id" value="" size="5" maxlength="6" /> 
				<input type="text" readonly name="project_name" id="project_name" value="" size="50"  />
			</td>
		<tr>
			<td class="key"><label for="team"> <?php
			echo JText::_('COM_JOOMLEAGUE_ADMIN_PERSON_ASSIGN_TID');
			?>
			</label>
			</td>
			<td><input onblur="$('team_name').value=''" type="text" name="team_id" id="team_id" value="" size="5" maxlength="6" /> 
				<input type="text" readonly name="team_name" id="team_name" value="" size="50"  />
			</td>
		</tr>
	</table>
</fieldset>
