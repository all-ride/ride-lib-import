<?php

namespace ride\library\import\provider\csv;

use ride\library\import\exception\ImportException;
use ride\library\import\provider\SourceProvider;
use ride\library\import\Importer;

/**
 * Source provider for a CSV file
 */
class CsvSourceProvider extends AbstractCsvProvider implements SourceProvider {

    /**
     * Reads the column names from the first row of the file
     * @return null
     */
    public function readColumnNames() {
        $this->openHandle();

        $row = $this->readRow();
        if (!$row) {
            throw new ImportException('Could not read the columns from the first row of ' . $this->file);
        }

        $this->columnNames = $row;
    }

    /**
     * Performs preparation tasks of the import
     * @param \ride\library\import\Importer $importer
     * @return null
     */
    public function preImport(Importer $importer) {
        $this->openHandle();
    }

    /**
     * Gets the next row from this destination
     * @return array|null $data Array with the name of the column as key and the
     * value to import as value. Null is returned when all rows are processed.
     */
    public function getRow() {
        if (!$this->handle) {
            throw new ImportException('Could not get row for source ' . $this->file . ': file is not opened, call preImport first');
        }

        $result = array();

        $row = $this->readRow();
        if ($row === null) {
            return $row;
        }

        foreach ($this->columnNames as $columnIndex => $columnName) {
            if (isset($row[$columnIndex])) {
                $result[$columnName] = $row[$columnIndex];
            } else {
                $result[$columnName] = null;
            }
        }

        return $result;
    }

    /**
     * Opens the handle to the file
     * @return null
     */
    protected function openHandle() {
        if ($this->handle) {
            return;
        }

        if (!$this->file->exists() || $this->file->isDirectory()) {
            throw new ImportException('Could not open the file of the source provider: ' . $this->file . ' does not exist or is a directory');
        }

        $this->handle = fopen($this->file->getAbsolutePath(), 'r');
        if ($this->handle === false) {
            throw new ImportException('Could not open ' . $this->file . ' for reading');
        }
    }

    /**
     * Reads the next line
     * @return array|null Array with the index of the column as key and the value of
     * the column as value
     */
    protected function readRow() {
        $row = fgetcsv($this->handle, 0, $this->delimiter, $this->enclosure, $this->escape);
        if ($row === false) {
            return null;
        }

        if ($row === array(null)) {
            return $this->readRow();
        }

        return $row;
    }

}
