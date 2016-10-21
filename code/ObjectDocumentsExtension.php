<?php

class ObjectDocumentsExtension extends DataExtension {

	private static $allow_documents = true;
	private static $documents_count = 20;
	
	private static $db = array(
		'DocumentsContent' => 'HTMLText',
		'DocumentsSorter' => 'Enum("SortOrder, Title, Name, ID")'
	);
	
	private static $many_many = array(
		'Documents' => 'File'
	);
	
	private static $many_many_extraFields = array(
		'Documents' => array('SortOrder' => 'Int')
	);
	
	public function updateCMSFields(FieldList $fields) {      
		 // Use SortableUploadField instead of UploadField!
		$documentsTab = $fields->findOrMakeTab('Root.Documents');

		$owner = $this->owner;

		if ($owner::config()->allow_documents) {
			$fields->addFieldToTab('Root.Documents', HtmlEditorField::create('DocumentsContent',_t("Object.DOCUMENTSCONTENT", "Content"))->setRows(3));

			$limit = $owner::config()->documents_count;

			$uploadClass = (class_exists("SortableUploadField") && $this->owner->DocumentsSorter == "SortOrder") ? "SortableUploadField" : "UploadField";

			$documentField = $uploadClass::create('Documents');
			$documentField->setConfig('allowedMaxFileNumber', $limit);
			$documentField->setAllowedFileCategories('zip','doc');
			$documentField->setAllowedExtensions(array_merge(array('zip'), $documentField->getAllowedExtensions()));
			$documentField->setFolderName('Uploads/'.$this->owner->ClassName.'/'.$this->owner->ID);

			if ($limit==1) {
				$documentsTab->setTitle(_t("Object.DOCUMENTTAB", "Document"));
				$documentField->setTitle(_t("Object.DOCUMENTUPLOADLABEL", "Document"));
			}
			else {
				$documentsTab->setTitle(_t("Object.DOCUMENTSTAB", "Documents"));
				$documentField->setTitle(_t("Object.DOCUMENTSUPLOADLABEL", "Documents"));
				$documentField->setDescription(sprintf(_t("Object.DOCUMENTSUPLOADLIMIT","Documents count limit: %s"), $limit));

				if ($this->owner->DocumentsSorter == "SortOrder")  {
					$message = (class_exists("SortableUploadField")) ? _t("Object.DOCUMENTSUPLOADHEADING", "<span style='color: green'>Sort documents by draging thumbnail</span>") : _t("Object.DOCUMENTSUPLOADHEADINGWRONG", "<span style='color: red'>Sorting documents by draging thumbnails (SortOrder) not allowed. Missing module SortabeUploadField.</span>"); 
				} else {
					$message = _t("Object.DOCUMENTSSORTERNOTICE", "Correct document sorting is visible on frontend only (if Sort by = Title, ID)");
				}

				$dropdownSorter = DropdownField::create('DocumentsSorter', _t("Object.DOCUMENTSSORTER", "Sort documents by:"))->setSource($this->owner->dbObject('DocumentsSorter')->enumValues());
				$fields->addFieldToTab('Root.Documents', $dropdownSorter);

				$fields->addFieldToTab('Root.Documents', HeaderField::create('DocumentsNotice', $message)->setHeadingLevel(4));
			}

			$fields->addFieldToTab('Root.Documents', $documentField);
		}
		else
			$fields->removeByName($documentsTab->Name);
	}

	public function SortedDocuments() {
		return $this->owner->Documents()->Sort($this->owner->DocumentsSorter);
	}

	public function MainDocument() {
		return $this->owner->Documents()->Sort($this->owner->DocumentsSorter)->limit(1)->First();
	}
}

// EOF
