<?php
namespace OliverHader\CdnResources\Hook;

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
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use OliverHader\CdnResources\Service\ConfigurationService;
use OliverHader\CdnResources\Service\HttpHeaderService;
use OliverHader\CdnResources\Service\ReplacementService;
use OliverHader\CdnResources\Service\UrlService;

/**
 * @package OliverHader\CdnResources\Hook
 */
class PostProcessHook implements SingletonInterface {

	/**
	 * @param array $parameters
	 * @param TypoScriptFrontendController $frontend
	 */
	public function output(array $parameters, TypoScriptFrontendController $frontend) {
		if (!$this->getConfigurationService()->getPrependStaticUrl()) {
			return;
		}

		$replacementService = $this->createReplacementService($frontend->content);
		$frontend->content = $replacementService->replace();

		$this->outputHeaders();
	}

	/**
	 * @param array $parameters
	 * @param PageRenderer $pageRenderer
	 */
	public function renderPostProcess(array $parameters, PageRenderer $pageRenderer) {
		if (!$this->getConfigurationService()->getPrependStaticUrl()) {
			return;
		}

		$prependStaticUrl = $this->getUrlService()->get(
			$this->getConfigurationService()->getPrependStaticUrl()
		);

		array_unshift(
			$parameters['headerData'],
			GeneralUtility::wrapJS('var prependStaticUrl = "' . htmlspecialchars($prependStaticUrl) . '";')
		);
	}

	/**
	 * @return void
	 */
	protected function outputHeaders() {
		$httpHeaderService = HttpHeaderService::getInstance();

		if ($this->getFrontend()->isStaticCacheble()) {
			$httpHeaderService->setType(HttpHeaderService::VALUE_Type_Static);
		} elseif ($this->getFrontend()->isClientCachable) {
			$httpHeaderService->setType(HttpHeaderService::VALUE_Type_Resources);
		} else {
			$httpHeaderService->setType(HttpHeaderService::VALUE_Type_None);
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
	 * @return TypoScriptFrontendController
	 */
	protected function getFrontend() {
		return $GLOBALS['TSFE'];
	}

	/**
	 * @param string $content
	 * @return ReplacementService
	 */
	protected function createReplacementService($content) {
		return GeneralUtility::makeInstance(
			'OliverHader\\CdnResources\\Service\\ReplacementService',
			$content
		);
	}

	/**
	 * @return UrlService
	 */
	protected function getUrlService() {
		return UrlService::getInstance();
	}

	/**
	 * @return ConfigurationService
	 */
	protected function getConfigurationService() {
		return ConfigurationService::getInstance();
	}

}