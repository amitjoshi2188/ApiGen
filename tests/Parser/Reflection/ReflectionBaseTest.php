<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\ReflectionBase;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use ApiGen\Parser\Tests\MethodInvoker;
use Mockery;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use TokenReflection\Broker;

class ReflectionBaseTest extends TestCase
{

    /**
     * @var ReflectionBase
     */
    private $reflectionClass;


    protected function setUp(): void
    {
        $backend = new Backend($this->getReflectionFactory());
        $broker = new Broker($backend);
        $broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

        $this->reflectionClass = $backend->getClasses()['Project\ReflectionMethod'];
    }


    public function testGetName(): void
    {
        $this->assertSame('Project\ReflectionMethod', $this->reflectionClass->getName());
    }


    public function testGetPrettyName(): void
    {
        $this->assertSame('Project\ReflectionMethod', $this->reflectionClass->getPrettyName());
    }


    public function testIsInternal(): void
    {
        $this->assertFalse($this->reflectionClass->isInternal());
    }


    public function testIsTokenized(): void
    {
        $this->assertTrue($this->reflectionClass->isTokenized());
    }


    public function testGetFileName(): void
    {
        $this->assertStringEndsWith('ReflectionMethod.php', $this->reflectionClass->getFileName());
    }


    public function testGetStartLine(): void
    {
        $this->assertSame(10, $this->reflectionClass->getStartLine());
    }


    public function testGetEndLine(): void
    {
        $this->assertSame(42, $this->reflectionClass->getEndLine());
    }


    public function testGetParsedClasses(): void
    {
        $parsedClasses = MethodInvoker::callMethodOnObject($this->reflectionClass, 'getParsedClasses');
        $this->assertCount(1, $parsedClasses);
    }


    private function getReflectionFactory(): ReflectionFactoryInterface
    {
        $parserStorageMock = $this->createMock(ParserStorageInterface::class, [
            'getElementsByType' => ['...']
        ]);

        $configurationMock = $this->createMock(ConfigurationInterface::class, [
            'getVisibilityLevel' => ReflectionProperty::IS_PUBLIC,
            'isInternalDocumented' => false,
        ]);
        return new ReflectionFactory($configurationMock, $parserStorageMock);
    }
}
