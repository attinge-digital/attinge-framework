<?php

namespace Attinge\Framework\Tests;

use Attinge\Framework\Container\Container;
use Attinge\Framework\Container\Exception\ContainerException;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function test_a_service_can_be_retrieved_from_the_container()
    {
        $container = new Container();
        $container->add('service', DependantClass::class);

        $this->assertInstanceOf(DependantClass::class, $container->get('service'));
    }
    public function test_a_ContainerException_is_thrown_if_a_service_cannot_be_found()
    {
        $container = new Container();
        $this->expectException(ContainerException::class);
        $container->add('foobar');
    }
    public function test_can_check_if_the_container_has_a_service() : void
    {
        $container = new Container();
        $container->add('dependant-class', DependantClass::class);

        $this->assertTrue($container->has('dependant-class'));
        $this->assertFalse($container->has('non-existent-class'));
    }
    public function test_can_add_an_instance_of_a_service_to_the_container() : void
    {
        $container = new Container();
        $container->add('dependant-service', DependantClass::class);
        $dependantService = $container->get('dependant-service');

        $this->assertInstanceOf(DependantClass::class, $dependantService);
    }

    public function test_services_can_be_recursively_autowired()
    {
        $container = new Container();

        $dependantService  = $container->get(DependantClass::class);
        $dependancyService = $dependantService->getDependency();

        $this->assertInstanceOf(DependencyClass::class, $dependancyService);
        $this->assertInstanceOf(SubDependencyClass::class, $dependancyService->getSubDependency());
    }
}