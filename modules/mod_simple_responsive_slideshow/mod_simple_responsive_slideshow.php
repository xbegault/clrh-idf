<?php
/**
* @file
* @brief    Simple Joomla!3 module implementation for FlexSlider 1.8, the best responsive jQuery slide around.
* @author   FavThemes
* @version  2.3
* @remarks  Copyright (C) 2013 FavThemes (WooThemes for the original script)
* @remarks  Licensed under GNU/GPLv3, see http://www.gnu.org/licenses/gpl-3.0.html
* @see      http://www.favthemes.com/extensions/product/5-simple-responsive-slideshow-for-joomla.html
*/

// no direct access

defined('_JEXEC') or die;

JHTML::script('https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js');
JHTML::script('modules/mod_simple_responsive_slideshow/js/jquery.flexslider.js');
JHTML::stylesheet('modules/mod_simple_responsive_slideshow/theme/flexslider.css');

$animation = $params->get('animation');
$slidedirection = $params->get('slideDirection');
$slideshowspeed = $params->get('slideshowSpeed');
$animationduration = $params->get('animationDuration');

$slideshow = ($params->get('slideshow') == 1) ? 'true' : 'false';
$directionnav = ($params->get('directionNav') == 1) ? 'true' : 'false';
$controlnav = ($params->get('controlNav') == 1) ? 'true' : 'false';
$keyboardnav = ($params->get('keyboardNav') == 1) ? 'true' : 'false';
$mousewheel = ($params->get('mousewheel') == 1) ? 'true' : 'false';
$randomize = ($params->get('randomize') == 1) ? 'true' : 'false';
$animationloop = ($params->get('animationLoop') == 1) ? 'true' : 'false';
$pauseonaction = ($params->get('pauseOnAction') == 1) ? 'true' : 'false';
$pauseonhover = ($params->get('pauseOnHover') == 1) ? 'true' : 'false';

$flexsliderBgColor = $params->get('flexsliderBgColor');

$rand = rand(10000,20000);

for ($i=1;$i<=10;$i++) {

${'file'.$i} = $params->get('file'.$i);
${'file'.$i.'link'} = $params->get('file'.$i.'link');
${'file'.$i.'caption'} = $params->get('file'.$i.'caption');
${'file'.$i.'alt'} = $params->get('file'.$i.'alt');

}

?>

<?php echo '<script type="text/javascript">'; ?>

<?php echo'jQuery.noConflict();
				jQuery(window).load(function() {
				jQuery(\'#favsimple-'.$rand.'\').flexslider({
				  animation: "'.$animation.'",
				  slideDirection: "'.$slidedirection.'",
				  slideshow: '.$slideshow.',';
if ($slideshow == "true") { echo '
			          slideshowSpeed: '.$slideshowspeed.',
				  animationDuration: '.$animationduration.','; }
echo '
				  directionNav: '.$directionnav.',
				  controlNav: '.$controlnav.',
				  keyboardNav: '.$keyboardnav.',
				  mousewheel: '.$mousewheel.',
				  randomize: '.$randomize.',
				  animationLoop: '.$animationloop.',
				  pauseOnAction: '.$pauseonaction.',
				  pauseOnHover: '.$pauseonhover.'
			  });
			});

</script>'; ?>

			<div id="favsimple-<?php echo $rand; ?>" class="flexslider"
				style="background-color: #<?php echo $flexsliderBgColor; ?>;"
			>
		    <ul class="slides">
<?php for ($i=1;$i<=10;$i++) { if (${'file'.$i} && ${'file'.$i} != " ") {?>
		    	<li>
		    		<?php if (${'file'.$i.'link'}) { ?> <a href="<?php echo ${'file'.$i.'link'}; ?>" class="modalizer"><img src="<?php echo ${'file'.$i}; ?>" alt="<?php echo ${'file'.$i.'alt'}; ?>" /></a><?php } else { ?> <img src="<?php echo ${'file'.$i}; ?>" alt="<?php echo ${'file'.$i.'alt'}; ?>" /> <?php } ?>
		    		<?php if (${'file'.$i.'caption'}) { ?> <p class="flex-caption"><?php echo ${'file'.$i.'caption'}; ?></p> <?php } ?>
		    	</li>
<?php } }?>
		    </ul>
		  </div>


