<?php

namespace ride\library\import\mapper;

use ride\library\decorator\Decorator;
use ride\library\import\exception\ImportException;
use ride\library\import\provider\DestinationProvider;
use ride\library\import\provider\SourceProvider;
use ride\library\import\Importer;

/**
 * Interface to map rows from the source provider to the destination provider
 */
class GenericMapper implements Mapper {

    /**
     * Map with the destination column as key and the source column(s) as value
     * @var array
     */
    protected $columnMap = array();

    /**
     * Map with the destination column name as key and the glue between columns
     * as value
     * @var array
     */
    protected $glueMap = array();

    /**
     * Map with the destination column name as key and a array with decorator
     * instances as value
     * @var array
     */
    protected $decoratorMap = array();

    /**
     * Flag to see if empty values should be glued
     * @var boolean
     */
    protected $willGlueEmpty = false;

    /**
     * Sets the flag to glue empty values
     * @param boolean $willGlueEmpty
     * @return null
     */
    public function setWillGlueEmpty($willGlueEmpty) {
        $this->willGlueEmpty = $willGlueEmpty;
    }

    /**
     * Maps a column from the source provider to the destination provider
     * @param string|array $sourceColumn Names of the source columns
     * @param string $destinationColumn Name of the destination column
     * @param string|array $glue Glue(s) between the multiple source values
     * @return null
     */
    public function mapColumn($sourceColumns, $destinationColumn, $glue = ' ') {
        if (!is_array($sourceColumns)) {
            $sourceColumns = array($sourceColumns);
        }

        foreach ($sourceColumns as $sourceColumn) {
            $this->validateColumnName($sourceColumn);
        }
        $this->validateColumnName($destinationColumn);

        $this->columnMap[$destinationColumn] = $sourceColumns;
        $this->glueMap[$destinationColumn] = $glue;
    }

    /**
     * Unmaps a column
     * @param string $destinationColumn Name of the destination column
     * @return boolean True when the columns has been unmapped, false it was not
     * mapped
     */
    public function unmapColumn($destinationColumn) {
        if (!isset($this->columnMap[$destinationColumn])) {
            return false;
        }

        unset($this->columnMap[$destinationColumn]);
        unset($this->glueMap[$destinationColumn]);

        return true;
    }

    /**
     * Maps all column which have the same name in the source and destination
     * @param \ride\library\orm\provider\SourceProvider $sourceProvider
     * @param \ride\library\orm\provider\DestinationProvider $destinationProvider
     * @return null
     */
    public function mapColumns(SourceProvider $sourceProvider, DestinationProvider $destinationProvider) {
        $sourceColumns = $sourceProvider->getColumnNames();
        $destinationColumns = $destinationProvider->getColumnNames();

        foreach ($sourceColumns as $sourceColumnName) {
            if (isset($destinationColumns[$sourceColumnName])) {
                $this->mapColumn($sourceColumnName, $sourceColumnName);
            }
        }
    }

    /**
     * Adds a decorator for the provided destination column
     * @param string $destinationColumn Name of the destination column
     * @param \ride\library\decorator\Decorator $decorator Decorator to apply
     * when mapping the rows
     * @return null
     */
    public function addDecorator($destinationColumn, Decorator $decorator) {
        $this->validateColumnName($destinationColumn);

        if (!isset($this->decoratorMap[$destinationColumn])) {
            $this->decoratorMap[$destinationColumn] = array();
        }

        $this->decoratorMap[$destinationColumn][] = $decorator;
    }

    /**
     * Validates a column name
     * @param string $columnName
     * @return null
     * @throws \ride\library\import\exception\ImportException
     */
    protected function validateColumnName($columnName) {
        if ((!is_string($columnName) && !is_numeric($columnName)) || $columnName < 0 || $columnName === '') {
            throw new ImportException('Could not map column: provided column name is invalid');
        }
    }

    /**
     * Performs preparation tasks for the importer
     * @param \ride\library\import\Importer $importer
     * @return null
     */
    public function preImport(Importer $importer) {
        $sourceProvider = $importer->getSourceProvider();
        $destinationProvider = $importer->getDestinationProvider();

        $this->validateMapping($sourceProvider, $destinationProvider);
    }

    /**
     * Validates the set mapping on the providers
     * @param \ride\library\import\provider\SourceProvider $sourceProvider
     * @param \ride\library\import\provider\DestinationProvider $destinationProvider
     * @return null
     * @throws \ride\library\import\exception\ImportException when the mapping
     * is invalid
     */
    protected function validateMapping(SourceProvider $sourceProvider, DestinationProvider $destinationProvider) {
        $sourceColumns = $sourceProvider->getColumnNames();
        $destinationColumns = $destinationProvider->getColumnNames();

        foreach ($this->columnMap as $mapDestinationColumn => $mapSourceColumns) {
            if (!isset($destinationColumns[$mapDestinationColumn])) {
                throw new ImportException('Could not map to ' . $mapDestinationColumn . ': destination column does not exist');
            }

            foreach ($mapSourceColumns as $mapSourceColumn) {
                if (!isset($sourceColumns[$mapSourceColumn])) {
                    throw new ImportException('Could not map from ' . $mapSourceColumn . ': source column does not exist');
                }
            }
        }
    }

    /**
     * Maps the columns from the source row to the destination row
     * @param array $sourceRow Row from the source provider
     * @param array $destinationRow Current state of the row for the destination
     * provider
     * @return array Row for the destination provider after the mapping of this
     * instance
     */
    public function mapRow(array $sourceRow, array $destinationRow) {
        foreach ($this->columnMap as $destinationColumnName => $sourceColumnNames) {
            $value = null;

            // retrieve the value from the source row
            $isFirstColumn = true;
            foreach ($sourceColumnNames as $index => $sourceColumnName) {
                if (!array_key_exists($sourceColumnName, $sourceRow)) {
                    // column not set, ignoring
                    continue;
                }

                if ($isFirstColumn) {
                    // first value for the column, assign it
                    $value = $sourceRow[$sourceColumnName];

                    $isFirstColumn = false;
                } else {
                    // additional value, glue together
                    if (!$this->willGlueEmpty && empty($sourceRow[$sourceColumnName])) {
                        continue;
                    }

                    $glue = $this->glueMap[$destinationColumnName];
                    if (is_array($glue)) {
                        if (isset($glue[$index - 1])) {
                            $glue = $glue[$index - 1];
                        } else {
                            $glue = $glue[0];
                        }
                    }

                    if ($this->willGlueEmpty || '' . $sourceRow[$sourceColumnName] != '') {
                        $value .= $glue . $sourceRow[$sourceColumnName];
                    }
                }
            }

            // apply the decorators for the column
            if (isset($this->decoratorMap[$destinationColumnName])) {
                foreach ($this->decoratorMap[$destinationColumnName] as $decorator) {
                    $value = $decorator->decorate($value);
                }
            }

            // update the value in the destination row
            $destinationRow[$destinationColumnName] = $value;
        }

        return $destinationRow;
    }

}
