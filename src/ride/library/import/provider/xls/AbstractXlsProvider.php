<?php

namespace ride\library\import\provider\xls;

use ride\library\import\provider\Provider;
use ride\library\system\System;
use PHPExcel;

/**
 * Abstract import provider for the CSV file format
 */
abstract class AbstractXlsProvider implements Provider {

    /**
     * Filename
     * @var Int
     */
    protected $rowNumber;

    /**
     * Filename
     * @var String
     */
    protected $fileName;

    /**
     * Instance of the CSV file
     * @var \ride\library\system\file\File
     */
    protected $file;

    /**
     * Instance of the PHPExcel Object
     * @ var \PHPExcel
     */
    protected $excel;

    /**
     * Handle of the open file
     * @var resource
     */
    protected $handle;

    /**
     * Column names in the file
     * @var array
     */
    protected $columnNames;

    /**
     * Column names in the file
     * @var \ride\library\system\System
     */
    protected $system;

    /**
     * Constructs a new source provider
     * @param \ride\library\system\file\File $file
     * @return null
     */
    public function __construct(System $system, $fileName) {
        $this->fileName = $fileName;
        $this->system = $system;
    }

    /**
     * Sets the file
     * @param \ride\library\system\file\File $file
     * @return null
     */
    public function setFile($file) {
        $this->file = $file;
        $this->closeFile();
    }

    /**
     * Gets the file
     * @return \ride\library\system\file\File
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * @return String
     */
    public function getFileName() {
        return $this->fileName;
    }

    /**
     * @param String $fileName
     */
    public function setFileName($fileName) {
        $this->fileName = $fileName;
    }

    /**
     * Sets the row number
     * @param string $row Number of the current row
     * @return Int
     */
    public function setRowNumber($rowNumber) {
        $this->rowNumber = $rowNumber;
    }

    /**
     * Gets the rowNumber
     * @return Int
     */
    public function getRowNumber() {
        return $this->rowNumber;
    }

    /**
     * @return PHPExcel
     */
    public function getExcel() {
        return $this->excel;
    }

    /**
     * @param PHPExcel $excel
     */
    public function setExcel($excel) {
        $this->excel = $excel;
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
        $this->closeFile();
    }

    /**
     * Closes the handle to the file if open
     * @return null
     */
    protected function closeFile() {
        if ($this->handle) {
            fclose($this->handle);
        }
    }

    public function getNextColumnNumber($col) {
        $chars = array('a-z');
    }
}
