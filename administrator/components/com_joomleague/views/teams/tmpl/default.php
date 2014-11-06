<?php defined('_JEXEC') or die('Restricted access');

//Ordering allowed ?
$ordering=($this->lists['order'] == 't.ordering');

JHtml::_('behavior.tooltip');
?>
<script>

	function searchTeam(val,key)
	{
		var f = $('adminForm');
		if(f)
		{
		f.elements['search'].value=val;
		f.elements['search_mode'].value= 'matchfirst';
		f.submit();
		}
	}

</script>
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm">
	<div style="width: 100%;">
		<div style="float: left;">
				<?php
				echo JText::_('COM_JOOMLEAGUE_GLOBAL_FILTER');
				?>&nbsp;<input	type="text" name="search" id="search"
								value="<?php echo $this->lists['search']; ?>"
								class="text_area" onchange="$('adminForm').submit(); " />
				<button onclick="this.form.submit(); "><?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_GO'); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit(); ">
					<?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_RESET'); ?>
				</button>
		</div>
		<div style="max-width: 700px; overflow: auto; float: right">
				<?php
				$startRange = hexdec($this->component_params->get('character_filter_start_hex', '0041'));
				$endRange = hexdec($this->component_params->get('character_filter_end_hex', '005A'));
				for ($i=$startRange; $i <= $endRange; $i++)
				{
					printf("<a href=\"javascript:searchTeam('%s')\">%s</a>&nbsp;&nbsp;&nbsp;&nbsp;",chr($i),chr($i));
				}
				?>
		</div>
	</div>
	<div style="clear: both;"></div>
	<div id="editcell">
		<table class="adminlist">
			<thead>
				<tr>
					<th width="5"><?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_NUM'); ?></th>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<th width="20">&nbsp;</th>
					<th class="left">
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_TEAMS_NAME','t.name',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th class="left">
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_TEAMS_CLUBNAME','c.name',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th>
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_TEAMS_WEBSITE','t.website',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th>
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_TEAMS_ML_NAME','t.middle_name',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th>
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_TEAMS_S_NAME','t.short_name',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th>
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_TEAMS_INFO','t.info',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th>
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_ADMIN_TEAMS_PICTURE','t.picture',$this->lists['order_Dir'],$this->lists['order']);
						?>
					</th>
					<th width="10%">
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_GLOBAL_ORDER','t.ordering',$this->lists['order_Dir'],$this->lists['order']);
						echo JHtml::_('grid.order',$this->items, 'filesave.png', 'team.saveorder');
						?>
					</th>
					<th width="5%">
						<?php
						echo JHtml::_('grid.sort','COM_JOOMLEAGUE_GLOBAL_ID','t.id',$this->lists['order_Dir'],$this->lists['order']);
						?>
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
					$link=JRoute::_('index.php?option=com_joomleague&task=team.edit&cid[]='.$row->id);
					$checked=JHtml::_('grid.checkedout',$row,$i);
					?>
					<tr class="<?php echo "row$k"; ?>">
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
							?>
							<td class="center">
								<a href="<?php echo $link; ?>">
									<?php
									$imageTitle=JText::_('COM_JOOMLEAGUE_ADMIN_TEAMS_EDIT_DETAILS');
									echo JHtml::_(	'image','administrator/components/com_joomleague/assets/images/edit.png',
													$imageTitle,'title= "'.$imageTitle.'"');
									?>
								</a>
							</td>
							<?php
						}
						?>
						<td><?php echo $row->name; ?></td>
						<td><?php echo (empty($row->clubname)) ? '<span style="color:red;">'.JText::_('COM_JOOMLEAGUE_ADMIN_TEAMS_NO_CLUB').'</span>' : '<a href="'.JRoute::_("index.php?option=com_joomleague&task=club.edit&cid[]=".$row->club_id).'">'.$row->clubname.'</a>'; ?></td>
						<td>
							<?php
							if ($row->website != '')
							{
								echo '<a href="'.$row->website.'" target="_blank">';
							}
							echo $row->website;
							if ($row->website != '')
							{
								echo '</a>';
							}
							?>
						</td>
						<td><?php echo $row->middle_name; ?></td>
						<td class="center"><?php echo $row->short_name; ?></td>
						<td><?php echo $row->info; ?></td>
						<td class="center">
							<?php
							if ($row->picture == '')
							{
								$imageTitle=JText::_('COM_JOOMLEAGUE_ADMIN_TEAMS_NO_IMAGE');
								echo JHtml::_(	'image','administrator/components/com_joomleague/assets/images/error.png',
												$imageTitle,'title= "'.$imageTitle.'"');
							}
							elseif ($row->picture == JoomleagueHelper::getDefaultPlaceholder("team"))
							{
								$imageTitle=JText::_('COM_JOOMLEAGUE_ADMIN_TEAMS_DEFAULT_IMAGE');
								echo JHtml::_(	'image','administrator/components/com_joomleague/assets/images/information.png',
				  								$imageTitle,'title= "'.$imageTitle.'"');
							} else {
								if (JFile::exists(JPATH_SITE.DS.$row->picture)) {
									$imageTitle=JText::_('COM_JOOMLEAGUE_ADMIN_TEAMS_CUSTOM_IMAGE');
									echo JHtml::_(	'image','administrator/components/com_joomleague/assets/images/ok.png',
													$imageTitle,'title= "'.$imageTitle.'"');
								} else {
									$imageTitle=JText::_('COM_JOOMLEAGUE_ADMIN_TEAMS_NO_IMAGE');
									echo JHtml::_(	'image','administrator/components/com_joomleague/assets/images/delete.png',
													$imageTitle,'title= "'.$imageTitle.'"');
								}
							}
							?>
						</td>
						<td class="order">
							<span>
								<?php echo $this->pagination->orderUpIcon($i,$i > 0 ,'team.orderup','COM_JOOMLEAGUE_GLOBAL_ORDER_UP',true); ?>
							</span>
							<span>
								<?php echo $this->pagination->orderDownIcon($i,$n,$i < $n,'team.orderdown','COM_JOOMLEAGUE_GLOBAL_ORDER_DOWN',true); ?>
								<?php $disabled=true ?	'' : 'disabled="disabled"'; ?>
							</span>
							<input	type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled; ?>
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

	<input type="hidden" name="search_mode"			value="<?php echo $this->lists['search_mode']; ?>" />
	<input type="hidden" name="task"				value="" />
	<input type="hidden" name="boxchecked"			value="0" />
	<input type="hidden" name="filter_order"		value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir"	value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>