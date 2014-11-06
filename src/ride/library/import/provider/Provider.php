<?php

namespace ride\library\import\provider;

use ride\library\import\Importer;

/**
 * Interface for a provider
 */
interface Provider {

    /**
     * Gets the available column names for this provider
     * @return array Array with the name of the column as key and as value
     */
    public function getColumnNames();

    /**
     * Performs preparation tasks of the import
     * @return null
     */
    public function preImport(Importer $importer);

    /**
     * Performs finishing tasks of the import
     * @return null
     */
    public function postImport();

}
