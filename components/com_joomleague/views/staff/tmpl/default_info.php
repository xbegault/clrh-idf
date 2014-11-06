<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<!-- person data START -->
<h2><?php	echo JText::_( 'COM_JOOMLEAGUE_PERSON_PERSONAL_DATA' );	?></h2>

<table class="plgeneralinfo">
	<tr>
		<?php
		if ( $this->config['show_photo'] == 1 )
		{
			?>
			<td class="picture">
				<?php
				$picturetext=JText::_( 'COM_JOOMLEAGUE_PERSON_PICTURE' );
				$imgTitle = JText::sprintf( $picturetext, JoomleagueHelper::formatName(null, $this->person->firstname, $this->person->nickname, $this->person->lastname, $this->config["name_format"]) );
				$picture = $this->teamStaff->picture;
				if ((empty($picture))|| ($picture == JoomleagueHelper::getDefaultPlaceholder("player")  ))
				{
				$picture = $this->person->picture;
				}
				if ( !file_exists( $picture ) )
				{
					$picture = JoomleagueHelper::getDefaultPlaceholder("player") ;
				}
				echo JoomleagueHelper::getPictureThumb($picture, $imgTitle,
														$this->config['picture_width'],
														$this->config['picture_height']);
				?>
			</td>
			<?php
		}
		?>
		<td class="info">
			<table class="plinfo">
				<?php
				if(!empty($this->person->country) && $this->config["show_nationality"] == 1)
				{
				?>
				<tr>
					<td class="label"><?php echo JText::_( 'COM_JOOMLEAGUE_PERSON_NATIONALITY' ); ?>
					</td>
					<td class="data">
					<?php
						echo Countries::getCountryFlag( $this->person->country ) . " " .
						JText::_( Countries::getCountryName($this->person->country));
						?>
					</td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td class="label">
						<?php echo JText::_( 'COM_JOOMLEAGUE_PERSON_NAME' ); ?>
					</td>
					<td class="data">
						<?php
						$outputName = JText::sprintf( '%1$s %2$s', $this->person->firstname, $this->person->lastname);
						if ( $this->person->id )
						{
							switch ( $this->config['show_user_profile'] )
							{
								case 1:	 // Link to Joomla Contact Page
											$link = JoomleagueHelperRoute::getContactRoute( $this->person->contact_id );
											$outputName = JHtml::link( $link, $outputName );
											break;

								case 2:	 // Link to CBE User Page with support for JoomLeague Tab
											$link = JoomleagueHelperRoute::getUserProfileRouteCBE(	$this->person->user_id,
																									$this->project->id,
																									$this->person->id );
											$outputName = JHtml::link( $link, $outputName );
											break;

								default:	break;
							}
						}
						echo $outputName;
						?>
					</td>
				</tr>
				<?php if ( ! empty( $this->person->nickname ) )
				{
					?>
				<tr>
					<td class="label"><?php
					echo JText::_( 'COM_JOOMLEAGUE_PERSON_NICKNAME' );
					?></td>
					<td class="data"><?php
					echo $this->person->nickname;
					?></td>
				</tr>
				<?php
				}
				if (	( $this->config[ 'show_birthday' ] > 0 ) &&
						( $this->config[ 'show_birthday' ] < 5 ) &&
						( $this->person->birthday != '0000-00-00' ) )
				{
					#$this->config['show_birthday'] = 4;
					?>
					<tr>
						<td class="label">

								<?php
								switch ( $this->config['show_birthday'] )
								{
									case 	1:			// show Birthday and Age
														$outputStr = 'COM_JOOMLEAGUE_PERSON_BIRTHDAY_AGE';
														break;

									case 	2:			// show Only Birthday
														$outputStr = 'COM_JOOMLEAGUE_PERSON_BIRTHDAY';
														break;

									case 	3:			// show Only Age
														$outputStr = 'COM_JOOMLEAGUE_PERSON_AGE';
														break;

									case 	4:			// show Only Year of birth
														$outputStr = 'COM_JOOMLEAGUE_PERSON_YEAR_OF_BIRTH';
														break;
								}
								echo JText::_( $outputStr );
								?>

						</td>
						<td class="data">
							<?php
							#$this->assignRef( 'playerage', $model->getAge( $this->player->birthday, $this->project->start_date ) );
							switch ( $this->config['show_birthday'] )
							{
								case 1:	 // show Birthday and Age
											$birthdateStr =	$this->person->birthday != "0000-00-00" ?
															JHtml::date( $this->person->birthday, JText::_( 'COM_JOOMLEAGUE_GLOBAL_DAYDATE' ), $this->overallconfig['time_zone'] ) : "-";
											$birthdateStr .= "&nbsp;(" . JoomleagueHelper::getAge( $this->person->birthday,$this->person->deathday ) . ")";
											break;

								case 2:	 // show Only Birthday
											$birthdateStr =	$this->person->birthday != "0000-00-00" ?
															JHtml::date( $this->person->birthday, JText::_( 'COM_JOOMLEAGUE_GLOBAL_DAYDATE' ), $this->overallconfig['time_zone'] ) : "-";
											break;

								case 3:	 // show Only Age
											$birthdateStr = JoomleagueHelper::getAge( $this->person->birthday,$this->person->deathday );
											break;

								case 4:	 // show Only Year of birth
											$birthdateStr =	$this->person->birthday != "0000-00-00" ?
															JHtml::date( $this->person->birthday, JText::_( '%Y' ), $this->overallconfig['time_zone'] ) : "-";
											break;

								default:	$birthdateStr = "";
											break;
							}
							echo $birthdateStr;
							?>
						</td>
					</tr>
					<?php if( $this->person->deathday != '0000-00-00' ) {?>
					<tr>
					<td class="label">
					<?php
						$outputStr = 'COM_JOOMLEAGUE_PERSON_DEATHDAY';
						echo JText::_( $outputStr );
						?>
						</td>
						<td class="data">
						<?php
						$deathdateStr =	$this->person->deathday != "0000-00-00" ?
							JHtml::date( $this->person->deathday, JText::_( 'COM_JOOMLEAGUE_GLOBAL_DAYDATE' ), $this->overallconfig['time_zone'] ) : "-";
							echo '&dagger; '.$deathdateStr;
						?>
						</td>
					</tr>
					<?php
					}
				}
			if (( $this->person->address != "" ) && ( $this->config[ 'show_person_address' ] ==1  ))
				{
					?>
				<tr>
					<td class="label"><?php echo JText::_( 'COM_JOOMLEAGUE_PERSON_ADDRESS' ); ?></td>
					<td class="data"><?php
						echo Countries::convertAddressString(	'',
																$this->person->address,
																$this->person->state,
																$this->person->zipcode,
																$this->person->location,
																$this->person->address_country,
																'COM_JOOMLEAGUE_PERSON_ADDRESS_FORM' );
					?></td>
				</tr>
				<?php
				}
			if (( $this->person->phone != "" ) && ( $this->config[ 'show_person_phone' ] ==1  ))
				{
					?>
					<tr>
						<td class="label">

								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PERSON_PHONE' );
								?>

						</td>
						<td class="data">
							<?php
							echo $this->person->phone;
							?>
						</td>
					</tr>
					<?php
				}

			if (( $this->person->mobile != "" ) && ( $this->config[ 'show_person_mobile' ] ==1  ))
				{
					?>
					<tr>
						<td class="label">

								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PERSON_MOBILE' );
								?>

						</td>
						<td class="data">
							<?php
							echo $this->person->mobile;
							?>
						</td>
					</tr>
					<?php
				}

			if (( $this->person->email != "" ) && ($this->config['show_person_email'] == 1))
			{
					?>
					<tr>
						<td class="label">

								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PERSON_EMAIL' );
								?>

						</td>
						<td class="data">
							<?php
							$user = JFactory::getUser();
							if ( ( $user->id ) || ( ! $this->overallconfig['nospam_email'] ) )
							{
								?>
								<a href="mailto: <?php echo $this->person->email; ?>">
									<?php
									echo $this->club->email;
									?>
								</a>
								<?php
							}
							else
							{
								echo JHtml::_('email.cloak', $this->person->email );
							}
							?>
						</td>
					</tr>
					<?php
			}

			if (( $this->person->website != "" ) && ($this->config['show_person_website'] == 1))
				{
					?>
					<tr>
						<td class="label">

								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PERSON_WEBSITE' );
								?>

						</td>
						<td class="data">
							<?php
							echo JHtml::_(	'link',
											$this->person->website,
											$this->person->website,
											array( 'target' => '_blank' ) );
							?>
						</td>
					</tr>
					<?php
				}

			if (( $this->person->height > 0 ) && ($this->config['show_person_height'] == 1))
				{
					?>
					<tr>
						<td class="label">

								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PERSON_HEIGHT' );
								?>

						</td>
						<td class="data">
							<?php
							echo str_replace( "%HEIGHT%", $this->person->height, JText::_( 'COM_JOOMLEAGUE_PERSON_HEIGHT_FORM' ) );
							?>
						</td>
					</tr>
					<?php
				}
			if (( $this->person->weight > 0 ) && ($this->config['show_person_weight'] == 1))
				{
					?>
					<tr>
						<td class="label">

								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PERSON_WEIGHT' );
								?>

						</td>
						<td class="data">
							<?php
							echo str_replace( "%WEIGHT%", $this->person->weight, JText::_( 'COM_JOOMLEAGUE_PERSON_WEIGHT_FORM' ) );
							?>
						</td>
					</tr>
					<?php
					}
				if ( isset($this->teamStaff->position_id) && $this->teamStaff->position_id > 0 )
				{
					?>
				<tr>
					<td class="label"><?php
					echo JText::_( 'COM_JOOMLEAGUE_PERSON_POSITION' );
					?></td>
					<td class="data"><?php
					echo JText::_( $this->teamStaff->position_name );
					?></td>
				</tr>
				<?php
				}

			if (( ! empty( $this->person->knvbnr ) ) && ($this->config['show_person_regnr'] == 1))
				{
					?>
					<tr>
						<td class="label">

								<?php
								echo JText::_( 'COM_JOOMLEAGUE_PERSON_REGNO' );
								?>

						</td>
						<td class="data">
							<?php
							echo $this->person->knvbnr;
							?>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
		</td>
	</tr>
</table>
<br />