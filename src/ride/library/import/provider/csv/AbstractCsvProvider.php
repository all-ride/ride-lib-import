<?php

namespace ride\library\import\provider\csv;

use ride\library\import\provider\Provider;
use ride\library\system\file\File;

/**
 * Abstract import provider for the CSV file format
 */
abstract class AbstractCsvProvider implements Provider {

    /**
     * Instance of the CSV file
     * @var \ride\library\system\file\File
     */
    protected $file;

    /**
     * Handle of the open file
     * @var resource
     */
    protected $handle;

    /**
     * Delimiter between columns
     * @var string
     */
    protected $delimiter;

    /**
     * Column enclosure character
     * @var string
     */
    protected $enclosure;

    /**
     * Escape character
     * @var string
     */
    protected $escape;

    /**
     * Column names in the file
     * @var array
     */
    protected $columnNames;

    /**
     * Constructs a new source provider
     * @param \ride\library\system\file\File $file
     * @return null
     */
    public function __construct(File $file) {
        $this->setFile($file);

        $this->delimiter = ',';
        $this->enclosure = '"';
        $this->escape = '\\';
    }

    /**
     * Sets the file
     * @param \ride\library\system\file\File $file
     * @return null
     */
    public function setFile(File $file) {
        $this->postImport();

        $this->file = $file;
        $this->columnNames = array();
    }

    /**
     * Gets the file
     * @return \ride\library\system\file\File
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * Sets the field delimiter
     * @param string $delimiter Delimiter between the columns (one character)
     * @return null
     */
    public function setDelimiter($delimiter) {
        $this->delimiter = $delimiter;
    }

    /**
     * Gets the field delimiter
     * @return string
     */
    public function getDelimiter() {
        return $this->delimiter;
    }

    /**
     * Sets the field enclosure
     * @param string $enclosure Enclosure of the columns (one character)
     * @return null
     */
    public function setEnclosure($enclosure) {
        $this->enclosure = $enclosure;
    }

    /**
     * Gets the field enclosure
     * @return  string
     */
    public function getEnclosure() {
        return $this->enclosure;
    }

    /**
     * Sets the escape character
     * @param string $escape Escape character for the enclosure
     * @return null
     */
    public function setEscape($escape) {
        $this->escape = $escape;
    }

    /**
     * Gets the escape character
     * @return string
     */
    public function getEscape() {
        return $this->escape;
    }

    /**
     * Maps a column number to a name
     * @var integer $columnIndex Index of the column, starting from 0
     * @var string $columnName Name for the column
     * @return null
     */
    public function setColumnName($columnIndex, $columnName) {
        $this->columnNames[$columnIndex] = $columnName;
    }

    /**
     * Gets the available columns for this provider
     * @return array Array with the name of the column as key and as value
     */
    public function getColumnNames() {
        $columns = array();

        foreach ($this->columnNames as $columnIndex => $columnName) {
            $columns[$columnName] = $columnName;
        }

        return $columns;
    }

    /**
     * Performs finishing tasks of the import
     * @return null
     */
    public function postImport() {
        if ($this->handle) {
            fclose($this->handle);
        }
    }

}
