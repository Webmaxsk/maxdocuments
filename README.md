# maxdocuments
Simple documents for any data object 

## Installation
composer require "webmaxsk/maxdocuments:*"

You can add docs to any Page via CMS. You can disable docs for any Page subclass by adding config to mysite/_config.php:
```php
Blog::config()->allow_documents = false;
Calendar::config()->allow_documents = false;
CalendarEvent::config()->allow_documents = false;
ErrorPage::config()->allow_documents = false;
RedirectorPage::config()->allow_documents = false;
UserDefinedForm::config()->allow_documents = false;
VirtualPage::config()->allow_documents = false;
```

You can add documents to any DataObject too, just extend DataObject with ObjectDocumentsExtension.

## Usage
Add to your template

```html
<% include FilesToDownload %>
```

## Example usage
check https://github.com/Webmaxsk/silverstripe-intranet-plate
