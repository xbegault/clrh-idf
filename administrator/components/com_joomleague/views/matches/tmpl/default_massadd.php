<?php defined('_JEXEC') or die('Restricted access');
?>
	<div id="editcell">
		<fieldset class="adminform">
			<legend><?php echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_TITLE','<i>'.$this->projectws->name.'</i>'); ?></legend>
			<form name='copyform' method='post' style='display:inline' id='copyform'>
				<input type='hidden' name='match_date' value='<?php echo $this->roundws->round_date_first.' '.$this->projectws->start_time; ?>' />
				<input type='hidden' name='round_id' value='<?php echo $this->roundws->id; ?>' />
				<input type='hidden' name='project_id' value='<?php echo $this->roundws->project_id; ?>' />
				<input type='hidden' name='act' value='rounds' />
				<input type='hidden' name='task' value='match.copyfrom' />
				<input type='hidden' name='addtype' value='0' id='addtype' />
				<input type='hidden' name='add_match_count' value='0' id='addmatchescount' />
				<?php 
				echo JHtml::_('form.token')."\n";
				$date = new JDate($this->roundws->round_date_first, new DateTimeZone($this->projectws->timezone));
				?>
				<table class="adminlist">
					<thead>
						<tr>
							<th class="nowrap"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_MULTI'); ?></th>
							<th class="nowrap"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_COPY'); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td valign='top' width='50%'>
								<table class="admintable">
									<tr>
										<td class="key"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_TYPE'); ?></td>
										<td><?php echo $this->lists['createTypes']; ?></td>
									</tr>
									<tr>
										<td colspan='2' >
											<div id='massadd_standard' style='display:block;'>
												<table>
													<tr>
														<td width="100" align="right" class="key"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_NR'); ?></td>
														<td>
															<input type='text' name='tempaddmatchescount' id='tempaddmatchescount' value='0' size='3' class='inputbox' />
														</td>
													</tr>
													<tr>
														<td width="100" align="right" class="key"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_START_HERE'); ?></td>
														<td><?php echo $this->lists['addToRound']; ?></td>
													</tr>
													<tr>
														<td width="100" align="right" class="key"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_AUTO_PUBL'); ?></td>
														<td><?php echo $this->lists['autoPublish']; ?></td>
													</tr>
													<tr>
														<td width="100" align="right" class="key"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_FIRST_MATCHNR'); ?></td>
														<td><input type='text' name='firstMatchNumber' size='4' value='' /></td>
													</tr>
													<tr>
														<td width="100" align="right" class="key"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_STARTTIME'); ?></td>
														<td>
															<?php
															echo JHtml::calendar(	$date->format(JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_DATE_FORMAT'), true),
																					'match_date','match_date',
																					JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_DATE_FORMAT_CAL'), 'size="10" ');
                                                           ?>
                                                            &nbsp;
															<input type='text' name='startTime' value='<?php echo $this->projectws->start_time; ?>' size='4' maxlength='5' class='inputbox' />
														</td>
													</tr>
													<tr>
														<td width="100" colspan='2'>
															<input type='submit' value='<?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_NEW_MATCHES'); ?>' onclick='return addmatches();' />
														</td>
													</tr>
												</table>
											</div>
											<div id='massadd_type2' style='display:none;'>
											</div>
										</td>
									</tr>
								</table>
							</td>
							<td valign='top'>
								<table class="admintable">
									<tr>
										<td width="100" align="right" class="key"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_COPY2'); ?></td>
										<td><?php echo $this->lists['project_rounds2']; ?></td>
									</tr>
									<tr>
										<td width="100" align="right" class="key"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_DEFAULT_DATE'); ?></td>
										<td>
											<?php
											echo JHtml::calendar(	$date->format(JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_DATE_FORMAT'), true),
																	'date','date',
																	JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_DATE_FORMAT_CAL'),'size="10" ');
											?>
											&nbsp;
											<input type='text' name='time' value='<?php echo $this->projectws->start_time; ?>' size='4' maxlength='5' class='inputbox' />
										</td>
									</tr>
									<tr>
										<td width="100" align="right" class="key"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_FIRST_MATCHNR'); ?></td>
										<td><input type="text" name="start_match_number" size="4" value="" /></td>
									</tr>
									<tr>
										<td width="100" align="right" class="key">										
												<?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_CREATE_NEW'); ?>
										</td>
										<td><input type="checkbox" name="create_new" value="1" class="inputbox" checked="checked" /></td>
									</tr>
									<tr>
										<td width="100" align="right" class="key">
												<?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_COPY_MIRROR'); ?>
										</td>
										<td>
											<select name="mirror" class="inputbox">
												<option value="0" selected="selected"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_COPY_MATCHES'); ?></option>
												<option value="1"><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_MIRROR_HA'); ?></option>
											</select>
										</td>
									</tr>
									<tr>
										<td width="100" colspan='2'>																	
											<input type='submit' value='<?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MASSADD_COPY_MATCHES'); ?>' onclick='copymatches();' />
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</fieldset>
	</div>