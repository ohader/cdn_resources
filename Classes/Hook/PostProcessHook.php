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
class Tx_CdnResources_Hook_PostProcessHook implements t3lib_Singleton {
	/**
	 * @param array $parameters
	 * @param tslib_fe $frontend
	 */
	public function output(array $parameters, tslib_fe $frontend) {
		if (!$this->getConfigurationService()->getPrependStaticUrl()) {
			return;
		}

		$replacementService = $this->createReplacementService($frontend->content);
		$frontend->content = $replacementService->replace();

		$this->outputHeaders();
	}

	/**
	 * @param array $parameters
	 * @param t3lib_PageRenderer $pageRenderer
	 */
	public function renderPostProcess(array $parameters, t3lib_PageRenderer $pageRenderer) {
		if (!$this->getConfigurationService()->getPrependStaticUrl()) {
			return;
		}

		$prependStaticUrl = $this->getUrlService()->get(
			$this->getConfigurationService()->getPrependStaticUrl()
		);

		array_unshift(
			$parameters['headerData'],
			t3lib_div::wrapJS('var prependStaticUrl = "' . htmlspecialchars($prependStaticUrl) . '";')
		);
	}

	/**
	 * @return void
	 */
	protected function outputHeaders() {
		$httpHeaderService = Tx_CdnResources_Service_HttpHeaderService::getInstance();

		if ($this->getFrontend()->isStaticCacheble()) {
			$httpHeaderService->setType(Tx_CdnResources_Service_HttpHeaderService::VALUE_Type_Static);
		} elseif ($this->getFrontend()->isClientCachable) {
			$httpHeaderService->setType(Tx_CdnResources_Service_HttpHeaderService::VALUE_Type_Resources);
		} else {
			$httpHeaderService->setType(Tx_CdnResources_Service_HttpHeaderService::VALUE_Type_None);
		}

		$httpHeaderService->output();
	}

	/**
	 * @return boolean
	 */
	protected function isOriginUrl() {
		$originUrl = $this->getConfigurationService()->getOriginUrl();
		return (!empty($originUrl) && $this->getUrlService()->equalsCurrentHost($originUrl));
	}

	/**
	 * @return tslib_fe
	 */
	protected function getFrontend() {
		return $GLOBALS['TSFE'];
	}

	/**
	 * @param string $content
	 * @return Tx_CdnResources_Service_ReplacementService
	 */
	protected function createReplacementService($content) {
		return t3lib_div::makeInstance(
			'Tx_CdnResources_Service_ReplacementService',
			$content
		);
	}

	/**
	 * @return Tx_CdnResources_Service_UrlService
	 */
	protected function getUrlService() {
		return t3lib_div::makeInstance('Tx_CdnResources_Service_UrlService');
	}

	/**
	 * @return Tx_CdnResources_Service_ConfigurationService
	 */
	protected function getConfigurationService() {
		return Tx_CdnResources_Service_ConfigurationService::getInstance();
	}
}
?>