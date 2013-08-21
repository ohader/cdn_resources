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
class Tx_CdnResources_Service_ExtractionService implements t3lib_Singleton {
	/**
	 * @return Tx_CdnResources_Service_ExtractionService
	 */
	public static function getInstance() {
		return t3lib_div::makeInstance('Tx_CdnResources_Service_ExtractionService');
	}

	/**
	 * @param string $content
	 * @return array
	 */
	public function findImages($content) {
		$images = array();
		$matches = array();

		if (preg_match_all('#<img([^>]+src="[^"]+"[^>]*?)/?\s*>#', $content, $matches)) {
			foreach ($matches[0] as $index => $tag) {
				$attributes = $this->parseAttributes($matches[1][$index]);

				if (empty($attributes['src'])) {
					continue;
				}

				$images[] = array(
					'tag' => $tag,
					'attributes' => $attributes,
				);
			}
		}

		return $images;
	}

	/**
	 * @param string $content
	 * @return array
	 */
	public function findStylesheets($content) {
		$stylesheets = array();
		$matches = array();

		if (preg_match_all('#<link([^>]+href="[^"]+"[^>]*?)/?\s*>#', $content, $matches)) {
			foreach ($matches[0] as $index => $tag) {
				$attributes = $this->parseAttributes($matches[1][$index]);

				if (empty($attributes['rel']) || $attributes['rel'] !== 'stylesheet') {
					continue;
				}

				$stylesheets[] = array(
					'tag' => $tag,
					'attributes' => $attributes,
				);
			}
		}

		return $stylesheets;
	}

	/**
	 * @param string $content
	 * @return array
	 */
	public function findJavaScripts($content) {
		$scripts = array();
		$matches = array();

		if (preg_match_all('#<script([^>]*)>(.*?)</script>#mis', $content, $matches)) {
			foreach ($matches[0] as $index => $tag) {
				$scripts[] = array(
					'tag' => $tag,
					'attributes' => $this->parseAttributes($matches[1][$index]),
					'content' => $matches[2][$index],
				);
			}
		}

		return $scripts;
	}

	/**
	 * @param string $string
	 * @return array
	 */
	protected function parseAttributes($string) {
		$attributes = array();
		$matches = array();

		if (preg_match_all('#([a-z-]+)\s*=\s*"([^"]+)"#', $string, $matches)) {
			foreach ($matches[1] as $index => $name) {
				$attributes[$name] = $matches[2][$index];
			}
		}

		return $attributes;
	}
}
?>