<?php 

/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 
?>

<a href="#" id="facebook-post-image"><img src="<?php echo JUri::base().'components/com_eventgallery/media/images/social/32/facebook.png' ?>" alt="Facebook" title="Facebook"></a>
<script type="text/javascript">

	var shareFunction = function(e) {
		e.preventDefault();

	    FB.ui(
	    	{
                method: 'feed',
                link: '<?php echo $this->link ?>',
                picture: '<?php echo $this->image ?>',
                caption: '<?php echo $this->description ?>',
                description: '<?php echo $this->description ?>'
            }, 
            function(response){}
        );

	};

	$('facebook-post-image').addEvent('click', shareFunction);

</script> 