<?php

namespace ride\library\import\provider;

/**
 * Interface for the destination of an import
 */
interface DestinationProvider extends Provider {

    /**
     * Imports a row into this destination
     * @param array $row Array with the name of the column as key and the
     * value to import as value
     */
    public function setRow(array $row);
}
