CRUD Controllers
================

**CRUD controllers** provide the CRUD operations (create, show, update, delete)
for Doctrine ORM entities. Each CRUD controller can be associated to one or more
dashboards.

Technically, these CRUD controllers are regular `Symfony controllers`_ so you can
do anything you usually do in a controller, such as injecting services and using
shortcuts like ``$this->render()`` or ``$this->isGranted()``.

CRUD controllers must implement the
``EasyCorp\Bundle\EasyAdminBundle\Contracts\Controller\CrudControllerInterface``,
which ensures that certain methods are defined in the controller. Instead of implementing
the interface, you can also extend from the ``AbstractCrudController`` class.
Run the following command to generate the basic structure of a CRUD controller:

.. code-block:: terminal

    $ php bin/console make:admin:crud

.. _crud-pages:

CRUD Controller Pages
---------------------

The four main pages of the CRUD controllers are:

* ``index``, displays a list of entities which can be paginated, sorted by
  column and refined with search queries and filters;
* ``detail``, displays the contents of a given entity;
* ``new``, allows to create new entity instances;
* ``edit``, allows to update any property of a given entity.

These pages are generated with four actions with the same name in the
``AbstractCrudController`` controller. This controller defines other secondary
actions (e.g. ``delete`` and ``autocomplete``) which don't match any page.

.. _crud_routes:

CRUD Routes
~~~~~~~~~~~

When using :ref:`pretty admin URLs <pretty-admin-urls>`, each of the CRUD actions
define an admin route following this name and path by default:

==================  ======================
CRUD route name     CRUD route path
==================  ======================
``*_index``         ``/``
``*_new``           ``/new``
``*_batch_delete``   ``/batch-delete``
``*_autocomplete``  ``/autocomplete``
``*_edit``          ``/{entityId}/edit``
``*_delete``        ``/{entityId}/delete``
``*_detail``        ``/{entityId}``
==================  ======================

For example, for a CRUD controller called ``ProductCrudController`` that belongs
to a backend with a route named ``admin`` and with the path ``/admin``, it will
generate the following routes:

==============================  ===============================
Admin route name                Admin route path
==============================  ===============================
``admin_product_index``         ``/admin/product``
``admin_product_new``           ``/admin/product/new``
``admin_product_batch_delete``   ``/admin/product/batch-delete``
``admin_product_autocomplete``  ``/admin/product/autocomplete``
``admin_product_edit``          ``/admin/product/324/edit``
``admin_product_delete``        ``/admin/product/324/delete``
``admin_product_detail``        ``/admin/product/324``
==============================  ===============================

.. tip::

    By default, EasyAdmin generates routes for all CRUD controllers on all
    dashboards. You can :ref:`restrict which controllers are accessible <security-controllers>`
    on each dahboard to not generate all these routes.

You can customize the route names and/or paths of the actions of all the CRUD controllers
served by some dashboard using the ``#[AdminDashboard]`` attribute::

    use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
    // ...

    #[AdminDashboard(routes: [
        'index' => ['routePath' => '/all'],
        'new' => ['routePath' => '/create', 'routeName' => 'create'],
        'edit' => ['routePath' => '/editing-{entityId}', 'routeName' => 'editing'],
        'delete' => ['routePath' => '/remove/{entityId}'],
        'detail' => ['routeName' => 'view'],
    ])]
    class SomeDashboardController extends AbstractDashboardController
    {
        // ...
    }

With this configuration, the routes for the ``ProductCrudController`` actions will be:

==============================  =====================================
Admin route name                Admin route path
==============================  =====================================
``admin_product_index``         ``/admin/product/all``
``admin_product_create``        ``/admin/product/create``
``admin_product_batch_delete``   ``/admin/product/current/batch-delete``
``admin_product_autocomplete``  ``/admin/product/current/autocomplete``
``admin_product_editing``       ``/admin/product/current/editing-324``
``admin_product_delete``        ``/admin/product/remove/324``
``admin_product_view``          ``/admin/product/324``
==============================  =====================================

You can also customize the path and/or route name of CRUD controllers using the
``#[AdminCrud]`` attribute with the following options:

* ``routePath``: the value that represents the controller in the entire route path
  (e.g. a ``/foo`` path will result in ``/admin`` + ``/foo`` + ``/<action>``);
