<?php

namespace Webmaxsk\MaxDocuments;

use ReflectionClass;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Assets\File;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use Bummzack\SortableFile\Forms\SortableUploadField;

class ObjectDocumentsExtension extends DataExtension {

    private static $documents = [
        'enabled' => true,
        'count' => 20,
    ];

	private static $db = [
		'DocumentsContent' => 'HTMLText',
		'DocumentsSorter' => 'Enum("SortOrder, Title, Name, ID")'
	];

	private static $many_many = [
		'Documents' => File::class
	];

    private static $many_many_extraFields = [
        'Documents' => ['SortOrder' => 'Int']
    ];

	private static $owns = [
		'Documents'
	];

	public function updateCMSFields(FieldList $fields) {
		$documentsTab = $fields->findOrMakeTab('Root.Documents');

        if ($this->owner->getDocumentsOption('enabled')) {
            $fields->addFieldToTab('Root.Documents', HtmlEditorField::create('DocumentsContent',_t('Object.DOCUMENTSCONTENT', 'Content'))->setRows(3));

            $limit = $this->owner->getDocumentsOption('count');

            $uploadClass = ($this->owner->DocumentsSorter == 'SortOrder') ? SortableUploadField::class : UploadField::class;

            $documentField = $uploadClass::create('Documents');
            $documentField->setAllowedMaxFileNumber($limit);
            $documentField->setAllowedFileCategories(['document','archive']);
            $documentField->setFolderName('Uploads/' . (new ReflectionClass($this->owner))->getShortName() . '/' . $this->owner->ID);

            if ($limit == 1) {
                $documentsTab->setTitle(_t('Object.DOCUMENTTAB', 'Document'));
                $documentField->setTitle(_t('Object.DOCUMENTUPLOADLABEL', 'Document'));
            }
            else {
                $documentsTab->setTitle(_t('Object.DOCUMENTSTAB', 'Documents'));
                $documentField->setTitle(_t('Object.DOCUMENTSUPLOADLABEL', 'Documents'));
                $documentField->setDescription(sprintf(_t('Object.DOCUMENTSUPLOADLIMIT','Documents count limit: %s'), $limit));

                if ($this->owner->DocumentsSorter == 'SortOrder')
                    $message = _t('Object.DOCUMENTSUPLOADHEADING', '<span style="color: green">Sort documents by dragging thumbnail.</span>');
                else
                    $message = _t('Object.DOCUMENTSSORTERNOTICE', 'Correct document sorting is visible on frontend only (if Sort by = Title, ID)');

                $fields->addFieldToTab('Root.Documents',
                    DropdownField::create('DocumentsSorter', _t('Object.DOCUMENTSSORTER', 'Sort documents by:'))->setSource($this->owner->dbObject('DocumentsSorter')->enumValues())
                        ->setDescription($message)
                );
            }

            $fields->addFieldToTab('Root.Documents', $documentField);
        }
        else {
            $fields->removeByName('DocumentsSorter');
            $fields->removeByName($documentsTab->Name);
        }
	}

	public function SortedDocuments() {
		return $this->owner->Documents()->Sort($this->owner->DocumentsSorter);
	}

	public function MainDocument() {
		return $this->owner->Documents()->Sort($this->owner->DocumentsSorter)->limit(1)->First();
	}

    public function getDocumentsOption($key)
    {
        $settings = $this->getDocumentsOptions();
        $value = null;

        if (isset($settings[$key])) {
            $value = $settings[$key];
        }

        // To allow other extensions to customise this option
        if ($this->owner) {
            $this->owner->extend('updateDocumentsOption', $key, $value);
        }

        return $value;
    }

    public function getDocumentsOptions()
    {
        $settings = [];

        if ($this->owner) {
            $settings = $this->owner->config()->get('documents');
        } else {
            $settings = Config::inst()->get(__CLASS__, 'documents');
        }

        return $settings;
    }
}

// EOF
