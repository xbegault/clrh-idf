/**
* @copyright	Copyright (C) 2005-2014 joomleague.at. All rights reserved.
* @license	GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

Joomla.submitbutton = function(pressbutton) {
	var res = true;
	var form = $('adminForm');

	if (pressbutton == 'teamstaff.cancel') {
		Joomla.submitform(pressbutton);
		if(window.parent.SqueezeBox) {
			window.parent.SqueezeBox.close();
		}
		return;
	}
	
	if (res) {
		Joomla.submitform(pressbutton);
	} else {
		return false;
	}
}