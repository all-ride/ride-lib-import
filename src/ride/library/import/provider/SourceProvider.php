<?php

namespace ride\library\import\provider;

/**
 * Interface for the source of an import
 */
interface SourceProvider extends Provider {

    /**
     * Gets the next row from this destination
     * @return array|null $data Array with the name of the column as key and the
     * value to import as value. Null is returned when all rows are processed.
     */
    public function getRow();

}
