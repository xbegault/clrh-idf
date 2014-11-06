<?php defined('_JEXEC') or die('Restricted access');

//Ordering allowed ?
$ordering=($this->lists['order'] == 'obj.ordering');

JHtml::_('behavior.tooltip');
?>
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<?php
				echo JText::_('COM_JOOMLEAGUE_GLOBAL_FILTER');
				?>&nbsp;<input	type="text" name="search" id="search"
								value="<?php echo $this->lists['search']; ?>"
								class="text_area" onchange="$('adminForm').submit(); " />
				<button onclick="this.form.submit(); "><?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_GO'); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit(); "><?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_RESET'); ?></button>
			</td>
			<td nowrap='nowrap' align='right'><?php echo $this->lists['sportstypes'].'&nbsp;&nbsp;'; ?></td>
			<td class="nowrap"><?php echo $this->lists['state']; ?></td>
		</tr>
	</table>
	<div id="editcell">
		<table class="adminlist">
			<thead>
				<tr>
					<th width="5"><?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_NUM'); ?></th>
					<th width="20">
						<input  type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<th width="20">&nbsp;</th>
					<th>
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_STATISTICS_NAME','obj.name',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th width="20">
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_STATISTICS_ABBREV','obj.short',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th width="10%">
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_STATISTICS_ICON','obj.icon',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th width="10%">
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_STATISTICS_SPORTSTYPE','obj.sports_type_id',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_STATISTICS_NOTE'); ?></th>
					<th><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_STATISTICS_TYPE'); ?></th>
					<th width="1%">
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_GLOBAL_PUBLISHED','obj.published',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort','COM_JOOMLEAGUE_GLOBAL_ORDER','obj.ordering',$this->lists['order_Dir'],$this->lists['order']); ?>
						<?php echo JHtml::_('grid.order',$this->items, 'filesave.png', 'statistic.saveorder'); ?>
					</th>

					<th width="5%">
						<?php echo JHtml::_('grid.sort','COM_JOOMLEAGUE_GLOBAL_ID','obj.id',$this->lists['order_Dir'],$this->lists['order']); ?>
					</th>
				</tr>
			</thead>
			<tfoot><tr><td colspan="12"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
			<tbody>
				<?php
				$k=0;
				for ($i=0,$n=count($this->items); $i < $n; $i++)
				{
					$row =& $this->items[$i];
					$link=JRoute::_('index.php?option=com_joomleague&task=statistic.edit&id='.$row->id);
					$checked=JHtml::_('grid.checkedout',$row,$i);
					$published=JHtml::_('grid.published',$row,$i,'tick.png','publish_x.png','statistic.');
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td class="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
						<td class="center"><?php echo $checked; ?></td>
						<?php
						if (JLTable::_isCheckedOut($this->user->get('id'),$row->checked_out))
						{
							?><td class="center"><?php echo $row->name; ?></td><?php
						}
						else
						{
							?><td class="center">
								<a href="<?php echo $link; ?>">
							<?php
								$imgTitle = JText::_('COM_JOOMLEAGUE_ADMIN_STATISTICS_EDIT_DETAILS');
								echo JHtml::image('administrator/components/com_joomleague/assets/images/edit.png',
											 $imgTitle, array('border' => 0,'title' => $imgTitle));
							?>
								</a>
							</td><?php
						}
						?>
						<td><?php echo $row->name; ?></td>
						<td><?php echo $row->short; ?></td>
						<td class="center">
							<?php
							$picture=JPATH_SITE.DS.$row->icon;
							$desc=JText::_($row->name);
							echo JoomleagueHelper::getPictureThumb($picture, $desc, 0, 21, 4);
							?>
						</td>
						<td class="center">
							<?php
							echo JoomleagueHelper::getSportsTypeName($row->sports_type_id);
							?>
						</td>
						<td><?php echo $row->note; ?></td>
						<td><?php echo JText::_($row->class); ?></td>
						<td class="center"><?php echo $published; ?>
						</td>
						<td class="order">
							<span><?php echo $this->pagination->orderUpIcon($i,$i > 0,'statistic.orderup','COM_JOOMLEAGUE_GLOBAL_ORDER_UP',true); ?></span>
							<span><?php echo $this->pagination->orderDownIcon($i,$n,$i < $n,'statistic.orderdown','COM_JOOMLEAGUE_GLOBAL_ORDER_DOWN',true); ?>
									<?php $disabled=true ?  '' : 'disabled="disabled"'; ?></span>
							<input  type="text" name="order[]" size="5"
									value="<?php echo $row->ordering; ?>" <?php echo $disabled; ?>
									class="text_area" style="text-align: center" />
						</td>
						<td class="center"><?php echo $row->id; ?></td>
					</tr>
					<?php
					$k=1 - $k;
				}
				?>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="task"				value="" />
	<input type="hidden" name="boxchecked"			value="0" />
	<input type="hidden" name="filter_order"		value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir"	value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>