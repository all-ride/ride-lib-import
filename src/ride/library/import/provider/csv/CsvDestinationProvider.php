<?php

namespace ride\library\import\provider\csv;

use ride\library\import\exception\ImportException;
use ride\library\import\provider\DestinationProvider;
use ride\library\import\Importer;

/**
 * Destination provider for a CSV file
 */
class CsvDestinationProvider extends AbstractCsvProvider implements DestinationProvider {

    /**
     * Performs preparation tasks of the import
     * @param \ride\library\import\Importer $importer
     * @return null
     */
    public function preImport(Importer $importer) {
        if ($this->handle) {
            return;
        }

        $this->file->getParent()->create();

        $this->handle = fopen($this->file->getAbsolutePath(), 'w');
        if ($this->handle === false) {
            throw new ImportException('Could not open ' . $this->file . ' for writing');
        }
    }

    /**
     * Imports a row into this destination
     * @param array $row Array with the name of the column as key and the
     * value to import as value
     * @return null
     */
    public function setRow(array $row) {
        if (!$this->handle) {
            throw new ImportException('Could not set row to destination ' . $this->file . ': file is not opened, call prepareImport first');
        }

        $data = array();

        foreach ($this->columnNames as $columnIndex => $columnName) {
            if (isset($row[$columnName])) {
                $data[$columnIndex] = $row[$columnName];
            } else {
                $data[$columnIndex] = null;
            }
        }

        if (fputcsv($this->handle, $data, $this->delimiter, $this->enclosure) === false) {
            throw new ImportException('Could not write row to destination ' . $this->file . ': CSV input error');
        }
    }

}
