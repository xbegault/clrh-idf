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
		$link = JoomleagueHelperRoute::getPlayerRoute( $this->project->id, $this->teamPlayer->team_id, $this->person->id, 'person.edit' );
		echo ' <a rel="{handler: \'iframe\',size: {x:'.$modalwidth.',y:'.$modalheight.'}}" href="'.$link.'" class="modal">';
		echo JHtml::image("media/com_joomleague/jl_images/edit.png",
							JText::_( 'COM_JOOMLEAGUE_PERSON_EDIT' ),
							array( "title" => JText::_( "COM_JOOMLEAGUE_PERSON_EDIT" ) )
		);
	    echo '</a>';
	}

	if ( isset($this->teamPlayer->injury) && $this->teamPlayer->injury )
	{
		$imageTitle = JText::_( 'COM_JOOMLEAGUE_PERSON_INJURED' );
		echo "&nbsp;&nbsp;" . JHtml::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/injured.gif',
							$imageTitle,
							array( 'title' => $imageTitle ) );
	}

	if ( isset($this->teamPlayer->suspension) && $this->teamPlayer->suspension )
	{
		$imageTitle = JText::_( 'COM_JOOMLEAGUE_PERSON_SUSPENDED' );
		echo "&nbsp;&nbsp;" . JHtml::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/suspension.gif',
							$imageTitle,
							array( 'title' => $imageTitle ) );
	}


	if ( isset($this->teamPlayer->away) && $this->teamPlayer->away )
	{
		$imageTitle = JText::_( 'COM_JOOMLEAGUE_PERSON_AWAY' );
		echo "&nbsp;&nbsp;" . JHtml::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/away.gif',
							$imageTitle,
							array( 'title' => $imageTitle ) );
	}
			?>
		</td>
	</tr>
</table>