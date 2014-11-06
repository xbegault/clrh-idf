<?php defined('_JEXEC') or die('Restricted access');

//Ordering allowed ?
$ordering=($this->lists['order'] == 'po.ordering');

JHtml::_('behavior.tooltip');
?>
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm">
	<table>
		<tr>
			<td align='left' width='100%'>
				<?php
				echo JText::_('COM_JOOMLEAGUE_GLOBAL_FILTER');
				?>&nbsp;<input	type="text" name="search" id="search"
								value="<?php echo $this->lists['search']; ?>"
								class="text_area" onchange="$('adminForm').submit(); " />
				<button onclick="this.form.submit(); "><?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_GO'); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit(); ">
					<?php
					echo JText::_('COM_JOOMLEAGUE_GLOBAL_RESET');
					?>
				</button>
			</td>
			<td nowrap='nowrap' align='right'><?php echo $this->lists['sportstypes'].'&nbsp;&nbsp;'; ?></td>
			<td nowrap='nowrap'><?php echo $this->lists['state']; ?></td>
		</tr>
	</table>
	<div id="editcell">
		<table class="adminlist">
			<thead>
				<tr>
					<th width="5"><?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_NUM'); ?></th>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<th width="20">&nbsp;</th>
					<th>
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_POSITIONS_STANDARD_NAME_OF_POSITION','po.name',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_POSITIONS_TRANSLATION'); ?></th>
					<th>
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_POSITIONS_PARENTNAME','po.parent_id',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th>
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_POSITIONS_SPORTSTYPE','po.sports_type_id',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th>
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_POSITIONS_PERSON_TYPE','po.persontype',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th width="5%"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_POSITIONS_HAS_EVENTS'); ?>
					</th>
					<th width="5%"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_POSITIONS_HAS_STATS'); ?>
					</th>
					<th width="5%">
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_GLOBAL_PUBLISHED','po.published',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th width="10%">
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_GLOBAL_ORDER','po.ordering',$this->lists['order_Dir'],$this->lists['order']);
						echo JHtml::_('grid.order', $this->items, 'filesave.png', 'position.saveorder');
						?>
					</th>
					<th width="5%">
						<?php echo JHtml::_('grid.sort','COM_JOOMLEAGUE_GLOBAL_ID','po.id',$this->lists['order_Dir'],$this->lists['order']); ?>
					</th>
				</tr>
			</thead>
			<tfoot><tr><td colspan="13"><?php echo $this->pagination->getListFooter(); ?></td></tr></tfoot>
			<tbody>
			<?php
			$k=0;
			for ($i=0,$n=count($this->items); $i < $n; $i++)
			{
				$row =& $this->items[$i];
				$link=JRoute::_('index.php?option=com_joomleague&task=position.edit&cid[]='.$row->id);
				$checked=JHtml::_('grid.checkedout',$row,$i);
				$published=JHtml::_('grid.published',$row,$i,'tick.png','publish_x.png','position.');
				?>
				<tr class="<?php echo 'row'.$k; ?>">
					<td class="center"><?php echo $this->pagination->getRowOffset($i); ?></td>
					<td class="center"><?php echo $checked; ?></td>
					<?php
					if (JLTable::_isCheckedOut($this->user->get('id'),$row->checked_out))
					{
						$inputappend=' disabled="disabled"';
						?><td class="center">&nbsp;</td><?php
					}
					else
					{
						$inputappend='';
						?><td class="center">
							<a href="<?php echo $link; ?>">
								<?php
								$imageTitle=JText::_('COM_JOOMLEAGUE_ADMIN_POSITIONS_EDIT_DETAILS');
								echo JHtml::_(	'image','administrator/components/com_joomleague/assets/images/edit.png',
												$imageTitle,'title= "'.$imageTitle.'"');
								?>
							</a>
						</td><?php
					}
					?>
					<td><?php echo $row->name; ?></td>
					<td>
						<?php
						if ($row->name == JText::_($row->name))
						{
							echo '&nbsp;';
						}
						else
						{
							echo JText::_($row->name);
						}
						?>
					</td>
					<td>
						<?php
							echo JHtml::_('select.genericlist',$this->lists['parent_id'],'parent_id'.$row->id,''.'class="inputbox" size="1" onchange="document.getElementById(\'cb'.$i.'\').checked=true"','value','text',$row->parent_id);
						?>
					</td>
					<td class="center"><?php echo JoomleagueHelper::getSportsTypeName($row->sports_type_id); ?></td>
					<td class="center"><?php echo JoomleagueHelper::getPosPersonTypeName($row->persontype); ?></td>
					<td class="center">
						<?php
						if ($row->countEvents == 0)
						{
							$imageTitle=JText::_('COM_JOOMLEAGUE_ADMIN_POSITIONS_NO_EVENTS');
							echo JHtml::_(	'image','administrator/components/com_joomleague/assets/images/error.png',
											$imageTitle,'title= "'.$imageTitle.'"');
						}
						else
						{
							$imageTitle=JText::sprintf('COM_JOOMLEAGUE_ADMIN_POSITIONS_NR_EVENTS',$row->countEvents);
							echo JHtml::_(	'image','administrator/components/com_joomleague/assets/images/ok.png',
											$imageTitle,'title= "'.$imageTitle.'"');
						}
						?>
					</td>
					<td class="center">
						<?php
						if ($row->countStats == 0)
						{
							$imageTitle=JText::_('COM_JOOMLEAGUE_ADMIN_POSITIONS_NO_STATISTICS');
							echo JHtml::_(	'image','administrator/components/com_joomleague/assets/images/error.png',
											$imageTitle,'title= "'.$imageTitle.'"');
						}
						else
						{
							$imageTitle=JText::sprintf('COM_JOOMLEAGUE_ADMIN_POSITIONS_NR_STATISTICS',$row->countStats);
							echo JHtml::_(	'image','administrator/components/com_joomleague/assets/images/ok.png',
											$imageTitle,'title= "'.$imageTitle.'"');
						}
						?>
					</td>
					<td class="center"><?php echo $published; ?></td>
					<td class="order">
						<span>
							<?php echo $this->pagination->orderUpIcon($i,$i > 0 ,'position.orderup','COM_JOOMLEAGUE_GLOBAL_ORDER_UP',true); ?>
						</span>
						<span>
							<?php echo $this->pagination->orderDownIcon($i,$n,$i < $n,'position.orderdown','COM_JOOMLEAGUE_GLOBAL_ORDER_DOWN',true); ?>
							<?php
							$disabled=true ? '' : 'disabled="disabled"';
							?>
						</span>
						<input  type="text" name="order[]" size="2"
								value="<?php echo $row->ordering; ?>" <?php echo $disabled ?>
								class="text_area" style="text-align: center" />
					</td>
					<td align="center"><?php echo $row->id; ?></td>
				</tr>
				<?php
				$k=1 - $k;
			}
			?>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="task" value="position.display" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHtml::_('form.token')."\n"; ?>
</form>