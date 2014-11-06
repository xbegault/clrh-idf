<?php
defined('_JEXEC') or die('Restricted access');

if (!isset($this->team) || !isset($this->club))
{
	JError::raiseWarning('ERROR_CODE', JText::_('COM_JOOMLEAGUE_TEAMINFO_ERROR'));
}
else
{
	if($this->config['show_club_info'] || $this->config['show_team_info'])
	{
		echo '<div class="left-column">';
	}
	else
	{
		echo'<div style="text-align:center; width:100%;">';
	}
	//dynamic object property string
	$pic = $this->config['show_picture'];
	echo JoomleagueHelper::getPictureThumb($this->team->$pic,
											$this->team->name,
											$this->config['team_picture_width'],
											$this->config['team_picture_height'],
											1);
	?>
</div>
	<?php
	if($this->config['show_club_info'] || $this->config['show_team_info'])
	{
		?>
<div class="right-column">
		<?php
		if ($this->config['show_club_info'])
		{
			if ((($this->club->address) || ($this->club->zipcode)))
			{
				?>
	<div class="jl_parentContainer">
		<span class="clubinfo_listing_item"><?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_CLUB_ADDRESS'); ?></span>
				<?php
				$dummy = Countries::convertAddressString(	$this->club->name,
															$this->club->address,
															$this->club->state,
															$this->club->zipcode,
															$this->club->location,
															$this->club->country,
															'COM_JOOMLEAGUE_TEAMINFO_CLUB_ADDRESS_FORM' );
				$dummy = explode('<br />', $dummy);

				for ($i = 0; $i < count($dummy); $i++)
				{
					if ($i > 0)
					{
						echo'<span class="clubinfo_listing_item">&nbsp;</span>';
					}

					echo'<span class="clubinfo_listing_value">'.$dummy[$i].'</span>';
				}
				echo '</div>';
			}

			if ($this->club->phone)
			{
				?>
	<div class="jl_parentContainer">
		<span class="clubinfo_listing_item"> <?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_CLUB_PHONE'); ?></span>
		<span class="clubinfo_listing_value"> <?php echo $this->club->phone; ?></span>
	</div>
				<?php
			}

			if ($this->club->fax)
			{
				?>
	<div class="jl_parentContainer">
		<span class="clubinfo_listing_item"> <?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_CLUB_FAX'); ?></span>
		<span class="clubinfo_listing_value"> <?php echo $this->club->fax; ?></span>
	</div>
				<?php
			}

			if ($this->club->email)
			{
				?>
	<div class="jl_parentContainer">
		<span class="clubinfo_listing_item"> <?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_CLUB_EMAIL'); ?></span>
		<span class="clubinfo_listing_value"> <?php
		$user = &JFactory::getUser();
		if (($user->id) or (!$this->overallconfig['nospam_email']))
		{
			echo JHtml::link('mailto:'. $this->club->email, $this->club->email);
		} else {
			echo JHtml::_('email.cloak', $this->club->email);
		}
		?>
		</span>
	</div>
				<?php
			}

			if ($this->club)
			{
				?>
	<div class="jl_parentContainer">
		<span class="clubinfo_listing_item"> <?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_CLUB_NAME'); ?></span>
		<span class="clubinfo_listing_value"> <?php
		$link = JoomleagueHelperRoute::getClubInfoRoute($this->project->slug, $this->club->slug);
		echo JHtml::link($link, $this->club->name);
		?>
		</span>
	</div>
				<?php
			}

			if ($this->club->website)
			{
				?>
	<div class="jl_parentContainer">
		<span class="clubinfo_listing_item"> <?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_CLUB_SITE'); ?></span>
		<span class="clubinfo_listing_value"> <?php echo JHtml::link($this->club->website, $this->club->website, array( "target" => "_blank")); ?></span>
	</div>
			<?php
			}
		}
		if ($this->config['show_team_info'])
		{
			?>
	<div class="jl_parentContainer">
		<span class="clubinfo_listing_item"> <?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_TEAM_NAME'); ?></span>
		<span class="clubinfo_listing_value"> <?php
		$link = JoomleagueHelperRoute::getTeamInfoRoute($this->project->slug, $this->team->slug);
		echo JHtml::link($link, $this->team->tname);
		?>
		</span>
	</div>
	<div class="jl_parentContainer">
		<span class="clubinfo_listing_item"> <?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_TEAM_NAME_SHORT'); ?></span>
		<span class="clubinfo_listing_value"> <?php
		$link = JoomleagueHelperRoute::getTeamStatsRoute($this->project->slug, $this->team->slug);
		echo JHtml::link($link, $this->team->short_name);
		?>
		</span>
	</div>
			<?php
			if ($this->team->info)
			{
				?>
	<div class="jl_parentContainer">
		<span class="clubinfo_listing_item"> <?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_INFO'); ?></span>
		<span class="clubinfo_listing_value"> <?php echo $this->team->info; ?></span>
	</div>
			<?php
			}

			if ($this->team->website)
			{
				?>
	<div class="jl_parentContainer">
		<span class="clubinfo_listing_item"> <?php echo JText::_('COM_JOOMLEAGUE_TEAMINFO_TEAM_SITE'); ?></span>
		<span class="clubinfo_listing_value"> <?php echo JHtml::link($this->team->team_website, $this->team->team_website, array( "target" => "_blank")); ?></span>
	</div>
			<?php
			}
		}
		?>
	</div>
	<br />
	<?php
	}
}
?>
