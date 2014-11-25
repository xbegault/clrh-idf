<?php

// No direct access.
defined('_JEXEC') or die;

?>
<?php if($this->API->modules('footer_nav')) : ?>
<footer id="gkFooter" class="gkPage">
	<div>
		
		<div id="gkFooterNav">
			<jdoc:include type="modules" name="footer_nav" style="<?php echo $this->module_styles['footer_nav']; ?>" modnum="<?php echo $this->API->modules('footer_nav'); ?>" />
		</div>
		<?php endif; ?>
		
		<?php if($this->API->get('framework_logo', '0') == '1') : ?>
		<a href="http://www.gavick.com" rel="nofollow" id="gkFrameworkLogo" title="Gavern Framework">Gavern Framework</a>
		
	</div>
</footer>
<?php endif; ?>