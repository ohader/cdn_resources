<?php
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

/**
 * @package cdn_resources
 */
class Tx_CdnResources_Hook_PreProcessHook implements t3lib_Singleton {

	/**
	 * @param array $parameters
	 * @param t3lib_PageRenderer $pageRenderer
	 */
	public function renderPreProcess(array $parameters, t3lib_PageRenderer $pageRenderer) {
		$trigger = $this->getConfigurationService()->getJQueryTrigger();

		if (empty($trigger) || !$this->getConfigurationService()->getEnableAdaptiveImages()) {
			return;
		}

		$trigger = '/' . ltrim($trigger, '/');
		$targets = array('jsLibs', 'jsFooterLibs', 'jsFiles', 'jsFooterFiles');
		foreach ($targets as $target) {
			if (empty($parameters[$target])) {
				continue;
			}

			$inserted = $this->insertAdaptiveImagesHandler($parameters, $target, $trigger);
			if ($inserted) {
				break;
			}
		}
	}

	/**
	 * @param array $parameters
	 * @param string $target
	 * @param string $trigger
	 * @return boolean
	 */
	protected function insertAdaptiveImagesHandler(array $parameters, $target, $trigger) {
		$inserted = FALSE;
		$temporaryDefinitions = array();

		foreach ($parameters[$target] as $fileKey => $fileDefinition) {
			$temporaryDefinitions[$fileKey] = $fileDefinition;
			if (strpos($fileDefinition['file'], $trigger) > 0) {
				$inserted = TRUE;

				// @todo Resolve hard-coded URI
				$file = '/typo3conf/ext/cdn_resources/Resources/Public/JavaScript/AdaptiveImageHandler.js';
				$temporaryDefinitions[$file] = array(
					'file' => $file,
					'type' => $fileDefinition['type'],
					'section' => $fileDefinition['section'],
				);
			}
		}

		if ($inserted) {
			$parameters[$target] = $temporaryDefinitions;
		}

		return $inserted;
	}

	/**
	 * @return tslib_fe
	 */
	protected function getFrontend() {
		return $GLOBALS['TSFE'];
	}

	/**
	 * @return Tx_CdnResources_Service_ConfigurationService
	 */
	protected function getConfigurationService() {
		return Tx_CdnResources_Service_ConfigurationService::getInstance();
	}

}
?>