<?php

namespace ride\library\import;

/**
 * Interface for a importer
 */
interface Importer {

    /**
     * Performs the import
     * @return null
     */
    public function import();

}
