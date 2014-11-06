/**
* @copyright	Copyright (C) 2005-2014 joomleague.at. All rights reserved.
* @license	GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

Joomla.submitbutton = function(task) {
	if (task == 'division.cancel') {
		Joomla.submitform(task);
		return;
	}
	var form = $('adminForm');
	var validator = document.formvalidator;
	
	if (validator.isValid(form)) {
		Joomla.submitform(task);
		return true;   
    }
    else {
		// do field validation
		if (validator.validate(form.name) === false) {
			alert(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_DIVISION_CSJS_NO_NAME'));
			form.name.focus();
			res = false;
		} 
	}
}
