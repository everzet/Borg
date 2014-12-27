<?php

namespace Behat\Borg\Documentation\Finder;

use Behat\Borg\Documentation\DocumentationSource;
use Behat\Borg\Package\Downloader\Download;

/**
 * Finds documentation source in provided downloaded release.
 */
interface DocumentationSourceFinder
{
    /**
     * Tries to find documentation source in provided download.
     *
     * @param Download $download
     *
     * @return null|DocumentationSource
     */
    public function findDocumentationSource(Download $download);
}
