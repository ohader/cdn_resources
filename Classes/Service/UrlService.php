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
class Tx_CdnResources_Service_UrlService implements t3lib_Singleton {
	/**
	 * @return Tx_CdnResources_Service_UrlService
	 */
	public static function getInstance() {
		return t3lib_div::makeInstance('Tx_CdnResources_Service_UrlService');
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public function prependStaticUrl($url) {
		$urlParts = parse_url($url);

		if (!empty($urlParts['host'])) {
			return $url;
		}

		$prependParts = parse_url($this->getConfigurationService()->getPrependStaticUrl());

		if (empty($prependParts['host']) && empty($prependParts['scheme']) && !empty($prependParts['path'])) {
			$prependParts['host'] = $prependParts['path'];
			unset($prependParts['path']);
		}

		$parts = array(
			'scheme' => (empty($prependParts['scheme'])
				? ($this->isSecure() ? 'https' : 'http')
				: $prependParts['scheme']),
			'host' => $prependParts['host'],
			'path' => $urlParts['path'],
		);

		if (!empty($urlParts['query'])) {
			$parts['query'] = $urlParts['query'];
		}

		if (!empty($urlParts['fragment'])) {
			$parts['fragment'] = $urlParts['fragment'];
		}

		$url = $this->build($parts);
		return $url;
	}

	/**
	 * @param string $url
	 * @return boolean
	 */
	public function equalsCurrentHost($url) {
		$result = FALSE;

		$urlParts = parse_url($url);
		$currentParts = parse_url(t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST'));

		if (empty($urlParts['host']) && empty($urlParts['scheme']) && !empty($urlParts['path'])) {
			$urlParts['host'] = $urlParts['path'];
			unset($urlParts['path']);
		}

		if (!empty($urlParts['host']) && !empty($currentParts['host']) && $urlParts['host'] === $currentParts['host']) {
			$result = TRUE;
		}

		return $result;
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public function get($url) {
		$prependParts = parse_url($this->getConfigurationService()->getPrependStaticUrl());

		if (empty($prependParts['host']) && empty($prependParts['scheme']) && !empty($prependParts['path'])) {
			$prependParts['host'] = $prependParts['path'];
			unset($prependParts['path']);
		}

		$parts = array(
			'scheme' => (empty($prependParts['scheme'])
				? ($this->isSecure() ? 'https' : 'http')
				: $prependParts['scheme']),
			'host' => $prependParts['host'],
		);

		return $this->build($parts);
	}

	/**
	 * @param array $urlParts
	 * @return string
	 */
	public function build(array $urlParts) {
		return (isset($urlParts['scheme']) ? $urlParts['scheme'] . '://' : '')
			. (isset($urlParts['user']) ? $urlParts['user'] . (isset($urlParts['pass']) ? ':' . $urlParts['pass'] : '') . '@' : '')
			. (isset($urlParts['host']) ? $urlParts['host'] : '') . '/' . ltrim((isset($urlParts['path']) ? $urlParts['path'] : ''), '/')
			. (isset($urlParts['query']) ? '?' . $urlParts['query'] : '') . (isset($urlParts['fragment']) ? '#' . $urlParts['fragment'] : '');
	}

	/**
	 * @return boolean
	 */
	public function isSecure() {
		return t3lib_div::getIndpEnv('TYPO3_SSL');
	}

	/**
	 * @return Tx_CdnResources_Service_ConfigurationService
	 */
	protected function getConfigurationService() {
		return Tx_CdnResources_Service_ConfigurationService::getInstance();
	}
}
?>