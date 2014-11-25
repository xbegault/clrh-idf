<?php // no direct access

/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

?>

<?php

/**
 * @var EventgalleryLibraryLineitemcontainer $lineitemcontainer
 */
$lineitemcontainer = $this->lineitemcontainer;

?>

<div class="methodinformation">

    <?php IF ($lineitemcontainer->getPaymentMethod() != NULL):
        $contentPayment = $lineitemcontainer->getPaymentMethod()->getMethodConfirmContent($lineitemcontainer);
        ?>
        <?php IF (strlen($contentPayment)>0): ?>
        <p class="payment-content">
            <?php echo $contentPayment?>
        </p>
    <?php ENDIF; ?>

    <?php ENDIF ?>


    <?php IF ($lineitemcontainer->getShippingMethod() != NULL):
        $contentShipping = $lineitemcontainer->getShippingMethod()->getMethodConfirmContent($lineitemcontainer);
        ?>
        <?php IF (strlen($contentShipping)>0): ?>
        <p class="shipping-content">
            <?php echo $contentShipping?>
        </p>
    <?php ENDIF; ?>
    <?php ENDIF ?>


    <?php IF ($lineitemcontainer->getSurcharge() != NULL):
        $contentSurcharge = $lineitemcontainer->getSurcharge()->getMethodConfirmContent($lineitemcontainer);
        ?>

        <?php IF (strlen($contentSurcharge)>0): ?>
        <p class="surcharge-content">
            <?php echo $contentSurcharge?>
        </p>
    <?php ENDIF; ?>

    <?php ENDIF ?>

</div>
<div style="clear:both"></div>