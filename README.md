# CDN Resources

This extension provides a way to rewrite resource URIs in HTML to point to
a particular hostname - e.g. a static CDN resource. Stylesheets, JavaScript
files and images are considered as resources in that regard.

## Requirements

+ TYPO3 CMS 6.2 or any later version

## Concept

Imagine your website is running on the URL http://www.domain.com/ and is used
to server dynamic contents as well as static resources. The lower the stress
and traffic on your regular web-server, static resources are target to be
served by a different server or a content delivery network.

It would be possible to deliver all contents (including the dynamically created
ones) using a CDN. However, one needs to take care of proper HTTP expiration and
caching headers individually.

The workflow then is rather simple, using the assumption that the CDN is used
as plain edge cache in this scenario.

+ user requests http://www.domain.com/ (provided by the CDN in this scenario)
+ CDN checks for the existence of that cache
  + if available, the data is returned directly from the CDN
  + if not available, the CDN issues another request to http://origin.domain.com/
    to retrieve the accordant data
+ user does not realize which server was used under the hood

## Configuration

This extension has some configuration properties that can be modified by
using the TYPO3 CMS Extension Manager.

+ prependStaticUrl: Static URL to prepend for resources
+ originUrl: Origin URL of CDN used to fetch data
+ enableAdaptiveImages: Whether to enable URL rewriting for adaptive-images
+ jQueryTrigger: Basic jQuery component, used to extend with adaptive-image replacement