<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Reflection;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Parser\Reflection\AbstractReflection;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;
use ApiGen\Tests\Parser\Reflection\ReflectionMethodSource\SomeClassWithAnnotations;

final class AbstractReflectionTest extends AbstractContainerAwareTestCase
{
    /**
     * @var AbstractReflection|ClassReflectionInterface
     */
    private $reflectionClass;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parserStorage = $parser->parseDirectories([__DIR__ . '/ReflectionMethodSource']);

        $this->reflectionClass = $parserStorage->getClasses()[SomeClassWithAnnotations::class];

        /** @var ParserStorageInterface $parserStorage */
        $parserStorage = $this->container->getByType(ParserStorageInterface::class);
        $parserStorage->setClasses([
            SomeClassWithAnnotations::class => $this->reflectionClass
        ]);
    }

    public function testGetName(): void
    {
        $this->assertSame(SomeClassWithAnnotations::class, $this->reflectionClass->getName());
    }

    public function testGetFileName(): void
    {
        $this->assertStringEndsWith('ReflectionMethod.php', $this->reflectionClass->getFileName());
    }

    public function testGetStartLine(): void
    {
        $this->assertSame(23, $this->reflectionClass->getStartLine());
    }

    public function testGetEndLine(): void
    {
        $this->assertSame(40, $this->reflectionClass->getEndLine());
    }

    public function testGetParsedClasses(): void
    {
        $parsedClasses = MethodInvoker::callMethodOnObject($this->reflectionClass, 'getParsedClasses');
        $this->assertCount(1, $parsedClasses);
    }
}
