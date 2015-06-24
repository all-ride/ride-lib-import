<?php

namespace ride\library\import\provider;

use ride\library\system\file\File;

/**
 * Interface for the destination of an import
 */
interface FileProvider extends Provider {

    /**
     * Sets the file
     * @param \ride\library\system\file\File $file
     * @return null
     */
    public function setFile(File $file);

    /**
     * Gets the file
     * @return \ride\library\system\file\File
     */
    public function getFile();

}
