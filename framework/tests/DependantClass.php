<?php

namespace Attinge\Framework\Tests;

class DependantClass
{
    public function __construct(private readonly DependencyClass $dependency) {}
    public function getDependency() : DependencyClass
    {
        return $this->dependency;
    }
}