<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );
?>			
		<fieldset class="adminform">
			<legend>
				<?php
				echo JText::_( 'COM_JOOMLEAGUE_ADMIN_MATCH_F_MREL_DETAILS' );
				?>
			</legend>
			<br/>
			<table class='admintable'>
				<tr>
					<td align="right" class="key">
						<label>
							<?php
							echo JText::_( 'COM_JOOMLEAGUE_ADMIN_MATCH_F_MREL_OLD_ID' );
							?>
						</label>
					</td>
					<td align="left">
						<?php echo $this->lists['old_match']; ?>  
						<?php if($this->match->old_match_id >0) : ?>
						  <a href="index.php?option=com_joomleague&tmpl=component&task=match.edit&cid[]=<?php echo $this->match->old_match_id?>">Match Link</a>
						<?php endif ?>
					</td>
				</tr>
				<tr>
					<td align="right" class="key">
						<label>
							<?php
							echo JText::_( 'COM_JOOMLEAGUE_ADMIN_MATCH_F_MREL_NEW_ID' );
							?>
						</label>
					</td>
					<td align="left">
						<?php echo $this->lists['new_match']; ?> 
						<?php if($this->match->new_match_id >0) : ?>
						  <a href="index.php?option=com_joomleague&tmpl=component&&task=match.edit&cid[]=<?php echo $this->match->new_match_id?>">Match Link</a>
						<?php endif ?>
					</td>
				</tr>
				
			</table>
		</fieldset>