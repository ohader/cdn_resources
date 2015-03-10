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
class HttpHeaderService implements SingletonInterface {

	const NAME_Type = 'X-CdnResource-Type';
	const VALUE_Type_Resources = 'resources';
	const VALUE_Type_Static = 'static';
	const VALUE_Type_None = 'none';

	/**
	 * @var array
	 */
	protected $headers = array();

	/**
	 * @return HttpHeaderService
	 */
	public static function getInstance() {
		return GeneralUtility::makeInstance('OliverHader\\CdnResources\\Service\\HttpHeaderService');
	}

	/**
	 * @param string $value
	 */
	public function setType($value) {
		$this->set(self::NAME_Type, $value);
	}

	/**
	 * @param string $name
	 * @param string $value
	 */
	public function set($name, $value) {
		$this->headers[$name] = $value;
	}

	/**
	 * @param string $name
	 * @return boolean
	 */
	public function has($name) {
		return isset($this->headers[$name]);
	}

	/**
	 * @return void
	 */
	public function output() {
		foreach ($this->headers as $name => $value) {
			header($name . ': ' . $value);
		}
	}

}