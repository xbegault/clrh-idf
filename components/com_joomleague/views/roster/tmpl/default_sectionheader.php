<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<table class="contentpaneopen">
	<tr>
		<td class="contentheading">
		<?php
		echo $this->pagetitle;
		if ( $this->showediticon )
		{
			$modalheight = JComponentHelper::getParams('com_joomleague')->get('modal_popup_height', 600);
			$modalwidth = JComponentHelper::getParams('com_joomleague')->get('modal_popup_width', 900);
			$link = JoomleagueHelperRoute::getPlayersRoute( $this->project->id, 
															$this->team->id, 
															'teamplayer.select', 
															$this->projectteam->division_id, 
															$this->projectteam->ptid);
			echo ' <a rel="{handler: \'iframe\',size: {x:'.$modalwidth.',y:'.$modalheight.'}}" href="'.$link.'" class="modal">';
			echo JHtml::image("media/com_joomleague/jl_images/edit.png",
					JText::_( 'COM_JOOMLEAGUE_ROSTER_EDIT' ),
					array( "title" => JText::_( "COM_JOOMLEAGUE_ROSTER_EDIT" ) )
			);
			echo '</a>';
		}
		?>
		</td>
	</tr>
</table>
<br />
