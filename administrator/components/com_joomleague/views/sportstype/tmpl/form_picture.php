<?php defined('_JEXEC') or die('Restricted access');
?>

	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_SPORTSTYPE_PIC' );?>
		</legend>
		<table class="admintable">
			<tbody>
				<?php foreach ($this->form->getFieldset('picture') as $field): ?>
				<tr>
					<td class="key"><?php echo $field->label; ?></td>
					<td><?php echo $field->input; ?></td>
				</tr>					
				<?php endforeach; ?>
			</tbody>
		</table>
	</fieldset>