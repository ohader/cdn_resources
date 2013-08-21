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
class Tx_CdnResources_Service_ReplacementService {
	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var array
	 */
	protected $search = array();

	/**
	 * @var array
	 */
	protected $replace = array();

	/**
	 * @param string $content
	 */
	public function __construct($content) {
		$this->content = (string) $content;
	}

	/**
	 * @return string
	 */
	public function replace() {
		$this->collect();

		return str_replace($this->search, $this->replace, $this->content);
	}

	protected function collect() {
		$this->collectImages();
		$this->collectStyleSheets();
		$this->collectJavaScripts();
	}

	protected function collectImages() {
		foreach ($this->getExtractionService()->findImages($this->content) as $image) {
			$this->prependStaticUrl($image, 'src');
		}
	}

	protected function collectStyleSheets() {
		foreach ($this->getExtractionService()->findStylesheets($this->content) as $styleSheet) {
			$this->prependStaticUrl($styleSheet, 'href');
		}
	}

	protected function collectJavaScripts() {
		foreach ($this->getExtractionService()->findJavaScripts($this->content) as $javaScript) {
			$this->prependStaticUrl($javaScript, 'src');
		}
	}

	/**
	 * @param array $element
	 * @param string $name
	 * @return boolean
	 */
	protected function prependStaticUrl(array $element, $name) {
		if (empty($element['attributes'][$name])) {
			return FALSE;
		}

		$attributeValue = $element['attributes'][$name];
		$modifiedAttributeValue = $this->getUrlService()->prependStaticUrl($attributeValue);

		if ($attributeValue === $modifiedAttributeValue) {
			return FALSE;
		}

		if (in_array($element['tag'], $this->search)) {
			return FALSE;
		}

		$this->search[] = $element['tag'];
		$this->replace[] = str_replace($attributeValue, $modifiedAttributeValue, $element['tag']);

		return TRUE;
	}

	/**
	 * @return Tx_CdnResources_Service_UrlService
	 */
	protected function getUrlService() {
		return t3lib_div::makeInstance('Tx_CdnResources_Service_UrlService');
	}

	/**
	 * @return Tx_CdnResources_Service_ExtractionService
	 */
	protected function getExtractionService() {
		return Tx_CdnResources_Service_ExtractionService::getInstance();
	}
}
?>