# maxdocuments
Simple documents for any data object

## Installation
```bash
composer require "webmaxsk/maxdocuments:*"
```

You can add docs to any Page via CMS. You can disable docs for any Page subclass by adding config to mysite/_config.php:
```php
SilverStripe\ErrorPage\ErrorPage:
  documents:
    enabled: false
SilverStripe\CMS\Model\VirtualPage:
  documents:
    enabled: false
SilverStripe\CMS\Model\RedirectorPage:
  documents:
    enabled: false
```

The maximum number of docs can be also specified in the config using the following syntax (default is 20 for a page):

```php
SilverStripe\Blog\Model\BlogPost:
  documents:
    count: 50
```


You can add docs to any DataObject too, just extend DataObject with ObjectDocumentsExtension.

## Usage
Add docs to your template

```html
<% include FilesToDownload %>
```

## Example usage
check https://github.com/Webmaxsk/silverstripe-intranet-plate
