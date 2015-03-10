<?php
defined('TYPO3_MODE') or die();

// Hook to post-process resources in content (that have been added directly by COA_INT/USER_INT):
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output']['cdn_resources'] =
	'OliverHader\\CdnResources\\Hook\\PostProcessHook->output';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess']['cdn_resources'] =
	'OliverHader\\CdnResources\\Hook\\PreProcessHook->renderPreProcess';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess']['cdn_resources'] =
	'OliverHader\\CdnResources\\Hook\\PostProcessHook->renderPostProcess';
