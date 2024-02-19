<?php

namespace Attinge\Framework\Tests;

class DependencyClass
{
    public function __construct(private readonly SubDependencyClass $subDependency) {}

    public function getSubDependency() : SubDependencyClass
    {
        return $this->subDependency;
    }
}