<!DOCTYPE html>
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

$title = "";
if (strlen($this->model->folder->getDescription())>0) {
	$title .= $this->model->folder->getDescription();
} else {
	$title .= $this->model->file->getFolderName();
}

$title .= ' - ';

if (strlen($this->model->file->getTitle())>0) {
	$title .= $this->model->file->getPlainTextTitle();
} else {
	$title .= $this->model->file->getFileName();
}

$imageurl = $this->model->file->getOriginalImageUrl();



?><html>
	<head prefix="og: http://ogp.me/ns#">
		
		<meta property="og:image" content="<?php echo  $imageurl; ?>"/>
		<meta property="og:url" content="<?php echo JRoute::_('index.php?option=com_eventgallery&view=singleimage&format=raw&folder='.$this->model->file->getFolderName().'&file='.$this->model->file->getFileName(), null, -1)?>"/>
		<meta property="og:title" content="<?php echo htmlspecialchars($title, ENT_COMPAT, 'UTF-8') ?>"/>
		<link rel="image_src" type="image/jpeg" href="<?php echo $imageurl; ?>"/>
		<title><?php echo $title ?></title>
	</head>
	<body>
		<a href="<?php echo JRoute::_('index.php?option=com_eventgallery&view=event&folder='.$this->model->file->getFolderName())?>">
		<img src="<?php echo  $this->model->file->getImageUrl(600, 600, false) ?>">
		</a>
	</body>
</html>