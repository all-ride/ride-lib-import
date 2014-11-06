<?php

namespace ride\library\import;

use ride\library\import\exception\ImportException;
use ride\library\import\mapper\Mapper;
use ride\library\import\provider\DestinationProvider;
use ride\library\import\provider\SourceProvider;

/**
 * Generic implementation of the importer
 */
class GenericImporter implements Importer {

    /**
     * Instance of the sourceProvider
     * @var \ride\library\import\source\SourceProvider
     */
    protected $sourceProvider;

    /**
     * Instance of the destinationProvider
     * @var \ride\library\import\source\DestinationProvider
     */
    protected $destinationProvider;

    /**
     * Array with the mappers between source and destination
     * @var array
     */
    protected $mappers;

    /**
     * Sets the source provider of this importer
     * @param \ride\library\import\source\SourceProvider $sourceProvider
     * @return null
     */
    public function setSourceProvider(SourceProvider $sourceProvider) {
        $this->sourceProvider = $sourceProvider;
    }

    /**
     * Gets the source provider of this importer
     * @return \ride\library\import\source\SourceProvider
     */
    public function getSourceProvider() {
        return $this->sourceProvider;
    }

    /**
     * Sets the destination provider of this importer
     * @param \ride\library\import\destination\DestinationProvider $destinationProvider
     * @return null
     */
    public function setDestinationProvider(DestinationProvider $destinationProvider) {
        $this->destinationProvider = $destinationProvider;
    }

    /**
     * Gets the destination provider of this importer
     * @return \ride\library\import\destination\DestinationProvider
     */
    public function getDestinationProvider() {
        return $this->destinationProvider;
    }

    /**
     * Adds a mapper to translate columns from the source provider to the
     * destination provider
     * @param \ride\library\import\mapper\Mapper $mapper
     * @return null
     */
    public function addMapper(Mapper $mapper) {
        $this->mappers[] = $mapper;
    }

    /**
     * Performs the import
     * @return null
     */
    public function import() {
        if (!$this->sourceProvider) {
            throw new ImportException('Could not perform import: no source provider set');
        }
        if (!$this->destinationProvider) {
            throw new ImportException('Could not perform import: no destination provider set');
        }
        if (!$this->mappers) {
            throw new ImportException('Could not perform import: no mappers added');
        }

        foreach ($this->mappers as $mapper) {
            $mapper->preImport($this);
        }

        $this->sourceProvider->preImport($this);
        $this->destinationProvider->preImport($this);

        while ($sourceRow = $this->sourceProvider->getRow()) {
            $destinationRow = array();

            foreach ($this->mappers as $mapper) {
                $destinationRow = $mapper->mapRow($sourceRow, $destinationRow);
            }

            $this->destinationProvider->setRow($destinationRow);
        }

        $this->sourceProvider->postImport();
        $this->destinationProvider->postImport();
    }

}
