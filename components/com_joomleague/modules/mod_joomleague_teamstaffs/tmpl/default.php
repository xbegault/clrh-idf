<?php

/**
 * @version	 $Id: default.php zeta65$
 * @package	 Joomla
 * @subpackage  Joomleague team players module
 * @copyright	Copyright (C) 2005-2014 joomleague.at. All rights reserved.
 * @license	 GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

defined('_JEXEC') or die('Restricted access');

// check if any players returned
$itemscount = count($list['staffs']);

//print_r($list['roster']);
if (!$itemscount) {
	echo '<p class="modjlgteamplayers">' . JText::_('NO ITEMS') . '</p>';
	return;
}?>

<div class="modjlgteamstaffs"><?php if ($params->get('show_project_name', 0)):?>
<p class="projectname"><?php echo $list['project']->name; ?></p>
<?php endif; ?>


<ul class="modjlgposition">
<h1>
<?php if ($params->get('show_team_name', 0)):?>
	<?php echo $list['project']->team_name; ?>
<?php endif; ?></div>
</h1>
<?php foreach (array_slice($list['staffs'], 0, $params->get('limit', 24)) as $items) :  ?>
	<li>
		<ul class="modjlgstaff">
		<?php foreach (array_slice($items, 0, $params->get('limit', 24)) as $item) : ?>
			<li><?php 
			echo modJLGTeamStaffsHelper::getStaffLink($item, $params);
			?></li>
			<?php	endforeach; ?>
		</ul>
	</li>
	<?php endforeach; ?>
</ul>
