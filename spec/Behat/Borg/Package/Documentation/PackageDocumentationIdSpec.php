<?php

namespace spec\Behat\Borg\Package\Documentation;

use Behat\Borg\Documentation\DocumentationId;
use Behat\Borg\Package\Package;
use Behat\Borg\Package\Version;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PackageDocumentationIdSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Package::fromName('behat'), Version::fromString('1.0.0'));
    }

    function it_is_documentation_identifier()
    {
        $this->shouldHaveType(DocumentationId::class);
    }
}
