<?php

declare(strict_types=1);

namespace DarkWebDesign\DoctrineUnitTesting\Mapping;

use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use DarkWebDesign\DoctrineUnitTesting\VerifyDeprecations;
use PHPUnit\Framework\TestCase;
use const CASE_LOWER;

final class UnderscoreNamingStrategyTest extends TestCase
{
    use VerifyDeprecations;

    /** @test */
    public function checkDeprecationMessage() : void
    {
        $this->expectDeprecationMessage('Creating Doctrine\ORM\Mapping\UnderscoreNamingStrategy without making it number aware is deprecated and will be removed in Doctrine ORM 3.0.');
        new UnderscoreNamingStrategy(CASE_LOWER, false);
    }
}
