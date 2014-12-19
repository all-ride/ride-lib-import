<?php

namespace ride\library\import\provider\xls;

use ride\library\import\Importer;
use ride\library\import\provider\DestinationProvider;

use PHPExcel;
use PHPExcel_Writer_Excel2007;

/*
 * Destination provider for the XLS file type.
 * This Provider uses the PHPExcel library to produce an XLS file.
 */

class XlsDestinationProvider extends AbstractXlsProvider implements DestinationProvider {

    /**
     * Performs preparation tasks of the import
     * @param \ride\library\import\Importer $importer
     * @return null
     */
    public function preImport(Importer $importer) {
        $excel = new PHPExcel;
        $this->setExcel($excel);
        $this->setRowNumber(1);
        if ($this->columnNames) {
            $this->setRow($this->columnNames);
        }
    }

    /**
     * Imports a row into this destination
     * @param array $row Array with the name of the column as key and the value to import as value
     * @return null
     */
    public function setRow(array $row) {
        $rowNumber = $this->getRowNumber();
        $excel = $this->getExcel();
        $sheet = $excel->getSheet();
        $col = 0;

        foreach ($row as $index => $data) {
            $sheet->setCellValueByColumnAndRow($col, $rowNumber, $data);
            $col++;
        }

        $rowNumber++;
        $this->setRowNumber($rowNumber);
    }

    /**
     * Performs finishing tasks of the import
     * Writes the PHPExcel object to a file.
     * @return null
     */
    public function postImport() {
        $excel = $this->getExcel();
        $writer = new PHPExcel_Writer_Excel2007($excel);
        $fs = $this->system->getFileSystem();
        $file = $fs->getTemporaryFile();
        $writer->save($file->getAbsolutePath());
        $this->setFile($file);
    }
}
