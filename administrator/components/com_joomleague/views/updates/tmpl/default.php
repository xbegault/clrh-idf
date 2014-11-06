<?php defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
?>
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th width="5" style="vertical-align: top; "><?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_NUM'); ?></th>
				<th class="title" class="nowrap"><?php echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_UPDATES_FILE','name',$this->lists['order_Dir'],$this->lists['order']); ?></th>
				<th class="title" class="nowrap"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_UPDATES_DESCR'); ?></th>
				<th class="title" class="nowrap"><?php echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_UPDATES_VERSION','version',$this->lists['order_Dir'],$this->lists['order']); ?></th>
				<th class="title" class="nowrap"><?php echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_UPDATES_DATE','date',$this->lists['order_Dir'],$this->lists['order']); ?></th>
				<th class="title" class="nowrap"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_UPDATES_EXECUTED'); ?></th>
				<th class="title" class="nowrap"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_UPDATES_COUNT');?></th>
			</tr>
		</thead>
		<tfoot><tr><td colspan='7'><?php echo '&nbsp;'; ?></td></tr></tfoot>
		<tbody><?php
		$k=0;
		for ($i=0, $n=count($this->updateFiles); $i < $n; $i++)
		{
			$row =& $this->updateFiles[$i];
			$link=JRoute::_('index.php?option=com_joomleague&view=updates&task=update.save&file_name='.$row['file_name']);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td class="center"><?php echo $i+1; ?></td>
				<td><?php
					$linkTitle=$row['file_name'];
					$linkParams="title='".JText::_('COM_JOOMLEAGUE_ADMIN_UPDATES_MAKE_UPDATE')."'";
					echo JHtml::link($link,$linkTitle,$linkParams);
					?></td>
				<td><?php
					if($row['updateDescription'] != "")
					{
						echo $row['updateDescription'];
					}
					else
					{
						echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_UPDATES_UPDATE',$row['last_version'],$row['version']);
					}
					?></td>
				<td class="center"><?php echo $row['version']; ?></td>
				<td class="center"><?php echo JText::_($row['updateFileDate']).' '.JText::_($row['updateFileTime']); ?></td>
				<td class="center"><?php echo $row['date']; ?></td>
				<td class="center"><?php echo $row['count']; ?></td>
			</tr>
			<?php
			$k=1 - $k;
		}
		?></tbody>
	</table>
	<input type="hidden" name="view" value="updates" />
	<input type="hidden" name="task" value="update.display" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHtml::_('form.token')."\n"; ?>
</form>