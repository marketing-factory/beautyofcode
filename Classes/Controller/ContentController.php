<?php
namespace FNagel\Beautyofcode\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Thomas Juhnke <tommy@van-tomas.de>
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
 * Class short description
 *
 * Class long description
 *
 * @author Thomas Juhnke <tommy@van-tomas.de>
 */
class ContentController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 *
	 * @var \FNagel\Beautyofcode\Service\LibraryServiceInterface
	 */
	protected $libraryService;

	/**
	 *
	 * @var \TYPO3\CMS\Extbase\Service\FlexFormService
	 */
	protected $flexformService;

	/**
	 *
	 * @param \FNagel\Beautyofcode\Service\LibraryServiceInterface $libraryService
	 */
	public function injectLibraryService(\FNagel\Beautyofcode\Service\LibraryServiceInterface $libraryService) {
		$this->libraryService = $libraryService;
	}

	/**
	 *
	 * @param \TYPO3\CMS\Extbase\Service\FlexFormService $flexformService
	 */
	public function injectFlexformService(\TYPO3\CMS\Extbase\Service\FlexFormService $flexformService) {
		$this->flexformService = $flexformService;
	}

	/**
	 * (non-PHPdoc)
	 * @see \TYPO3\CMS\Extbase\Mvc\Controller\ActionController::initializeAction()
	 */
	public function initializeAction() {
		// @todo: allow merging from flexform
		$this->libraryService->setConfigurationManager($this->configurationManager);
		$this->libraryService->load($this->settings['version']);
	}

	/**
	 *
	 * @return void
	 */
	public function renderAction() {
		$flexform = $this->configurationManager->getContentObject()->data['pi_flexform'];
		$flexformValues = $this->flexformService->convertFlexFormContentToArray($flexform);

		$getCssConfigMethod = 'get' . ucfirst($this->settings['version']) . 'CssConfig';

		$this->view->assignMultiple(array(
			'lang' => $flexformValues['cLang'],
			'label' => $flexformValues['cLabel'],
			'code' => $flexformValues['cCode'],
			'cssConfig' => method_exists($this, $getCssConfigMethod) ? $this->$getCssConfigMethod() : '',
		));
	}

	/**
	 * Function to solve CSSconfiguration which overwrites TS configuration
	 *
	 * @return	string  space and semicolon seperated CSS classes
	 */
	protected function getStandaloneCssConfig() {
		$string = '';
		if (is_array($this->values['css'])) {
			// built brushes string
			$string = '; ';
			foreach ($this->values['css'] as $config => $configValue) {
				// use TS config or not available in SyntaxHighlighter v3
				if (($configValue != "" && $configValue != "auto") && $config != "toolbar") {
					// highlight range
					if ($config == "highlight") {
						$string .= " highlight: [" . \TYPO3\CMS\Core\Utility\GeneralUtility::expandList($configValue) . "]; ";
					} else {
						$state = ($configValue) ? "true" : "false";
						$string .= $config . ": " . $state . "; ";
					}
				}
			}
			$string = substr($string, 0, -2);
		}
		return $string;
	}

	/**
	 * Function to solve CSSconfiguration which overwrites TS configuration
	 *
	 * @return	string  space seperated CSS classes
	 */
	public function getJqueryCssConfig() {
		$string = '';
		if (is_array($this->values['css'])) {
			// built brushes string
			$string = '';
			foreach($this->values['css'] AS $config => $configValue) {
				if ($configValue != "" && $configValue != "auto") {
					// highlight range
					if ($config == "highlight") {
						$string .= " boc-highlight[" . \TYPO3\CMS\Core\Utility\GeneralUtility::expandList($configValue) . "]";
					} else {
						if ($configValue) $string .= " boc-" . $config;
						else $string .= " boc-no-" . $config;
					}
				}
			}
		}
		return $string;
	}
}
?>