<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
include_once "lib/resolver.php";

final class ResolverTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        global $config;
        $resolver = new \SCCP\Resolve\Resolver($config);
        $this->assertInstanceOf(
            \SCCP\Resolve\Resolver::class,
            $resolver
        );
    }
}

