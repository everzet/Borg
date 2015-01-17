<?php

namespace Behat\Borg\Application\Infrastructure\Documentation;

use Behat\Borg\Documentation\DocumentationId;
use Behat\Borg\Documentation\Publisher\PublishedDocumentation;
use Behat\Borg\Documentation\Repository\Repository;
use Everzet\PersistedObjects\FileRepository;
use Everzet\PersistedObjects\InMemoryRepository;
use Everzet\PersistedObjects\ObjectIdentifier;

final class PersistedObjectsRepository implements Repository, ObjectIdentifier
{
    public function __construct($path)
    {
        $this->repo = $path ? new FileRepository($path, $this) : new InMemoryRepository($this);
    }

    public function save(PublishedDocumentation $documentation)
    {
        $this->repo->save($documentation);
    }

    public function find(DocumentationId $documentationId)
    {
        if ('current' === $documentationId->versionString()) {
            return current($this->findAll($documentationId->projectName()));
        }

        return $this->repo->findById((string)$documentationId);
    }

    public function findAll($projectName)
    {
        $documentation = array_filter(
            $this->repo->getAll(),
            function (PublishedDocumentation $documentation) use ($projectName) {
                return strtolower($projectName) == $documentation->documentationId()->projectName();
            }
        );

        usort($documentation, [$this, 'compareDocumentation']);

        return $documentation;
    }

    public function getIdentity($object)
    {
        return (string)$object->documentationId();
    }

    private function compareDocumentation(PublishedDocumentation $a, PublishedDocumentation $b)
    {
        $versionA = $a->documentationId()->versionString();
        $versionB = $b->documentationId()->versionString();

        if ($versionB == 'master' && $versionA == 'develop') {
            return 1;
        }
        if ($versionB == 'develop' && $versionA == 'master') {
            return -1;
        }

        return version_compare($versionB, $versionA);
    }
}
