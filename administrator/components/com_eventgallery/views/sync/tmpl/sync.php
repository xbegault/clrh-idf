<?php

/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access'); 
$document = JFactory::getDocument();
$version =  new JVersion();
if ($version->isCompatible('3.0')) {

} else {
    $css=JURI::base().'components/com_eventgallery/media/css/legacy.css';
    $document->addStyleSheet($css);
}
?>

<progress id="syncprogress" value="0" max="100"></progress>

<div class="row-fluid">
<?php
    foreach($this->folders as $folder) {
        echo '<div class="span4 eventgallery-folder" data-status="open" data-folder="'.$folder.'">'.$folder.'</div>';
}
?>
</div>

<form action="index.php" method="post" name="adminForm" id="adminForm">
    <input type="hidden" name="option" value="com_eventgallery" />
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>


<style type="text/css">
    #syncprogress {
        width: 100%;
        height: 20px;
    }

    .eventgallery-folder {
        float: left;
        margin: 10px;
        padding: 10px;
        border: 1px solid #DDD;
        -webkit-box-shadow: 1px 1px 1px rgba(50, 50, 50, 0.75);
        -moz-box-shadow:    1px 1px 1px rgba(50, 50, 50, 0.75);
        box-shadow:         1px 1px 1px rgba(50, 50, 50, 0.75);

        box-sizing:border-box;
        -moz-box-sizing:border-box; /* Firefox */
    }

    .done {
        -webkit-box-shadow: 0px 0px 0px rgba(50, 50, 50, 0.75);
        -moz-box-shadow:    0px 0px 0px rgba(50, 50, 50, 0.75);
        box-shadow:         0px 0px 0px rgba(50, 50, 50, 0.75);
    }

    .sync {
        background-color: darkseagreen;
    }

    .no-sync {
        background-color: lightblue;
    }

    .deleted {
        background-color: lightsalmon;
    }
</style>

<script>

(function() {

    var folderContainers  = new Array();
    var max = 0;

    function syncFolder() {

        updateProcess();

        if (folderContainers.length==0) {
            done();
            return;
        }

        var myElement = folderContainers.pop();
        var myRequest = new Request.JSON({
            url: '<?php echo JRoute::_('index.php?option=com_eventgallery&format=raw&task=sync.process&'.JSession::getFormToken().'=1', false);?>',
            method: 'get',
            onRequest: function(){
                myElement.set('html', 'loading...');
            },
            onSuccess: function(responseJSON, responseText){
                myElement.addClass('done');
                var text = "";
                var cssClass = "";

                if (responseJSON.status == 'sync') {
                    text = responseJSON.folder + " synced";
                    cssClass = "sync";
                }

                if (responseJSON.status == 'deleted') {
                    text = responseJSON.folder + " deleted";
                    cssClass = "deleted";
                }

                if (responseJSON.status == 'nosync') {
                    text = responseJSON.folder + " not synced";
                    cssClass = "no-sync";
                }
                myElement.set('html', text);
                myElement.addClass(cssClass);
                syncFolder();
            },
            onFailure: function(xhr){
                myElement.addClass('failed');
                myElement.set('html', 'Sorry, your request failed :('+xhr.status+')');
                console.log(xhr);
                syncFolder();
            },
            onError: function(text, error) {
                myElement.set('html', 'Sorry, an error occured :('+error+')<br>' + text);
                console.log(text, error);
                syncFolder();
            }
        });

        myRequest.send('folder='+myElement.getAttribute('data-folder'));
      
    }

    function start() {
        $$(".eventgallery-folder").each(function(item){
            folderContainers.push(item);
        }.bind(this));

        max = folderContainers.length;
        $('syncprogress').setAttribute('max', max);
        $('syncprogress').setAttribute('value', 0);


        syncFolder();

    }

    function updateProcess() {

        $('syncprogress').setAttribute('value', max-folderContainers.length);
    }

    function done() {
        alert('Done.');
    }

    window.addEvent('domready', function() {
        start();
    });


})();
</script>