<?php
/**
 * @package LiveUpdate
 * @copyright Copyright ©2011-2013 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Configuration class for your extension's updates. Override to your liking.
 */
class LiveUpdateConfig extends LiveUpdateAbstractConfig
{
	var $_extensionName			= 'com_eventgallery';
	var $_extensionTitle		= 'Event Gallery';
	var $_updateURL				= 'http://www.svenbluege.de/?option=com_ars&view=update&format=ini&id=1';
	var $_requiresAuthorization	= false;
	var $_versionStrategy		= 'different';
	var $_storageAdapter		= 'component';
	var $_storageConfig			= array(
		'extensionName'	=> 'com_eventgallery',
		'key'			=> 'liveupdate'
	);

	public function __construct() {
		JLoader::import('joomla.filesystem.file');
		// Load the component parameters, not using JComponentHelper to avoid conflicts ;)
		JLoader::import('joomla.html.parameter');
		JLoader::import('joomla.application.component.helper');
		$db = JFactory::getDbo();
		$sql = $db->getQuery(true)
			->select($db->quoteName('params'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('type').' = '.$db->quote('component'))
			->where($db->quoteName('element').' = '.$db->quote('com_eventgallery'));
		$db->setQuery($sql);
		$rawparams = $db->loadResult();
		$params = new JRegistry();
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$params->loadString($rawparams, 'JSON');
		} else {
			$params->loadJSON($rawparams);
		}

		$sql = $db->getQuery(true)
			->select($db->quoteName('name'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('type').' = '.$db->quote('package'))
			->where($db->quoteName('element').' = '.$db->quote('pkg_eventgallery_full'));
		$db->setQuery($sql);
		$result = $db->loadResult();

		$isFull = $result!=null?true:false;


		// Determine the appropriate update URL based on whether we're on Core or Professional edition
		if($isFull)
		{
			$this->_updateURL = 'http://www.svenbluege.de/?option=com_ars&view=update&format=ini&id=2';
			$this->_extensionTitle = 'Event Gallery Extended';
			$this->_downloadID=$params->get('downloadid',null);

		}

		// Dev releases use the "newest" strategy
		//if(substr($this->_currentVersion,1,2) == 'ev') {
		//	$this->_versionStrategy = 'newest';
		//} else {
			$this->_versionStrategy = 'vcompare';
		//}

		// Get the minimum stability level for updates
		$this->_minStability = $params->get('minstability', 'stable');


		// Do we need authorized URLs?
		$this->_requiresAuthorization = $isFull;

		// Should I use our private CA store?
		if(@file_exists(dirname(__FILE__).'/../assets/cacert.pem')) {
			$this->_cacerts = dirname(__FILE__).'/../assets/cacert.pem';
		}

		parent::__construct();
	}
}