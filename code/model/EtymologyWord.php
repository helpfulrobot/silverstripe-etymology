<?php

/*
 * MIT License
 *  
 * Copyright (c) 2016 Hudhaifa Shatnawi
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Jan 6, 2017 - 10:33:31 AM
 */
class EtymologyWord
        extends DataObject
        implements ManageableDataObject {

    private static $db = array(
        'Word' => 'Varchar(255)',
        'Spelling' => 'Varchar(255)',
        'Meaning' => 'Varchar(255)',
        'Classification' => 'Varchar(255)',
        'Description' => 'Varchar(255)',
        'Explanations' => 'HTMLText',
    );
    private static $translate = array(
    );
    private static $has_one = array(
        'Pronunciation' => 'File',
        'Dialect' => 'EtymologyDialect',
    );
    private static $has_many = array(
    );
    private static $many_many = array(
        'References' => 'EtymologyReference',
        'Origins' => 'EtymologyWord',
    );
    private static $belongs_many_many = array(
        'Derivations' => 'EtymologyWord',
    );
    private static $searchable_fields = array(
        'Word' => array(
            'field' => 'TextField',
            'filter' => 'PartialMatchFilter',
        ),
    );
    private static $summary_fields = array(
        'Word',
        'Spelling',
        'Meaning',
        'Classification',
        'Description',
        'Explanations',
    );

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);

        $labels['Word'] = _t('Etymology.WORD', 'Word');
        $labels['Spelling'] = _t('Etymology.SPELLING', 'Spelling');
        $labels['Meaning'] = _t('Etymology.MEANING', 'Meaning');
        $labels['Description'] = _t('Etymology.DESCRIPTION', 'Description');
        $labels['Pronunciation'] = _t('Etymology.PRONUNCIATION', 'Pronunciation');
        $labels['References'] = _t('Etymology.REFERENCES', 'References');
        $labels['Dialect'] = _t('Etymology.DIALECT', 'Dialect');
        $labels['Origins'] = _t('Etymology.ORIGINS', 'Origins');
        $labels['Derivations'] = _t('Etymology.DERIVATIONS', 'Derivations');
        $labels['Classification'] = _t('Etymology.CLASSIFICATION', 'Classification');
        $labels['Explanations'] = _t('Etymology.EXPLANATIONS', 'Explanations');

        return $labels;
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        if ($field = $fields->fieldByName('Root.Main.Pronunciation')) {
            $field->getValidator()->setAllowedExtensions(array('mp3'));
            $field->setFolderName("etymology/pronunciations");

            $fields->removeFieldFromTab('Root.Main', 'Pronunciation');
            $fields->addFieldToTab('Root.Main', $field);
        }

        $this->reorderField($fields, 'Word', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Spelling', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Meaning', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Classification', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Description', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Dialect', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Pronunciation', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Explanations', 'Root.Main', 'Root.Main');

        return $fields;
    }

    public function getTitle() {
        return $this->Word;
    }

    public function canView($member = null) {
        return true;
    }

    /// Utils ///
    function reorderField($fields, $name, $fromTab, $toTab, $disabled = false) {
        $field = $fields->fieldByName($fromTab . '.' . $name);

        if ($field) {
            $fields->removeFieldFromTab($fromTab, $name);
            $fields->addFieldToTab($toTab, $field);

            if ($disabled) {
                $field = $field->performDisabledTransformation();
            }
        }

        return $field;
    }

    function removeField($fields, $name, $fromTab) {
        $field = $fields->fieldByName($fromTab . '.' . $name);

        if ($field) {
            $fields->removeFieldFromTab($fromTab, $name);
        }

        return $field;
    }

    function trim($field) {
        if ($this->$field) {
            $this->$field = trim($this->$field);
        }
    }

    public function getName() {
        return $this->Word;
    }

    public function WordUp() {
        return $this->renderWith('WordSeries_Up');
    }

    public function WordDown() {
        return $this->renderWith('WordSeries_Down');
    }

    public function getObjectItem() {
        return $this->renderWith('Etymology_Item');
    }

    public function getObjectImage() {
        return null;
    }

    public function getObjectDefaultImage() {
        return null;
    }

    public function getObjectLink() {
        return EtymologyPage::get()->first()->Link("show/$this->ID");
    }

    public function getObjectRelated() {
        
    }

    public function getObjectSummary() {
        $lists = array();
        if ($this->Spelling) {
            $lists[] = array(
                'Title' => _t('Etymology.SPELLING', 'Spelling'),
                'Value' => $this->Spelling
            );
        }

        if ($this->Meaning) {
            $lists[] = array(
                'Title' => _t('Etymology.MEANING', 'Meaning'),
                'Value' => $this->Meaning
            );
        }

        if ($this->Dialect()->exists()) {
            $lists[] = array(
                'Title' => _t('Etymology.LANGUAGE', 'Language'),
                'Value' => $this->Dialect()->Language()->Name
            );

            $lists[] = array(
                'Title' => _t('Etymology.DIALECT', 'Dialect'),
                'Value' => $this->Dialect()->Name
            );
        }

        if ($this->Description) {
            $lists[] = array(
                'Title' => _t('Etymology.DESCRIPTION', 'Description'),
                'Value' => '<br />' . $this->Description
            );
        }

        return new ArrayList($lists);
    }

    public function getObjectTabs() {
        $lists = array();

        if ($this->Origins()->Count()) {
            $lists[] = array(
                'Title' => _t('Etymology.ORIGINS', 'Origins'),
                'Content' => $this
                        ->customise(array(
                            'TheWord' => $this,
                            'Dir' => 'origins'
                        ))
                        ->renderWith('WordSeries')
            );
        }

        if ($this->Derivations()->Count()) {
            $lists[] = array(
                'Title' => _t('Etymology.DERIVATIONS', 'Derivations'),
                'Content' => $this
                        ->customise(array(
                            'TheWord' => $this,
                            'Dir' => 'derivations'
                        ))
                        ->renderWith('WordSeries')
            );
        }

        if ($this->References()->Count()) {
            $lists[] = array(
                'Title' => _t('Etymology.REFERENCES', 'References'),
                'Content' => $this->renderWith('Word_References')
            );
        }

        if ($this->Explanations) {
            $lists[] = array(
                'Title' => _t('Etymology.EXPLANATIONS', 'Explanations'),
                'Content' => $this->Explanations
            );
        }

        return new ArrayList($lists);
    }

    public function getObjectTitle() {
        $title = $this->getTitle();

        if ($this->Classification) {
            $title .= '(' . $this->Classification . ')';
        }

        return $title;
    }

    public function isObjectDisabled() {
        return false;
    }

}
