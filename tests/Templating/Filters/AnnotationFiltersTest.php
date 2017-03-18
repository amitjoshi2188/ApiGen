<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Templating\Filters\AnnotationFilters;
use Mockery;
use PHPUnit\Framework\TestCase;

class AnnotationFiltersTest extends TestCase
{

    /**
     * @var AnnotationFilters
     */
    private $annotationFilters;


    protected function setUp(): void
    {
        $configurationMock = $this->createMock(Configuration::class);
        $configurationMock->method('getOption')->with(CO::INTERNAL)->willReturn(false);
        $elementResolverMock = $this->createMock('ApiGen\Generator\Resolvers\ElementResolver');
        $this->annotationFilters = new AnnotationFilters($configurationMock, $elementResolverMock);
    }


    public function testAnnotationBeautify(): void
    {
        $this->assertSame('Used by', $this->annotationFilters->annotationBeautify('usedby'));
        $this->assertSame('Method', $this->annotationFilters->annotationBeautify('method'));
    }


    public function testAnnotationFilter(): void
    {
        $annotations = ['method' => true, 'remain' => true, 'internal' => true];
        $this->assertSame(
            ['remain' => true],
            $this->annotationFilters->annotationFilter($annotations)
        );
    }


    public function testAnnotationFilterWithCustom(): void
    {
        $annotations = ['remain' => true, 'otherToRemain' => true];
        $this->assertSame(
            ['otherToRemain' => true],
            $this->annotationFilters->annotationFilter($annotations, ['remain'])
        );
    }
}
