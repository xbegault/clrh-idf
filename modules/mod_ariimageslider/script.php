<?php
/*
 * ARI Framework Lite
 *
 * @package		ARI Framework Lite
 * @version		1.0.0
 * @author		ARI Soft
 * @copyright	Copyright (c) 2009 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die('Restricted access');

class mod_ariimagesliderInstallerScript
{
	function preflight($type, $parent)
	{
		$type = strtolower($type);
		if ($type == 'install' || $type == 'update')
		{
			$this->updateManifest($parent);
		}
	
		if ($type == 'update')
			$this->removeOutdateFiles();
	}
	
	private function updateManifest($parent)
	{
		jimport('joomla.filesystem.file');
		
		$installer = $parent->getParent();
		$manifestFile = basename($installer->getPath('manifest'));
		$cleanManifestFile = preg_replace('/^\_+/i', '', $manifestFile);

		$dir = dirname(__FILE__) . '/install/';

		JFile::delete($dir . $cleanManifestFile);
		JFile::copy($dir . '../' . $cleanManifestFile, $dir . $cleanManifestFile);
	}

	private function removeOutdateFiles()
	{
		jimport('joomla.filesystem.file');

		$colorFieldFile = JPATH_ROOT . '/modules/mod_ariimageslider/mod_ariimageslider/fields/color.php';
		if (file_exists($colorFieldFile))
			JFile::delete($colorFieldFile);

		$colorFolder = JPATH_ROOT . '/modules/mod_ariimageslider/mod_ariimageslider/fields/color';

		if (file_exists($colorFolder))
			JFolder::delete($colorFolder);
	}
}