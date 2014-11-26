<?php

namespace ride\library\import\provider\csv;

use ride\library\import\provider\DestinationProvider;
use ride\library\import\Importer;
use ride\library\system\file\browser\FileBrowser;
use ride\library\system\file\File;
use PHPExcel;

class XlsDestinationProvider extends CsvDestinationProvider {

    protected $xls;

    protected $fileBrowser;

    public function __construct(FileBrowser $fileBrowser, File $file) {
        parent::__construct($file);
        $this->fileBrowser = $fileBrowser;
    }

    public function preImport(Importer $importer) {
        parent::preImport($importer);
        $fs = $this->getFile()->getFileSystem();
        $xls = $this->fileBrowser->getApplicationDirectory()->getChild('data/destinationFiles/Actors.xls');
        var_dump($xls);
        $this->xls = $xls;
        var_dump($this->xls);
    }

    /**
     * Performs finishing tasks of the import
     * @return null
     */
    public function postImport() {
        parent::postImport();
        $path = $this->getFile()->getAbsolutePath();
        //set cache
        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

        //open csv file
        $objReader = new \PHPExcel_Reader_CSV();
        $objReader->setInputEncoding('utf8');
        $objPHPExcel = $objReader->load($path);
        $in_sheet = $objPHPExcel->getActiveSheet();

        //open excel file
        $objPHPExcel = new PHPExcel();
        $out_sheet = $objPHPExcel->getActiveSheet();

        //row index start from 1
        $row_index = 0;
        foreach ($in_sheet->getRowIterator() as $row) {
            $row_index++;
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            //column index start from 0
            $column_index = -1;
            foreach ($cellIterator as $cell) {
                $column_index++;
                $out_sheet->setCellValueByColumnAndRow($column_index, $row_index, $cell->getValue());
            }
        }

        //write excel file
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($this->xls);
    }

    /**
     * Sets the column names for the first row of the output
     * @param array $columnNames Value of getColumnNames of the source provider
     * @return null
     */
    public function setColumnNames(array $columnNames) {
        parent::setColumnNames($columnNames);
    }

    /**
     * Imports a row into this destination
     * @param array $row Array with the name of the column as key and the
     * value to import as value
     * @return null
     */
    public function setRow(array $row) {
        parent::setRow($row);
    }
}
