<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Twig;

use EasyCorp\Bundle\EasyAdminBundle\Twig\EasyAdminTwigExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EasyAdminTwigExtensionTest extends KernelTestCase
{
    /**
     * @dataProvider provideValuesForRepresentAsString
     */
    public function testRepresentAsString($value, $expectedValue, bool $assertRegex = false, string|callable|null $toStringMethod = null): void
    {
        $customTranslator = new class implements TranslatorInterface {
            public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
            {
                return '*'.$id;
            }

            public function getLocale(): string
            {
                return 'en';
            }
        };

        $reflectedClass = new \ReflectionClass(EasyAdminTwigExtension::class);
        $twigExtensionInstance = $reflectedClass->newInstanceWithoutConstructor();
        $property = $reflectedClass->getProperty('translator');
        $property->setValue($twigExtensionInstance, $customTranslator);

        $result = $twigExtensionInstance->representAsString($value, $toStringMethod);

        if ($assertRegex) {
            $this->assertMatchesRegularExpression($expectedValue, $result);
        } else {
            $this->assertSame($expectedValue, $result);
        }

        $this->assertStringNotContainsString("\0", $result, 'The string representation of a value must not contain the null character (which can happen when the original value is an anonymous class object)');
    }

    public function testRepresentAsStringException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/The method "someMethod\(\)" does not exist or is not callable in the value of type "class@anonymous.*"/');

        $reflectedClass = new \ReflectionClass(EasyAdminTwigExtension::class);
        $twigExtensionInstance = $reflectedClass->newInstanceWithoutConstructor();

        $twigExtensionInstance->representAsString(new class {}, 'someMethod');
    }

    public function provideValuesForRepresentAsString(): iterable
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
