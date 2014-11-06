# Ride: Import Library

Import library of the PHP Ride framework.

This module can perform imports from one source to another.

## Code Sample

Check this code sample to see the possibilities of this library:

    <?php

    use ride\library\decorator\BooleanDecorator;
    use ride\library\import\mapper\GenericMapper;
    use ride\library\import\provider\csv\CsvSourceProvider;
    use ride\library\import\provider\csv\CsvDestinationProvider;
    use ride\library\import\GenericImporter;
    use ride\library\system\file\File;

    function importFile(File $sourceFile, File $destinationFile) {
        // initialize source provider
        $sourceProvider = new CsvSourceProvider($sourceFile);
        // column index 0 shall be called name
        $sourceProvider->setColumnName(0, 'name');
        // column index 1 shall be called firstname
        $sourceProvider->setColumnName(1, 'firstname');
        $sourceProvider->setColumnName(2, 'street');
        $sourceProvider->setColumnName(3, 'number');
        $sourceProvider->setColumnName(4, 'box');
        $sourceProvider->setColumnName(5, 'postalCode');
        $sourceProvider->setColumnName(6, 'city');
        $sourceProvider->setColumnName(7, 'subscribe_newsletter');
        // if the first row has the column names, you can use those instead of mapping everything manually
        $sourceProvider->readColumnNames();

        // initialize destination provider
        $destinationProvider = new CsvDestinationProvider($destinationFile);
        // column index 0 shall be called fullName
        $destinationProvider->setColumnName(0, 'fullName');
        $destinationProvider->setColumnName(1, 'address');
        $destinationProvider->setColumnName(3, 'postalCode');
        $destinationProvider->setColumnName(4, 'city');
        $destinationProvider->setColumnName(5, 'isNewsletter');

        // create a mapping to translate values from the source to the destination
        $mapper = new GenericMapper();
        $mapper->mapColumn(array('name', 'firstname'), 'fullName');
        // glue street, number and box together; use a space between street and number, then use a slash to add the box
        $mapper->mapColumn(array('street', 'number', 'box'), 'address', array(' ', '/'));
        $mapper->mapColumn('postalCode', 'postalCode');
        $mapper->mapColumn('city', 'city');
        $mapper->mapColumn('subscribe_newsletter', 'isNewsletter');
        // you can add decorators to process the resulting value
        $mapper->addDecorator('isNewsletter', new BooleanDecorator());

        // initialize importer with providers and mapper
        $importer = new GenericImporter();
        $importer->setSourceProvider($sourceProvider);
        $importer->setDestinationProvider($destinationProvider);
        $importer->addMapper($mapper);

        $importer->import();
    }
