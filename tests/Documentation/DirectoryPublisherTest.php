<?php

namespace tests\Behat\Borg\Documentation;

use Behat\Borg\Documentation\Builder\BuiltDocumentation;
use Behat\Borg\Documentation\DocumentationId;
use Behat\Borg\Documentation\Publisher\DirectoryPublisher;
use Behat\Borg\Documentation\Publisher\PublishedDocumentation;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Filesystem\Filesystem;

class DirectoryPublisherTest extends PHPUnit_Framework_TestCase
{
    private $tempBuildPath;
    private $tempPublishPath;
    /**
     * @var DirectoryPublisher
     */
    private $publisher;

    protected function setUp()
    {
        $this->tempBuildPath = getenv('TEST_TEMP_PATH') . '/github/publisher/build';
        $this->tempPublishPath = getenv('TEST_TEMP_PATH') . '/github/publisher/publish';
        $this->publisher = new DirectoryPublisher($this->tempPublishPath);

        (new Filesystem())->remove([$this->tempBuildPath, $this->tempPublishPath]);
    }

    /** @test */
    function it_publishes_documentation_by_moving_it_to_appropriate_folder()
    {
        $anId = $this->getMock(DocumentationId::class);
        $anId->method('__toString')->willReturn('built_doc');
        $builtDoc = $this->getMock(BuiltDocumentation::class);
        $builtDoc->method('getDocumentationId')->willReturn($anId);
        $builtDoc->method('getBuildPath')->willReturn($this->tempBuildPath . '/built_doc');

        (new Filesystem())->mkdir($this->tempBuildPath . '/built_doc');
        (new Filesystem())->touch($this->tempBuildPath . '/built_doc/my_file');

        $publishedDoc = $this->publisher->publish($builtDoc);

        $this->assertEquals(
            PublishedDocumentation::publish($builtDoc, $this->tempPublishPath . '/built_doc'),
            $publishedDoc
        );
        $this->assertFileExists($this->tempPublishPath . '/built_doc/my_file');
        $this->assertEquals($publishedDoc, unserialize(file_get_contents(
            $this->tempPublishPath . '/built_doc/publish.meta'
        )));
    }

    /** @test */
    function it_can_check_if_documentation_was_published()
    {
        $anId = $this->getMock(DocumentationId::class);
        $anId->method('__toString')->willReturn('my_doc');
        $builtDoc = $this->getMock(BuiltDocumentation::class);
        $publishedDoc = PublishedDocumentation::publish($builtDoc, $this->tempPublishPath . '/my_doc');

        (new Filesystem())->mkdir($this->tempPublishPath . '/my_doc');
        file_put_contents($this->tempPublishPath . '/my_doc/publish.meta', serialize($publishedDoc));

        $this->assertTrue($this->publisher->hasPublished($anId));
        $this->assertFalse($this->publisher->hasPublished($this->getMock(DocumentationId::class)));
    }

    /** @test */
    function it_can_get_published_documentation()
    {
        $anId = $this->getMock(DocumentationId::class);
        $anId->method('__toString')->willReturn('my_doc');
        $builtDoc = $this->getMock(BuiltDocumentation::class);
        $publishedDoc = PublishedDocumentation::publish($builtDoc, $this->tempPublishPath . '/my_doc');

        (new Filesystem())->mkdir($this->tempPublishPath . '/my_doc');
        file_put_contents(
            $this->tempPublishPath . '/my_doc/publish.meta', serialize($publishedDoc)
        );

        $this->assertEquals($publishedDoc, $this->publisher->getPublished($anId));
    }

    /**
     * @test
     * @expectedException \Behat\Borg\Documentation\Exception\RequestedDocumentationWasNotPublished
     */
    function it_throws_an_exception_when_trying_to_get_unpublished_documentation()
    {
        $this->publisher->getPublished($this->getMock(DocumentationId::class));
    }
}
