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
     * Sets the column names for the first row of the output
     * @param array $columnNames Value of getColumnNames of the source provider
     * @return null
     */
    public function setColumnNames(array $columnNames) {
        $this->columnNames = $columnNames;
    }

    /**
     * Performs preparation tasks of the import
     * @param \ride\library\import\Importer $importer
     * @return null
     */
    public function preImport(Importer $importer) {
        if ($this->handle) {
            return;
        }

        $file = $this->getFile();
        $file->getParent()->create();

        $this->handle = fopen($file->getAbsolutePath(), 'w');
        if ($this->handle === false) {
            throw new ImportException('Could not open ' . $file . ' for writing');
        }

        if ($this->columnNames) {
            $this->writeRow($this->columnNames);
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
            throw new ImportException('Could not set row to destination ' . $this->getFile() . ': file is not opened, call prepareImport first');
        }

        // fill rows
        $data = array(array());

        foreach ($this->columnNames as $columnIndex => $columnName) {
            $rowIndex = 0;

            if (!isset($row[$columnName])) {
                continue;
            }

            if (is_array($row[$columnName])) {
                do {
                    $data[$rowIndex][$columnIndex] = array_shift($row[$columnName]);
                    $rowIndex++;
                } while ($row[$columnName]);
            } else {
                $data[$rowIndex][$columnIndex] = $row[$columnName];
            }
        }

        // normalize rows
        foreach ($data as $rowIndex => $columns) {
            foreach ($this->columnNames as $columnIndex => $columnName) {
                if (!isset($columns[$columnIndex])) {
                    $data[$rowIndex][$columnIndex] = null;
                }
            }
        }

        // write rows
        foreach ($data as $row) {
            $this->writeRow($row);
        }
    }

    /**
     * Writes a row to the file
     * @param array $data Data of the row
     * @return null
     */
    protected function writeRow(array $data) {
        if (fputcsv($this->handle, $data, $this->delimiter, $this->enclosure) === false) {
            throw new ImportException('Could not write row to destination ' . $this->getFile() . ': CSV input error');
        }
    }

}
