<?php // no direct access

/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

/**
* PARAMS: 
* - address
*/
?>


<?php echo $this->address->getFirstName(); ?> <?php echo $this->address->getLastName(); ?> <br/>
<?php echo $this->address->getAddress1(); ?><br/>
<?php IF (strlen($this->address->getAddress2())>0):?>
    <?php echo $this->address->getAddress2(); ?><br/>
<?php ENDIF; ?>
<?php IF (strlen($this->address->getAddress3())>0):?>
    <?php echo $this->address->getAddress3(); ?><br/>
<?php ENDIF; ?>
<?php echo $this->address->getZip(); ?> <?php echo $this->address->getCity(); ?>
<?php IF (strlen($this->address->getCountry())>0):?>
    <br/><?php echo $this->address->getCountry(); ?>
<?php ENDIF; ?>
