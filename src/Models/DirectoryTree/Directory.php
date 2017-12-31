<?php

namespace DarkWebDesign\DoctrineUnitTesting\Models\DirectoryTree;

/**
 * @Entity
 */
class Directory extends AbstractContentItem
{
    /**
     * @Column(type="string")
     */
    protected $path;

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }
}
