<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Twig;

use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Twig\EasyAdminTwigExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\AssetMapper\ImportMap\ImportMapRenderer;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Icons\Twig\UXIconRuntime;

class EasyAdminTwigExtensionTest extends KernelTestCase
{
    /**
     * @dataProvider provideValuesForRepresentAsString
     */
    public function testRepresentAsString($value, $expectedValue, bool $assertRegex = false, string|callable|null $toStringMethod = null): void
    {
        $translator = $this->getMockBuilder(TranslatorInterface::class)->disableOriginalConstructor()->getMock();
        $translator->method('trans')->willReturnCallback(fn ($value) => '*'.$value);

        $extension = new EasyAdminTwigExtension(
            $this->getMockBuilder(ServiceLocator::class)->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder(AdminContextProvider::class)->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder(CsrfTokenManagerInterface::class)->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder(ImportMapRenderer::class)->disableOriginalConstructor()->getMock(),
            $translator,
            $this->getMockBuilder(UXIconRuntime::class)->disableOriginalConstructor()->getMock(),
        );

        $result = $extension->representAsString($value, $toStringMethod);

        if ($assertRegex) {
            $this->assertMatchesRegularExpression($expectedValue, $result);
        } else {
            $this->assertSame($expectedValue, $result);
        }
    }

    public function testRepresentAsStringExcepion()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/The method "someMethod\(\)" does not exist or is not callable in the value of type "class@anonymous.*"/');

        $extension = new EasyAdminTwigExtension(
            $this->getMockBuilder(ServiceLocator::class)->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder(AdminContextProvider::class)->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder(CsrfTokenManagerInterface::class)->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder(ImportMapRenderer::class)->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder(TranslatorInterface::class)->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder(UXIconRuntime::class)->disableOriginalConstructor()->getMock(),
        );

        $extension->representAsString(new class {}, 'someMethod');
    }

    public function provideValuesForRepresentAsString()
    {
        yield [null, ''];
        yield ['foo bar', 'foo bar'];
        yield [5, '5'];
        yield [3.14, '3.14'];
        yield [true, 'true'];
        yield [false, 'false'];
        yield [[1, 2, 3], 'Array (3 items)'];
        yield [new class implements TranslatableInterface {
            public function trans(TranslatorInterface $translator, ?string $locale = null): string
            {
                return $translator->trans('some value');
            }
        }, '*some value'];
        yield [new class {}, '/class@anonymous.*/', true];
        yield [new class {
            public function __toString()
            {
                return 'foo bar';
            }
        }, 'foo bar'];
        yield [new class {
            public function getId()
            {
                return 1234;
            }
        }, '/class@anonymous.* #1234/', true];

        yield ['foo', 'foo bar', false, fn ($value) => $value.' bar'];
        yield [new class {
            public function someMethod()
            {
                return 'foo';
            }
        }, 'foo', false, 'someMethod'];
        yield ['foo', '*foo bar', false, fn ($value, $translator) => $translator->trans($value.' bar')];
    }
}