* ``routeName``: the value that represents the controller in the full route name
  (e.g. a ``foo_bar`` route name will result in ``admin_`` + ``foo_bar`` + ``_<action>``).

Using the same example as above, you can configure the route names and paths of
the controller as follows::

    use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
    // ...

    #[AdminCrud(routePath: '/stock/current', routeName: 'stock')]
    class ProductCrudController extends AbstractCrudController
    {
        // ...
    }

The route names/paths will no longer be ``admin_product_*`` and ``/admin/product/*``
but the following:

==============================  =====================================
Admin route name                Admin route path
==============================  =====================================
``admin_stock_index``           ``/admin/stock/current``
``admin_stock_new``             ``/admin/stock/current/new``
``admin_stock_batch_delete``     ``/admin/stock/current/batch-delete``
``admin_stock_autocomplete``    ``/admin/stock/current/autocomplete``
``admin_stock_edit``            ``/admin/stock/current/324/edit``
``admin_stock_delete``          ``/admin/stock/current/324/delete``
``admin_stock_detail``          ``/admin/stock/current/324``
==============================  =====================================

Finally, you can also customize the route name and/or path of each CRUD controller
action using the ``#[AdminAction]`` attribute::

    use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
    // ...

    class ProductCrudController extends AbstractCrudController
    {
        // ...

        #[AdminAction(routePath: '/latest-products', routeName: 'latest')]
        public function index(AdminContext $context)
        {
            // ...
        }
    }

The ``index()`` action of this controller will no longer use the ``admin_product_index``
route name and the ``/admin/product`` path in the URL. Instead, the route name
will be ``admin_product_latest`` and the path will be ``/admin/product/latest-products``.

.. tip::

    You can combine the ``#[AdminDashboard]``, ``#[AdminCrud]``, and ``#[AdminAction]``
    attributes to customize some or all route names and paths.

Page Names and Constants
~~~~~~~~~~~~~~~~~~~~~~~~

Some methods require as argument the name of some CRUD page. You can use any of
the following strings: ``'index'``, ``'detail'``, ``'edit'`` and ``'new'``. If
you prefer to use constants for these values, use ``Crud::PAGE_INDEX``,
``Crud::PAGE_DETAIL``, ``Crud::PAGE_EDIT`` and ``Crud::PAGE_NEW`` (they are
defined in the ``EasyCorp\Bundle\EasyAdminBundle\Config\Crud`` class).

CRUD Controller Configuration
-----------------------------

The only mandatory config option of a CRUD controller is the FQCN of the
Doctrine entity being managed by the controller. This is defined as a public
static method::

    namespace App\Controller\Admin;

    use App\Entity\Product;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

    class ProductCrudController extends AbstractCrudController
    {
        // it must return a FQCN (fully-qualified class name) of a Doctrine ORM entity
        public static function getEntityFqcn(): string
        {
            return Product::class;
        }

        // ...
    }

The rest of CRUD options are configured using the ``configureCrud()`` method::

    namespace App\Controller\Admin;

    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

    class ProductCrudController extends AbstractCrudController
    {
        // ...

        public function configureCrud(Crud $crud): Crud
        {
            return $crud
                ->setEntityLabelInSingular('...')
                ->setDateFormat('...')
                // ...
            ;
        }
    }

Design Options
~~~~~~~~~~~~~~

::

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // set this option if you prefer the page content to span the entire
            // browser width, instead of the default design which sets a max width
            ->renderContentMaximized()

            // set this option if you prefer the sidebar (which contains the main menu)
            // to be displayed as a narrow column instead of the default expanded design
            ->renderSidebarMinimized()
        ;
    }

.. _crud_entity_options:

Entity Options
~~~~~~~~~~~~~~

::

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Product')
            ->setEntityLabelInPlural('Products')

            // in addition to a string, the argument of the singular and plural label methods
            // can be a closure that defines two nullable arguments: entityInstance (which will
            // be null in 'index' and 'new' pages) and the current page name
            ->setEntityLabelInSingular(
                fn (?Product $product, ?string $pageName) => $product ? $product->toString() : 'Product'
            )
            ->setEntityLabelInPlural(function (?Category $category, ?string $pageName) {
                return 'edit' === $pageName ? $category->getLabel() : 'Categories';
            })

            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            ->setEntityPermission('ROLE_EDITOR')
        ;
    }

Title and Help Options
~~~~~~~~~~~~~~~~~~~~~~

By default, the page titles of the ``index`` and ``new`` pages are based on the
:ref:`entity option <crud_entity_options>` values defined with the
``setEntityLabelInSingular()`` and ``setEntityLabelInPlural()`` methods. In the
``detail`` and ``edit`` pages, EasyAdmin tries first to convert the entity into
a string representation and falls back to a generic title otherwise.

You can override the default page titles with the following methods::

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the visible title at the top of the page and the content of the <title> element
            // it can include these placeholders:
            //   %entity_name%, %entity_as_string%,
            //   %entity_id%, %entity_short_id%
            //   %entity_label_singular%, %entity_label_plural%
            ->setPageTitle('index', '%entity_label_plural% listing')

            // you can pass a PHP closure as the value of the title
            ->setPageTitle('new', fn () => new \DateTime('now') > new \DateTime('today 13:00') ? 'New dinner' : 'New lunch')

            // in DETAIL and EDIT pages, the closure receives the current entity
            // as the first argument
            ->setPageTitle('detail', fn (Product $product) => (string) $product)
            ->setPageTitle('edit', fn (Category $category) => sprintf('Editing <b>%s</b>', $category->getName()))

            // the help message displayed to end users (it can contain HTML tags)
            ->setHelp('edit', '...')
        ;
    }

EasyAdmin applies the ``raw`` filter to all titles, labels, help messages, etc.
displayed in templates. This is done to allow you to customize everything with
HTML tags (because those tags will be rendered instead of escaped).

That's why the default page titles used by EasyAdmin only include safe contents
like the entity name and ID. Otherwise, your backend could be vulnerable to
`XSS attacks`_.

If you change the default page title to include the placeholder ``%entity_as_string%``,
check that you don't include user-created contents in the value returned by the
``__toString()`` method of the related entity. If you can't avoid that, make sure
to sanitize any user submitted data with the Symfony `HtmlSanitizer component`_.

.. _crud-date-time-number-format-options:

Date, Time and Number Formatting Options
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

::

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the argument must be either one of these strings: 'short', 'medium', 'long', 'full', 'none'
            // (the strings are also available as \EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField::FORMAT_* constants)
            // or a valid ICU Datetime Pattern (see https://unicode-org.github.io/icu/userguide/format_parse/datetime/)
            ->setDateFormat('...')
            ->setTimeFormat('...')

            // first argument = datetime pattern or date format; second optional argument = time format
            ->setDateTimeFormat('...', '...')

            ->setDateIntervalFormat('%%y Year(s) %%m Month(s) %%d Day(s)')
            ->setTimezone('...')

            // this option makes numeric values to be rendered with a sprintf()
            // call using this value as the first argument.
            // this option overrides any formatting option for all numeric values
            // (e.g. setNumDecimals(), setRoundingMode(), etc. are ignored)
            // NumberField and IntegerField can override this value with their
            // own setNumberFormat() methods, which works in the same way
            ->setNumberFormat('%.2d')

            // Sets the character used to separate each thousand group in a number
            // e.g. if separator is ',' then 12345 is formatted as 12,345
            // By default, EasyAdmin doesn't add any thousands separator to numbers;
            // NumberField and IntegerField can override this value with their
            // own setThousandsSeparator() methods, which works in the same way
            ->setThousandsSeparator(',')

            // Sets the character used to separate the decimal part of a non-integer number
            // e.g. if separator is '.' then 1/10 is formatted as 0.1
            // by default, EasyAdmin displays the default decimal separator used by PHP;
            // NumberField and IntegerField can override this value with their
            // own setDecimalSeparator() methods, which works in the same way
            ->setDecimalSeparator('.')
        ;
    }

Search, Order, and Pagination Options
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

::

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // ...

            // the names of the Doctrine entity properties where the search is made on
            // (by default it looks for in all properties)
            ->setSearchFields(['name', 'description'])
            // use dots (e.g. 'seller.email') to search in Doctrine associations
            ->setSearchFields(['name', 'description', 'seller.email', 'seller.address.zipCode'])
            // set it to null to disable and hide the search box
            ->setSearchFields(null)
            // call this method to focus the search input automatically when loading the 'index' page
            ->setAutofocusSearch()

            // by default, the search results match all the terms (SearchMode::ALL_TERMS):
            // term1 in (field1 or field2) AND term2 in (field1 or field2)
            // e.g. if you look for 'lorem ipsum' in [title, description],
            // results require matching 'lorem' in either title or description
            // (or both) AND 'ipsum' in either title or description (or both)
            ->setSearchMode(SearchMode::ALL_TERMS)

            // use the SearchMode::ANY_TERMS option to change the search mode to
            // match at least one of the terms:
            // term1 in (field1 or field2) OR term2 in (field1 or field2)
            // e.g. if you look for 'lorem ipsum' in [title, description],
            // results will match either 'lorem' in title or description (or both)
            // OR 'ipsum' in title or description (or both)
            ->setSearchMode(SearchMode::ANY_TERMS)
        ;
    }

.. tip::

    The search engine splits all terms by default (searching for ``foo bar``
    returns items with ``foo`` and ``bar``). You can wrap all or part of your
    query with quotes to make an exact search: ``"foo bar"`` only returns
    items with that exact content, including the middle white space.

::

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // ...

            // defines the initial sorting applied to the list of entities
            // (user can later change this sorting by clicking on the table columns)
            ->setDefaultSort(['id' => 'DESC'])
            ->setDefaultSort(['id' => 'DESC', 'title' => 'ASC', 'startsAt' => 'DESC'])
            // you can sort by Doctrine associations up to two levels
            ->setDefaultSort(['seller.name' => 'ASC'])
        ;
    }

::

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // ...

            // the max number of entities to display per page
            ->setPaginatorPageSize(30)
            // the number of pages to display on each side of the current page
            // e.g. if num pages = 35, current page = 7 and you set ->setPaginatorRangeSize(4)
            // the paginator displays: [Previous]  1 ... 3  4  5  6  [7]  8  9  10  11 ... 35  [Next]
            // set this number to 0 to display a simple "< Previous | Next >" pager
            ->setPaginatorRangeSize(4)

            // these are advanced options related to Doctrine Pagination
            // (see https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/tutorials/pagination.html)
            ->setPaginatorUseOutputWalkers(true)
            ->setPaginatorFetchJoinCollection(true)
        ;
    }

.. note::

    When using `Doctrine filters`_, listings may not include some items because
    they were removed by those global Doctrine filters. Use the dashboard route
    name to not apply the filters when the request URL belongs to the dashboard
    You can also get the dashboard route name via the :ref:`application context variable <admin-context>`.

The default Doctrine query executed to get the list of entities displayed in the
``index`` page takes into account the sorting configuration, the optional search
query, the optional :doc:`filters </filters>` and the pagination. If you need to
fully customize this query, override the ``createIndexQueryBuilder()`` method in
your CRUD controller.

Templates and Form Options
~~~~~~~~~~~~~~~~~~~~~~~~~~

::

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // this method allows to use your own template to render a certain part
            // of the backend instead of using EasyAdmin default template
            // the first argument is the "template name", which is the same as the
            // Twig path but without the `@EasyAdmin/` prefix and the `.html.twig` suffix
            ->overrideTemplate('crud/field/id', 'admin/fields/my_id.html.twig')

            // the theme/themes to use when rendering the forms of this entity
            // (in addition to EasyAdmin default theme)
            ->addFormTheme('foo.html.twig')
            // this method overrides all existing form themes (including the
            // default EasyAdmin form theme)
            ->setFormThemes(['my_theme.html.twig', 'admin.html.twig'])

            // this sets the options of the entire form (later, you can set the options
            // of each form type via the methods of their associated fields)
            // pass a single array argument to apply the same options for the new and edit forms
            ->setFormOptions([
                'validation_groups' => ['Default', 'my_validation_group']
            ]);

            // pass two array arguments to apply different options for the new and edit forms
            // (pass an empty array argument if you want to apply no options to some form)
            ->setFormOptions(
                ['validation_groups' => ['my_validation_group']],
                ['validation_groups' => ['Default'], '...' => '...'],
            );
        ;
    }

Other Options
~~~~~~~~~~~~~

::

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // by default, when the value of some field is `null`, EasyAdmin displays
            // a label with the `null` text. You can change that by overriding
            // the `label/null` template. However, if you have lots of `null` values
            // and want to reduce the "visual noise" in your backend, you can use
            // the following option to not display anything when some value is `null`
            // (this option is applied both in the `index` and `detail` pages)
            ->hideNullValues()
    }

Custom Redirect After Creating or Editing Entities
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

By default, when clicking on "Save" button when creating or editing entities
you are redirected to the previous page. If you want to change this behavior,
override the ``getRedirectResponseAfterSave()`` method.

For example, if you've added a :ref:`custom action <actions-custom>` called
"Save and view detail", you may prefer to redirect to the detail page after
saving the changes::

    protected function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        $submitButtonName = $context->getRequest()->request->all()['ea']['newForm']['btn'];

        if ('saveAndViewDetail' === $submitButtonName) {
            // when using pretty admin URLs
            return $this->redirectToRoute('admin_product_detail', [
                'entityId' => $context->getEntity()->getPrimaryKeyValue(),
            ]);

            // when using legacy admin URLs
            $url = $this->container->get(AdminUrlGenerator::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($context->getEntity()->getPrimaryKeyValue())
                ->generateUrl();

            return $this->redirect($url);
        }

        return parent::getRedirectResponseAfterSave($context, $action);
    }

Same Configuration in Different CRUD Controllers
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you want to do the same config in all CRUD controllers, there's no need to
repeat the config in each controller. Instead, add the ``configureCrud()`` method
in your dashboard and all controllers will inherit that configuration::

    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

    class DashboardController extends AbstractDashboardController
    {
        // ...

        public function configureCrud(): Crud
        {
            return Crud::new()
                // this defines the pagination size for all CRUD controllers
                // (each CRUD controller can override this value if needed)
                ->setPaginatorPageSize(30)
            ;
        }
    }

Fields
------

Fields allow to display the contents of your Doctrine entities on each
:ref:`CRUD page <crud-pages>`. EasyAdmin provides built-in fields to display
all the common data types, but you can also :ref:`create your own fields <custom-fields>`.

If your CRUD controller extends from the ``AbstractCrudController``, the fields
are configured automatically. In the ``index`` page you'll see a few fields and
in the rest of pages you'll see as many fields as needed to display all the
properties of your Doctrine entity.

Read the :doc:`chapter about Fields </fields>` to learn how to configure which
fields to display on each page, how to configure the way each field is rendered, etc.

Customizing CRUD Actions
------------------------

The default CRUD actions (``index()``, ``detail()``, ``edit()``, ``new()`` and
``delete()`` methods in the controller) implement the most common behaviors
used in applications.

The first way to customize their behavior is to override those methods in your
own controllers. However, the original actions are so generic that they contain
quite a lot of code, so overriding them is not that convenient.

Instead, you can override other smaller methods that implement certain features
needed by the CRUD actions. For example, the ``index()`` action calls to a
method named ``createIndexQueryBuilder()`` to create the Doctrine query builder
used to get the results displayed on the index listing. If you want to customize
that listing, it's better to override the ``createIndexQueryBuilder()`` method
instead of the entire ``index()`` method. There are many of these methods, so
you should check the ``EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController`` class.

The other alternative to customize CRUD actions is to use the
:doc:`events triggered by EasyAdmin </events>`, such as ``BeforeCrudActionEvent``
and ``AfterCrudActionEvent``.

Creating, Persisting and Deleting Entities
------------------------------------------

Most of the actions of a CRUD controller end up creating, persisting or deleting
entities. If your CRUD controller extends from the ``AbstractCrudController``,
these methods are already implemented, but you can customize them overriding
methods and listening to events.

First, you can override the ``createEntity()``, ``updateEntity()``, ``persistEntity()``
and ``deleteEntity()`` methods. The ``createEntity()`` method for example only
executes ``return new $entityFqcn()``, so you need to override it if your entity
needs to pass constructor arguments or set some of its properties::

    namespace App\Controller\Admin;

    use App\Entity\Product;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

    class ProductCrudController extends AbstractCrudController
    {
        public static function getEntityFqcn(): string
        {
            return Product::class;
        }

        public function createEntity(string $entityFqcn)
        {
            $product = new Product();
            $product->createdBy($this->getUser());

            return $product;
        }

        // ...
    }

The other way of overriding this behavior is listening to the
:doc:`events triggered by EasyAdmin </events>` when an entity is created, updated,
persisted, deleted, etc.

Passing Additional Variables to CRUD Templates
----------------------------------------------

The default CRUD actions implemented in ``AbstractCrudController`` don't end
with the usual ``$this->render('...')`` instruction to render a Twig template
and return its contents in a Symfony ``Response`` object.

Instead, CRUD actions return a ``EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore``
object with the variables passed to the template that renders the CRUD action
contents. This ``KeyValueStore`` object is similar to Symfony's ``ParameterBag``
object. It's like an object-oriented array with useful methods such as ``get()``,
``set()``, ``has()``, etc.

Before ending each CRUD action, their ``KeyValueStore`` object is passed to a
method called ``configureResponseParameters()`` which you can override in your
own controller to add/remove/change those template variables::

    namespace App\Controller\Admin;

    use App\Entity\Product;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
    use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

    class ProductCrudController extends AbstractCrudController
    {
        // ...

        public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
        {
            if (Crud::PAGE_DETAIL === $responseParameters->get('pageName')) {
                $responseParameters->set('foo', '...');

                // keys support the "dot notation", so you can get/set nested
                // values separating their parts with a dot:
                $responseParameters->setIfNotSet('bar.foo', '...');
                // this is equivalent to: $parameters['bar']['foo'] = '...'
            }

            return $responseParameters;
        }
    }

You can add as many or as few parameters to this ``KeyValueStore`` object as you
need. The only mandatory parameter is either ``templateName`` or
``templatePath`` to set respectively the name or path of the template to render
as the result of the CRUD action.

Template Names and Template Paths
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

All the templates used by EasyAdmin to render its contents are configurable.
That's why EasyAdmin deals with "template names" instead of normal Twig
template paths.

A template name is the same as the template path but without the ``@EasyAdmin``
prefix and the ``.html.twig`` suffix. For example, ``@EasyAdmin/layout.html.twig``
refers to the built-in layout template provided by EasyAdmin. However, ``layout``
refers to "whichever template is configured as the layout in the application".

Working with template names instead of paths gives you full flexibility to
customize the application behavior while keeping all the customized templates.
In Twig templates, use the ``ea.templatePath()`` function to get the Twig path
associated to the given template name:

.. code-block:: twig

    <div id="flash-messages">
        {{ include(ea.templatePath('flash_messages')) }}
    </div>

    {% if some_value is null %}
        {{ include(ea.templatePath('label/null')) }}
    {% endif %}

.. _crud-generate-urls:
.. _generate-admin-urls:

Generating Admin URLs
---------------------

When using :ref:`pretty admin URLs <pretty-admin-urls>`, EasyAdmin generates
one route per each CRUD action of each :doc:`dashboard </dashboards>`. You can
list them all with the following command:

.. code-block:: terminal

    $ php bin/console debug:router

If you don't see some or any of your admin routes, clear the cache of your
Symfony application so the EasyAdmin route loader can generate them again:

.. code-block:: terminal

    $ php bin/console cache:clear

You can use any of these routes to generate the admin URLs thanks to the
`utilities provided by Symfony to generate URLs`_::

    // redirecting to an admin URL inside a controller
    return $this->redirectToRoute('admin_product_new');

    // generating an admin URL inside a service
    $userProfileUrl = $this->router->generate('admin_user_detail', [
        'entityId' => $user->getId(),
    ]);

    // generating an admin URL in a Twig template
    <a href="{{ ea_url().setRoute('admin_blog_post_edit', {entityId: post.id}) }}">Edit Blog Post</a>

Building Admin URLs
~~~~~~~~~~~~~~~~~~~

If you don't use :ref:`pretty admin URLs <pretty-admin-urls>` or if you need to
build routes dynamically, you can use the ``AdminUrlGenerator`` provided by
EasyAdmin to build the admin URLs.

When generating a URL this way, you don't start from scratch. EasyAdmin reuses all
the query parameters existing in the current request. This is done on purpose because
generating new URLs based on the current URL is the most common scenario. Use
the ``unsetAll()`` method to remove all existing query parameters::

    namespace App\Controller\Admin;

    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
    use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

    class SomeCrudController extends AbstractCrudController
    {
        private $adminUrlGenerator;

        public function __construct(AdminUrlGenerator $adminUrlGenerator)
        {
            $this->adminUrlGenerator = $adminUrlGenerator;
        }

        // ...

        public function someMethod()
        {
            // instead of injecting the AdminUrlGenerator service in the constructor,
            // you can also get it from inside a controller action as follows:
            // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

            // the existing query parameters are maintained, so you only
            // have to pass the values you want to change.
            $url = $this->adminUrlGenerator->set('page', 2)->generateUrl();

            // you can remove existing parameters
            $url = $this->adminUrlGenerator->unset('menuIndex')->generateUrl();
            $url = $this->adminUrlGenerator->unsetAll()->set('foo', 'someValue')->generateUrl();

            // the URL builder provides shortcuts for the most common parameters
            $url = $this->adminUrlGenerator
                ->setController(SomeCrudController::class)
                ->setAction('theActionName')
                ->generateUrl();

            // ...
        }
    }

.. tip::

    If you need to deal with the admin URLs manually for any reason, the names
    of the query string parameters are defined as constants in the
    :class:`EasyCorp\\Bundle\\EasyAdminBundle\\Config\\Option\\EA` class.

.. _ea-url-function:

The exact same features are available in templates thanks to the ``ea_url()``
Twig function. In templates you can omit the call to the ``generateUrl()``
method (it will be called automatically for you):

.. code-block:: twig

    {# both are equivalent #}
    {% set url = ea_url({ page: 2 }).generateUrl() %}
    {% set url = ea_url({ page: 2 }) %}

    {% set url = ea_url().set('page', 2) %}

    {% set url = ea_url()
        .setController('App\\Controller\\Admin\\SomeCrudController')
        .setAction('theActionName') %}

Generating CRUD URLs from outside EasyAdmin
...........................................

When generating URLs of EasyAdmin pages from outside EasyAdmin (e.g. from a
regular Symfony controller) the :ref:`admin context variable <admin-context>`
is not available. That's why you must always set the CRUD controller associated
to the URL. If you have more than one dashboard, you must also set the Dashboard::

    use App\Controller\Admin\DashboardController;
    use App\Controller\Admin\ProductCrudController;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
    use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    class SomeSymfonyController extends AbstractController
    {
        private $adminUrlGenerator;

        public function __construct(AdminUrlGenerator $adminUrlGenerator)
        {
            $this->adminUrlGenerator = $adminUrlGenerator;
        }

        public function someMethod()
        {
            // if your application only contains one Dashboard, it's enough
            // to define the controller related to this URL
            $url = $this->adminUrlGenerator
                ->setController(ProductCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl();

            // in applications containing more than one Dashboard, you must also
            // define the Dashboard associated to the URL
            $url = $this->adminUrlGenerator
                ->setDashboard(DashboardController::class)
                ->setController(ProductCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl();

            // some actions may require to pass additional parameters
            $url = $this->adminUrlGenerator
                ->setController(ProductCrudController::class)
                ->setAction(Action::EDIT)
                ->setEntityId($product->getId())
                ->generateUrl();

            // ...
        }
    }

The same applies to URLs generated in Twig templates:

.. code-block:: twig

    {# if your application defines only one Dashboard #}
    {% set url = ea_url()
        .setController('App\\Controller\\Admin\\ProductCrudController')
        .setAction('index') %}
    {# if you prefer PHP constants, use this:
       .setAction(constant('EasyCorp\\Bundle\\EasyAdminBundle\\Config\\Action::INDEX')) #}

    {# if your application defines multiple Dashboards #}
    {% set url = ea_url()
        .setDashboard('App\\Controller\\Admin\\DashboardController')
        .setController('App\\Controller\\Admin\\ProductCrudController')
        .setAction('index') %}

    {# some actions may require to pass additional parameters #}
    {% set url = ea_url()
        .setController('App\\Controller\\Admin\\ProductCrudController')
        .setAction('edit')
        .setEntityId(product.id) %}

.. _`Symfony controllers`: https://symfony.com/doc/current/controller.html
.. _`Doctrine filters`: https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/filters.html
.. _`XSS attacks`: https://en.wikipedia.org/wiki/Cross-site_scripting
.. _`HtmlSanitizer component`: https://symfony.com/components/HTML%20Sanitizer
.. _`utilities provided by Symfony to generate URLs`: https://symfony.com/doc/current/routing.html#generating-urls
