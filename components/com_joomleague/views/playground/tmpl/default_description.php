<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<?php
if ( $this->playground->notes )
{
?>

<h2><?php echo JText::_('COM_JOOMLEAGUE_PLAYGROUND_NOTES'); ?></h2>
		
	<div class="venuecontent">
    <?php 
    $description = $this->playground->notes;
    $description = JHtml::_('content.prepare', $description);
    echo $description; 
    ?>
    </div>
    <?php
}
?>