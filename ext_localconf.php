<?php
if (!defined('TYPO3_MODE')) {
	die ("Access denied.");
}

// Hook to post-process resources in content (that have been added directly by COA_INT/USER_INT):
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output']['cdn_resources'] =
	'EXT:cdn_resources/Classes/Hook/PostProcessHook.php:Tx_CdnResources_Hook_PostProcessHook->output';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess']['cdn_resources'] =
	'EXT:cdn_resources/Classes/Hook/PostProcessHook.php:Tx_CdnResources_Hook_PreProcessHook->renderPreProcess';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess']['cdn_resources'] =
	'EXT:cdn_resources/Classes/Hook/PostProcessHook.php:Tx_CdnResources_Hook_PostProcessHook->renderPostProcess';
?>