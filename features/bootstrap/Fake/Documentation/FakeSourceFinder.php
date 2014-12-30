<?php

namespace Fake\Documentation;

use Behat\Borg\Documentation\Source;
use Behat\Borg\Documentation\Finder\SourceFinder;
use Behat\Borg\Package\Downloader\Download;
use Behat\Borg\Package\Release;

final class FakeSourceFinder implements SourceFinder
{
    private $source = [];

    public function releaseWasDocumented(Release $release, Source $source)
    {
        $this->source[(string)$release] = $source;
    }

    public function findSource(Download $download)
    {
        if (!isset($this->source[(string)$download->getRelease()])) {
            return null;
        }

        return $this->source[(string)$download->getRelease()];
    }
}
