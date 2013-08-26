/**
 * Adaptive Image Handler
 * (c) 2013 Oliver Hader <oliver.hader@typo3.org>
 *
 * @license GNU General Public License v2 or any later version
 * @package cdn_resources
 */

jQuery(document).ready(function($) {
	var attributeNames = $(['src', 'data-src']);
	var resolutionSteps = [1382, 992, 768, 480];
	var resolutionCurrent = Math.max(screen.width, screen.height);
	var resolution = resolutionSteps[0];

	if (typeof prependStaticUrl === 'undefined' || resolution <= 0) {
		return;
	}

	resolutionSteps = resolutionSteps.sort(function(first, second) { return second - first; });
	$(resolutionSteps).each(function(index, resolutionStep) {
		if (resolutionCurrent > resolutionStep) {
			resolution = resolutionStep;
			return false;
		}
	});

	$('img').each(function(index, element) {
		var $element = $(element);

		attributeNames.each(function(index, attributeName) {
			var attributeValue = $element.attr(attributeName);

			if (attributeValue && attributeValue.indexOf(prependStaticUrl) === 0) {
				attributeValue = prependStaticUrl + 'ai/' + resolution + '/' + attributeValue.substr(prependStaticUrl.length);
				$element.attr(attributeName, attributeValue);
			}
		});
	});
}(jQuery));