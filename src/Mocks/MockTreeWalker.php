<?php

namespace DarkWebDesign\DoctrineUnitTesting\Mocks;

use Doctrine\ORM\Query\TreeWalkerAdapter;

/**
 * Mock class for TreeWalker.
 */
class MockTreeWalker extends TreeWalkerAdapter
{
    /**
     * {@inheritdoc}
     */
    public function getExecutor($AST)
    {
        return null;
    }
}
