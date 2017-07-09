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
 * @version 1.0, Jul 8, 2017 - 02:17:43 PM
 */
class DialectExtension_BelongsMany
        extends DataExtension {

    private static $belongs_many_many = array(
        'Dialects' => 'EtymologyDialect',
    );

    public function extraTabs(&$lists) {
        $dialects = $this->owner->Dialects();
        if ($dialects->Count()) {
            $wordList = new ArrayList();
            foreach ($dialects as $d) {
                $wordList->merge($d->Words());
            }


            $lists[] = array(
                'Title' => _t('Etymology.DIALECTS', 'Dialects'),
                'Content' => $this->owner
                        ->customise(array(
                            'Results' => $wordList
                        ))
                        ->renderWith('List_Grid')
            );
        }
    }

    public function updateCMSFields(FieldList $fields) {
        $field = $fields->fieldByName('Root.Dialects.Dialects');
        if ($field != null) {
//        $config = GridFieldConfig::create();
            $config = $field->getConfig();

            $config->removeComponentsByType('GridFieldAddExistingAutocompleter');
            $config->addComponent(new GridFieldAddExistingAutocompleter('buttons-before-right', array('Name')));

            $field->setConfig($config);
        }
    }

}
