<?php

namespace Behat\Borg\ComposerPackage;

use Behat\Borg\Package\Exception\BadPackageNameGiven;
use Behat\Borg\Package\Package;

/**
 * composer.json-based package.
 */
final class ComposerPackage implements Package
{
    const PACKAGE_NAME_REGEX = '#^[A-Za-z0-9][A-Za-z0-9_.-]*/[A-Za-z0-9][A-Za-z0-9_.-]*$#u';

    /**
     * @var string
     */
    private $string;

    /**
     * Initializes package.
     *
     * @param string $string
     */
    public function __construct($string)
    {
        if (1 !== preg_match(self::PACKAGE_NAME_REGEX, $string)) {
            throw new BadPackageNameGiven(
                "Composer package name should match `" . self::PACKAGE_NAME_REGEX . "`, but `{$string}` given."
            );
        }

        $this->string = strtolower($string);
    }

    /**
     * {@inheritdoc]
     */
    public function getOrganisationName()
    {
        return explode('/', $this->string)[0];
    }

    /**
     * {@inheritdoc]
     */
    public function getName()
    {
        return explode('/', $this->string)[1];
    }

    /**
     * {@inheritdoc]
     */
    public function __toString()
    {
        return $this->string;
    }
}
