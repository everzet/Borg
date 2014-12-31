<?php

namespace spec\Behat\Borg\Documentation\Publisher;

use Behat\Borg\Documentation\Builder\BuiltDocumentation;
use Behat\Borg\Documentation\DocumentationId;
use Behat\Borg\Documentation\Page\Page;
use Behat\Borg\Documentation\Page\PageId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PublishedDocumentationSpec extends ObjectBehavior
{
    function let(
        BuiltDocumentation $builtDocumentation,
        DocumentationId $anId,
        \DateTimeImmutable $buildTime,
        \DateTimeImmutable $docTime
    ) {
        $builtDocumentation->getId()->willReturn($anId);
        $builtDocumentation->getBuildTime()->willReturn($buildTime);
        $builtDocumentation->getDocumentationTime()->willReturn($docTime);

        $this->beConstructedThrough('publish', [$builtDocumentation, __DIR__]);
    }

    function it_has_the_same_id_as_built_documentation(DocumentationId $anId)
    {
        $this->getId()->shouldReturn($anId);
    }

    function it_has_the_same_build_time_as_built_documentation(\DateTimeImmutable $buildTime)
    {
        $this->getBuildTime()->shouldReturn($buildTime);
    }

    function it_has_the_same_documentation_time_as_built_documentation(\DateTimeImmutable $docTime)
    {
        $this->getDocumentationTime()->shouldReturn($docTime);
    }

    function it_can_tell_if_it_contains_a_page(DocumentationId $anId)
    {
        $this->shouldHavePage(new PageId($anId->getWrappedObject(), basename(__FILE__)));
        $this->shouldNotHavePage(new PageId($anId->getWrappedObject(), 'any file'));
    }

    function it_can_get_page_by_its_id(DocumentationId $anId)
    {
        $pageId = new PageId($anId->getWrappedObject(), basename(__FILE__));

        $this->getPage($pageId)->shouldBeLike(new Page($this->getWrappedObject(), $pageId));
    }

    function it_throws_an_exception_when_trying_to_get_inexistent_page(DocumentationId $anId)
    {
        $pageId = new PageId($anId->getWrappedObject(), 'any file');

        $this->shouldThrow()->duringGetPage($pageId);
    }

    function it_can_provide_absolute_path_to_provided_page(DocumentationId $anId)
    {
        $pageId = new PageId($anId->getWrappedObject(), basename(__FILE__));

        $this->getPagePath($pageId)->shouldReturn(__FILE__);
    }

    function it_throws_an_exception_when_trying_to_get_path_for_inexistent_page(DocumentationId $anId)
    {
        $pageId = new PageId($anId->getWrappedObject(), 'any file');

        $this->shouldThrow()->duringGetPagePath($pageId);
    }
}
