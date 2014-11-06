<?php

namespace ride\library\import\mapper;

use ride\library\import\Importer;

/**
 * Interface to map rows from the source provider to the destination provider
 */
interface Mapper {

    /**
     * Maps the columns from the source row to the destination row
     * @param array $sourceRow Row from the source provider
     * @param array $destinationRow Current state of the row for the destination
     * provider
     * @return array Row for the destination provider after the mapping of this
     * instance
     */
    public function mapRow(array $sourceRow, array $destinationRow);

    /**
     * Performs preparation tasks for the importer
     * @param \ride\library\import\Importer $importer
     * @return null
     */
    public function preImport(Importer $importer);

}
