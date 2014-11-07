<?php

namespace ride\library\import;

/**
 * Chain implementation of the importer
 */
class ChainImporter implements Importer {

    /**
     * Array with the importers to chain
     * @var array
     */
    protected $importers = array();

    /**
     * Adds a importer to the chain
     * @param Importer $importer
     * @return null
     */
    public function addImporter(Importer $importer) {
        $this->importers[] = $importer;
    }

    /**
     * Performs the import
     * @return null
     */
    public function import() {
        $this->preImport();

        foreach ($this->importers as $importer) {
            $importer->import();
        }

        $this->postImport();
    }

    /**
     * Hook before importing
     * @return null
     */
    protected function preImport() {

    }

    /**
     * Hook after importing
     * @return null
     */
    protected function postImport() {

    }

}
