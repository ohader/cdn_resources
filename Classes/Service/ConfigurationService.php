<?php
namespace OliverHader\CdnResources\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Oliver Hader <oliver.hader@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package OliverHader\CdnResources\Service
 */
class ConfigurationService implements SingletonInterface {

	const EXTENSION_Key = 'cdn_resources';
	const NAME_PrependStaticUrl = 'prependStaticUrl';
	const NAME_OriginUrl = 'originUrl';
	const NAME_EnableAdaptiveImages = 'enableAdaptiveImages';
	const NAME_JQueryTrigger = 'jQueryTrigger';

	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * @return ConfigurationService
	 */
	public static function getInstance() {
		return GeneralUtility::makeInstance('OliverHader\\CdnResources\\Service\\ConfigurationService');
	}

	/**
	 * Creates this object
	 */
	public function __construct() {
		$this->configuration = array();

		if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXTENSION_Key])) {
			$this->configuration = (array) unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][self::EXTENSION_Key]);
		}

		$this->configuration = array_map('trim', $this->configuration);
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public function get($name) {
		$result = NULL;

		if (isset($this->configuration[$name])) {
			$result = $this->configuration[$name];
		}

		return $result;
	}

	/**
	 * @return string
	 */
	public function getPrependStaticUrl() {
		return $this->get(self::NAME_PrependStaticUrl);
	}

	/**
	 * @return string
	 */
	public function getOriginUrl() {
		return $this->get(self::NAME_OriginUrl);
	}

	/**
	 * @return boolean
	 */
	public function getEnableAdaptiveImages() {
		return (bool) $this->get(self::NAME_EnableAdaptiveImages);
	}

	/**
	 * @return string
	 */
	public function getJQueryTrigger() {
		return $this->get(self::NAME_JQueryTrigger);
	}

}