<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Controller;

use Doctrine\ORM\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Config\Action as AppAction;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\CategoryCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller\SecureDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class CategoryCrudControllerTest extends AbstractCrudTestCase
{
    protected EntityRepository $categories;

    protected function getControllerFqcn(): string
    {
        return CategoryCrudController::class;
    }

    protected function getDashboardFqcn(): string
    {
        return SecureDashboardController::class;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->client->followRedirects();
        $this->client->setServerParameters(['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => '1234']);

        $this->categories = $this->entityManager->getRepository(Category::class);
    }

    public function testConfigureAssets()
    {
        $crawler = $this->client->request('GET', $this->generateIndexUrl());

        static::assertCount(1, $crawler->filter('head > link[data-added-from-controller]'));
        static::assertCount(1, $crawler->filter('body > span[data-added-from-controller]'));
    }

    /**
     * @dataProvider new
     */
    public function testNew(?string $invalidCsrfToken, ?string $expectedErrorMessage)
    {
        $this->client->request('GET', $this->generateNewFormUrl());

        $form = [
            'Category[name]' => 'Foo',
            'Category[slug]' => 'foo',
        ];
        if (null !== $invalidCsrfToken) {
            $form['Category[_token]'] = $invalidCsrfToken;
        }

        $this->client->submitForm('Create', $form);
        if (null === $expectedErrorMessage) {
            static::assertSelectorNotExists('.global-invalid-feedback');
            static::assertInstanceOf(Category::class, $this->categories->findOneBy(['slug' => 'foo']));
        } else {
            $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
            static::assertSelectorTextContains('.global-invalid-feedback', $expectedErrorMessage);
            static::assertNull($this->categories->findOneBy(['slug' => 'foo']));
        }
    }

    public static function new(): \Generator
    {
        yield [
            '', // Manipulate the CSRF token to an empty value
            'The CSRF token is invalid.',
        ];
        yield [
            '123abc', // Manipulate the CSRF token to this invalid value
            'The CSRF token is invalid.',
        ];
        yield [
            null, // Do not manipulate the CSRF token
            null, // Do not expect any error
        ];
    }

    /**
     * @dataProvider edit
     *
     * @group legacy
     */
    public function testEdit(?string $invalidCsrfToken, ?string $expectedErrorMessage)
    {
        $this->client->request('GET', $this->generateEditFormUrl($this->categories->findOneBy([])->getId()));

        $form = [
            'Category[name]' => 'Bar',
            'Category[slug]' => 'bar',
        ];
        if (null !== $invalidCsrfToken) {
            $form['Category[_token]'] = $invalidCsrfToken;
        }

        $this->client->submitForm('Save changes', $form);
        if (null === $expectedErrorMessage) {
            static::assertSelectorNotExists('.global-invalid-feedback');
            static::assertInstanceOf(Category::class, $this->categories->findOneBy(['slug' => 'bar']));
        } else {
            $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
            static::assertSelectorTextContains('.global-invalid-feedback', $expectedErrorMessage);
            static::assertNull($this->categories->findOneBy(['slug' => 'bar']));
        }
    }

    public static function edit(): \Generator
    {
        yield [
            '', // Manipulate the CSRF token to an empty value
            'The CSRF token is invalid.',
        ];
        yield [
            '123abc', // Manipulate the CSRF token to this invalid value
            'The CSRF token is invalid.',
        ];
        yield [
            null, // Do not manipulate the CSRF token
            null, // Do not expect any error
        ];
    }

    /**
     * @dataProvider delete
     *
     * @group legacy
     */
    public function testDelete(?string $invalidCsrfToken, callable $expectedCategoriesCount)
    {
        $initialCategoriesCount = \count($this->categories->findAll());

        // List all categories
        $crawler = $this->client->request('GET', $this->generateIndexUrl());
        static::assertIndexFullEntityCount($initialCategoriesCount);

        // Try to delete the first found category
        $form = $crawler->filter('#delete-form')->form();
        $form->getNode()->setAttribute(
            'action',
            $crawler->filter('a.action-delete')->first()->attr('formaction')
        );
        if (null !== $invalidCsrfToken) {
            $form['token'] = $invalidCsrfToken;
        }
        $this->client->submit($form);

        // List all categories again and see if the result count changed
        $this->client->request('GET', $this->generateIndexUrl());
        static::assertIndexFullEntityCount($expectedCategoriesCount($initialCategoriesCount));
    }

    public static function delete(): \Generator
    {
        yield [
            '', // Manipulate the CSRF token to an empty value
            fn (int $initialCategoriesCount): int => $initialCategoriesCount,
        ];
        yield [
            '123abc', // Manipulate the CSRF token to this invalid value
            fn (int $initialCategoriesCount): int => $initialCategoriesCount,
        ];
        yield [
            null, // Do not manipulate the CSRF token
            fn (int $initialCategoriesCount): int => $initialCategoriesCount - 1,
        ];
    }

    public function testDetail()
    {
        /* @var Category $category */
        $category = $this->categories->findOneBy([]);

        $crawler = $this->client->request('GET', $this->generateDetailUrl($category->getId()));

        static::assertSame((string) $category->getId(), $crawler->filter('.content-body .field-group')->eq(0)->filter('.field-value')->text());
        static::assertSame($category->getName(), $crawler->filter('.content-body .field-group')->eq(1)->filter('.field-value')->text());
        static::assertSame($category->getSlug(), $crawler->filter('.content-body .field-group')->eq(2)->filter('.field-value')->text());
        static::assertSame(true === $category->isActive() ? 'Yes' : 'No', $crawler->filter('.content-body .field-group')->eq(3)->filter('.field-value')->text());
    }

    /**
     * @dataProvider toggle
     */
    public function testToggle(string $method, ?string $invalidCsrfToken, ?string $fieldName, int $expectedStatusCode, bool $toggleIsExpectedToSucceed)
    {
        $expectedExceptionClass = match ($expectedStatusCode) {
            Response::HTTP_METHOD_NOT_ALLOWED => MethodNotAllowedHttpException::class,
            Response::HTTP_BAD_REQUEST => BadRequestHttpException::class,
            default => null,
        };

        if (null !== $expectedExceptionClass) {
            // needed to not display 'Uncaught PHP exception' messages in PHPUnit output
            // see https://stackoverflow.com/questions/50456114/phpunit-dont-report-symfony-exceptions-rendered-to-http-errors/50465691
            $this->expectException($expectedExceptionClass);
            $this->client->catchExceptions(false);
        }

        // Find the first toggle URL in the category list
        $crawler = $this->client->request('GET', $this->generateIndexUrl());
        $firstFoundToggleUrl = $crawler->filter('td.field-boolean .form-switch input[type="checkbox"]')->first()->attr('data-toggle-url');

        // Get the category's active state from the DB
        parse_str(parse_url($firstFoundToggleUrl, \PHP_URL_QUERY), $parameters);
        $categoryId = $parameters['entityId'];
        $active = $this->categories->find($categoryId)->isActive();
        static::assertIsBool($active);

        // Adapt the toggle URL, so it will change the category's active property to the opposite of what it is currently
        $firstFoundToggleUrl .= sprintf('&newValue=%s', false === $active ? 'true' : 'false');

        // Change the CSRF token
        if (null !== $invalidCsrfToken) {
            $firstFoundToggleUrl = preg_replace('/csrfToken=.+?&/', sprintf('csrfToken=%s&', $invalidCsrfToken), $firstFoundToggleUrl);
        }

        // Change the field name
        if (null !== $fieldName) {
            $firstFoundToggleUrl = preg_replace('/fieldName=.+?&/', sprintf('fieldName=%s&', $fieldName), $firstFoundToggleUrl);
        }

        // Do the AJAX request
        $this->client->request($method, $firstFoundToggleUrl, [], [], [
            'HTTP_x-requested-with' => 'XMLHttpRequest',
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '1234',
        ]);
        static::assertResponseStatusCodeSame($expectedStatusCode);
        /* @var Category $category */
        $this->entityManager->refresh($category = $this->categories->find($categoryId)); // After the request refresh the category
        static::assertIsBool($category->isActive());
        if (true === $toggleIsExpectedToSucceed) {
            static::assertNotSame($active, $category->isActive());
        } else {
            static::assertSame($active, $category->isActive());
        }
    }

    public function testPagination()
    {
        $crawler = $this->client->request('GET', $this->generateIndexUrl());

        $prevPageItem = $crawler->filter('.list-pagination-paginator .page-item:nth-child(1)');
        $prevPageLink = $prevPageItem->filter('.page-link');
        $firstPageItem = $crawler->filter('.list-pagination-paginator .page-item:nth-child(2)');
        $firstPageLink = $firstPageItem->filter('.page-link');
        $secondPageLink = $crawler->filter('.list-pagination-paginator .page-item:nth-child(3) .page-link');
        $nextPageItem = $crawler->filter('.list-pagination-paginator .page-item:nth-child(4)');
        $nextPageLink = $nextPageItem->filter('.page-link');

        // test global number of pages
        static::assertIndexPagesCount(2); // 30 categories with 20 categories per page

        // test current page number of entities
        static::assertIndexPageEntityCount(20); // 20 rows per page

        // test default pagination items
        static::assertCount(4, $crawler->filter('.list-pagination-paginator .page-item'));
        static::assertStringContainsString('Previous', $prevPageLink->text());
        static::assertStringContainsString('disabled', $prevPageItem->attr('class'));
        static::assertStringContainsString('1', $firstPageLink->text());
        static::assertStringContainsString('active', $firstPageItem->attr('class'));
        static::assertStringContainsString('2', $secondPageLink->text());
        static::assertStringContainsString('Next', $nextPageLink->text());

        // test default pagination URLs
        $firstPageUrl = $firstPageLink->attr('href');
        static::assertSame('1', $this->getParameterFromUrlQueryString($firstPageUrl, EA::PAGE));

        $secondPageUrl = $secondPageLink->attr('href');
        static::assertSame('2', $this->getParameterFromUrlQueryString($secondPageUrl, EA::PAGE));

        $nextPageUrl = $nextPageLink->attr('href');
        static::assertSame($secondPageUrl, $nextPageUrl);

        // test pagination maintains all query parameters, including custom ones
        $queryParameters = http_build_query(['sort[name]' => 'DESC', 'CUSTOM_param' => 'foobar1234']);
        $crawler = $this->client->request('GET', $this->generateIndexUrl().'&'.$queryParameters);

        $firstPageUrl = $crawler->filter('.list-pagination-paginator .page-item:nth-child(2) .page-link')->attr('href');
        static::assertSame(['name' => 'DESC'], $this->getParameterFromUrlQueryString($firstPageUrl, 'sort'));
        static::assertSame('foobar1234', $this->getParameterFromUrlQueryString($firstPageUrl, 'CUSTOM_param'));

        $nextPagePageUrl = $crawler->filter('.list-pagination-paginator .page-item:nth-child(4) .page-link')->attr('href');
        static::assertSame(['name' => 'DESC'], $this->getParameterFromUrlQueryString($nextPagePageUrl, 'sort'));
        static::assertSame('foobar1234', $this->getParameterFromUrlQueryString($nextPagePageUrl, 'CUSTOM_param'));
    }

    public static function toggle(): \Generator
    {
        yield [
            'GET', // HTTP method
            null, // Do not manipulate the CSRF token
            null, // Do not manipulate the field name
            Response::HTTP_METHOD_NOT_ALLOWED, // Response status code, fails because of wrong method "GET"
            false, // Should the toggle successfully change the toggled property?
        ];
        yield [
            'PATCH',
            '123abc', // Manipulate the CSRF token to this invalid value
            null, // Do not manipulate the field name
            Response::HTTP_UNAUTHORIZED, // Response status code, fails because of wrong CSRF token
            false,
        ];
        yield [
            'PATCH',
            null, // Do not manipulate the CSRF token
            null, // Do not manipulate the field name
            Response::HTTP_OK,
            true,
        ];
        yield [
            'PATCH',
            null, // Do not manipulate the CSRF token
            'activeWithNoPermission', // Manipulate the field name to field with invalid permission
            Response::HTTP_BAD_REQUEST,
            false,
        ];
        yield [
            'PATCH',
            null, // Do not manipulate the CSRF token
            'activeDisabled', // Manipulate the field name to disabled field
            Response::HTTP_BAD_REQUEST,
            false,
        ];
    }

    /**
     * @dataProvider filter
     */
    public function testFilter(array $categories, array $filters, int $expectedResultCount)
    {
        foreach ($categories as $category) {
            $this->entityManager->persist($category);
        }
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', $this->generateFilterRenderUrl(), [], [], ['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => '1234']);
        $form = $crawler->filter('form[name="filters"]')->form();
        $form['filters'] = $filters;
        $this->client->submit($form, [], ['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => '1234']);
        static::assertIndexFullEntityCount($expectedResultCount);
    }

    public static function filter(): \Generator
    {
        yield [
            [],
            [
                'name' => [
                    'comparison' => 'like',
                    'value' => 'foobazfoobar',
                ],
            ],
            0,
        ];
        yield [
            [
                (new Category())->setName('Buzzbar')->setSlug('buzzbar'),
            ],
            [
                'name' => [
                    'comparison' => 'like',
                    'value' => 'buzzb',
                ],
            ],
            1,
        ];
        yield [
            [
                (new Category())->setName('Buzzfoo')->setSlug('buzzfoo')->setActive(true),
                (new Category())->setName('Buzzfoo 2')->setSlug('buzzfoo-2')->setActive(true),
                (new Category())->setName('Buzzfoo 3')->setSlug('buzzfoo-3')->setActive(false),
            ],
            [
                'name' => [
                    'comparison' => 'like',
                    'value' => 'zzfoo',
                ],
                'active' => '1',
            ],
            2,
        ];
    }

    /**
     * @dataProvider customPage
     */
    public function testCustomPage(string $username, int $expectedStatusCode)
    {
        if (Response::HTTP_FORBIDDEN === $expectedStatusCode) {
            // needed to not display 'Uncaught PHP exception' messages in PHPUnit output
            // see https://stackoverflow.com/questions/50456114/phpunit-dont-report-symfony-exceptions-rendered-to-http-errors/50465691
            $this->expectException(ForbiddenActionException::class);
            $this->client->catchExceptions(false);
        }

        $this->client->request('GET', $this->getCrudUrl(AppAction::CUSTOM_ACTION), [], [], ['PHP_AUTH_USER' => $username, 'PHP_AUTH_PW' => '1234']);

        static::assertResponseStatusCodeSame($expectedStatusCode);
    }

    public static function customPage(): \Generator
    {
        yield [self::generateUsername('ROLE_USER'), Response::HTTP_FORBIDDEN];
        yield [self::generateUsername('ROLE_ADMIN'), Response::HTTP_OK];
    }

    private static function generateUsername(string $role): string
    {
        switch ($role) {
            case 'ROLE_USER':
                return 'user';
            case 'ROLE_ADMIN':
                return 'admin';
        }

        throw new \InvalidArgumentException(sprintf('Unknown role, use one of: %s', implode(', ', ['ROLE_USER', 'ROLE_ADMIN'])));
    }

    private function getParameterFromUrlQueryString(string $url, string $parameterName): string|array|null
    {
        $queryString = parse_url($url, \PHP_URL_QUERY);
        parse_str($queryString, $queryStringParams);

        return $queryStringParams[$parameterName] ?? null;
    }
}
