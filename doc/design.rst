Design
======

The design of the backend is ready for any kind of application. It's been
created with `Bootstrap 5`_, and some custom CSS and JavaScript code; all
managed by `Webpack`_ via Symfony's `Webpack Encore`_.

Like any other Symfony bundle, assets are copied to (or symlinked from) the
``public/bundles/`` directory of your application when installing or updating
the bundle. If this doesn't work for any reason, your backend won't display the
proper CSS/JS styles. In those cases, run this command to install those assets
manually:

.. code-block:: terminal

    # remove the --symlink option if your system doesn't support symbolic links
    $ php bin/console assets:install --symlink

Depending on your needs, there are several ways of customizing the design. Some
of them require pure CSS/JavaScript code and others require overriding and/or
creating new Twig templates.

.. _icon-customization:

Changing the Backend Icons
--------------------------

.. versionadded:: 4.16

    The option to configure the icon set was introduced in EasyAdmin 4.16.0.

By default, EasyAdmin uses `FontAwesome icons`_ both for the built-in interface
icons and any custom icons that you add to menu items, fields, form tabs, etc.
The full FontAwesome icon set (~2,000 icons) is already included in EasyAdmin,
so you don't need to download any of these icons.

If you prefer to use other icons, call the ``useCustomIconSet()`` in your dashboard::

    namespace App\Controller\Admin;

    use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Option\IconSet;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

    class DashboardController extends AbstractDashboardController
    {
        public function configureAssets(): Assets
        {
            return Assets::new()
                ->useCustomIconSet()
            ;
        }

        // ...
    }

Then, whenever you define a custom icon for any EasyAdmin feature, use the full
icon prefix and name (``lucide:map-pin``, ``ic:baseline-calendar-month``, etc.)
that you would typically use in `Symfony UX Icons`_.

If all your icons use a common prefix (e.g. ``tabler:`` when using the `Tabler`_
icons), pass it to the ``useCustomIconSet()`` method::

    return Assets::new()->useCustomIconSet('tabler');

Now, the ``tabler:`` prefix will be added automatically to all your custom icon
names. This way, you can use names like ``user`` and ``file`` instead of
``tabler:user`` and ``tabler:file``.

.. _template-customization:

Modifying Backend Templates
---------------------------

Backend pages are created with multiple Twig templates and fragments. You can
modify them in two ways:

* **Override EasyAdmin templates** using Symfony's mechanism to override templates
  (this is the same for all bundles, not only EasyAdmin);
* **Replace EasyAdmin templates** using EasyAdmin features.

Overriding Templates
~~~~~~~~~~~~~~~~~~~~

.. tip::

    Instead of using Symfony mechanism to override templates, you may consider
    using a similar but more powerful feature provided by EasyAdmin to replace
    templates, as explained in :ref:`the next section <replacing_templates>`.

Following Symfony's mechanism to `override templates from bundles`_, you must
create the ``templates/bundles/EasyAdminBundle/`` directory in your application
and then create new templates with the same path as the original templates.
For example::

    your-project/
    ├─ ...
    └─ templates/
       └─ bundles/
          └─ EasyAdminBundle/
             ├─ layout.html.twig
             ├─ menu.html.twig
             ├─ crud/
             │  ├─ index.html.twig
             │  ├─ detail.html.twig
             │  └─ field/
             │     ├─ country.html.twig
             │     └─ text.html.twig
             ├─ label/
             │  └─ null.html.twig
             └─ page/
                ├─ content.html.twig
                └─ login.html.twig

Instead of creating the new templates from scratch, you can extend from the
original templates and change only the parts you want to override. However, you
must use a special syntax inside ``extends`` to avoid an infinite loop:

.. code-block:: twig

    {# templates/bundles/EasyAdminBundle/layout.html.twig #}

    {# DON'T DO THIS: it will cause an infinite loop #}
    {% extends '@EasyAdmin/layout.html.twig' %}

    {# DO THIS: the '!' symbol tells Symfony to extend from the original template #}
    {% extends '@!EasyAdmin/layout.html.twig' %}

    {% block sidebar %}
        {# ... #}
    {% endblock %}

.. _replacing_templates:

Replacing Templates
~~~~~~~~~~~~~~~~~~~

This option allows you to render certain parts of the backend with your own Twig
templates. First, you can replace some templates globally in the
:doc:`dashboard </dashboards>`::

    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

    class DashboardController extends AbstractDashboardController
    {
        // ...

        public function configureCrud(): Crud
        {
            return Crud::new()
                // ...

                // the first argument is the "template name", which is the same as the
                // Twig path but without the `@EasyAdmin/` prefix
                ->overrideTemplate('label/null', 'admin/labels/my_null_label.html.twig')

                ->overrideTemplates([
                    'crud/index' => 'admin/pages/index.html.twig',
                    'crud/field/textarea' => 'admin/fields/dynamic_textarea.html.twig',
                ])
            ;
        }
    }

You can also replace templates per :doc:`CRUD controller </crud>` (this overrides
any change done in the dashboard)::

    namespace App\Controller\Admin;

    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

    class ProductCrudController extends AbstractCrudController
    {
        // ...

        public function configureCrud(Crud $crud): Crud
        {
            return $crud
                // ...

                ->overrideTemplate('crud/layout', 'admin/advanced_layout.html.twig')

                ->overrideTemplates([
                    'crud/field/text' => 'admin/product/field_id.html.twig',
                    'label/null' => 'admin/labels/null_product.html.twig',
                ])
            ;
        }
    }

Fields And Actions Templates
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Each :doc:`field </fields>` (and each :doc:`action </actions>`) defines a
``setTemplatePath()`` method to set the Twig template used to render that
specific field (or action)::

    TextField::new('...', '...')
        // ...
        ->setTemplatePath('custom_fields/text.html.twig');

    // ...

    Action::new('...', '...')
        // ...
        ->setTemplatePath('admin/actions/my_custom_action.html.twig');

The ``setTemplatePath()`` method only applies to fields displayed on the
``index`` and ``detail`` pages. Read the next section to learn how to customize
fields in the ``new`` and ``edit`` pages, which use Symfony forms.

Form Field Templates
~~~~~~~~~~~~~~~~~~~~

EasyAdmin provides a ready-to-use `form theme`_ based on Bootstrap 5. Dashboards
and CRUD controllers define ``addFormTheme(string $themePath)`` and
``setFormThemes(array $themePaths)`` methods so you can
`customize individual form fields`_ using your own form theme.

Imagine a form field where you want to include a ``<a>`` element that links to
additional information. If the field is called ``title`` and belongs to a
``Product`` entity, the configuration would look like this::

    TextField::new('title')
        // ...
        ->setFormTypeOptions([
            'block_name' => 'custom_title',
        ]);

The next step is to define the template fragment used by that field, which
requires to know the `form fragment naming rules`_ defined by Symfony:

.. code-block:: twig

    {# templates/admin/form.html.twig #}
    {# note that the Twig block name starts with an uppercase letter
       ('_Product_...' instead of '_product_...') because the first part
       of the block name is the unmodified entity name #}
    {% block _Product_custom_title_widget %}
        {# ... #}
        <a href="...">More information</a>
    {% endblock %}

Finally, add this custom theme to the list of themes used to render backend forms::

    namespace App\Controller\Admin;

    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

    class ProductCrudController extends AbstractCrudController
    {
        // ...

        public function configureCrud(Crud $crud): Crud
        {
            return $crud
                // ...

                // don't forget to add EasyAdmin's form theme at the end of the list
                // (otherwise you'll lose all the styles for the rest of form fields)
                ->setFormThemes(['admin/form.html.twig', '@EasyAdmin/crud/form_theme.html.twig'])
            ;
        }
    }

.. note::

    You can also override the form widget by using the original field name.
    In the example above it would look like this:
    ``{% block _Product_title_widget %}``. The full syntax is:
    ``{% block _<Entity name>_<Field name>_widget %}``.

.. _crud-design-custom-web-assets:

Adding Custom Web Assets
------------------------

Use the ``configureAssets()`` method in the :doc:`dashboard </dashboards>` and/or
the :doc:`CRUD controllers </crud>` to add your own CSS and JavaScript files::

    namespace App\Controller\Admin;

    use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

    class ProductCrudController extends AbstractCrudController
    {
        // ...

        public function configureAssets(Assets $assets): Assets
        {
            return $assets
                // imports the given entrypoint defined in the importmap.php file of AssetMapper
                // it's equivalent to adding this inside the <head> element:
                // {{ importmap('admin') }}
                ->addAssetMapperEntry('admin')
                // you can also import multiple entries
                // it's equivalent to calling {{ importmap(['app', 'admin']) }}
                ->addAssetMapperEntry('app', 'admin')

                // adds the CSS and JS assets associated to the given Webpack Encore entry
                // it's equivalent to adding these inside the <head> element:
                // {{ encore_entry_link_tags('...') }} and {{ encore_entry_script_tags('...') }}
                ->addWebpackEncoreEntry('admin-app')

                // it's equivalent to adding this inside the <head> element:
                // <link rel="stylesheet" href="{{ asset('...') }}">
                ->addCssFile('build/admin.css')
                ->addCssFile('https://example.org/css/admin2.css')

                // it's equivalent to adding this inside the <head> element:
                // <script src="{{ asset('...') }}"></script>
                ->addJsFile('build/admin.js')
                ->addJsFile('https://example.org/js/admin2.js')

                // use these generic methods to add any code before </head> or </body>
                // the contents are included "as is" in the rendered page (without escaping them)
                ->addHtmlContentToHead('<link rel="dns-prefetch" href="https://assets.example.com">')
                ->addHtmlContentToBody('<script> ... </script>')
                ->addHtmlContentToBody('<!-- generated at '.time().' -->')
            ;
        }
    }

If you need to customize the HTML attributes or other features of the ``<link>``
and ``<script>`` tags, pass an ``Asset`` object to the ``addCssFile()``,
``addJsFile()`` and ``addWebpackEncoreEntry()`` methods::

    use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
    // ...

    return $assets
        ->addCssFile(Asset::new('build/admin.css')->preload()->nopush())
        ->addCssFile(Asset::new('build/admin-print.css')->htmlAttr('media', 'print'))

        ->addJsFile(Asset::new('build/admin.js')->defer())
        ->addJsFile(Asset::new('build/admin.js')->preload())
        ->addJsFile(Asset::new('build/admin.js')->htmlAttr('referrerpolicy', 'strict-origin'))

        ->addWebpackEncoreEntry(Asset::new('admin-app')->webpackEntrypointName('...'))

        // adding full Asset objects for AssetMapper entries work too, but it's
        // useless because entries can't define any property, only their name
        ->addAssetMapperEntry(Asset::new('admin'))

        ->addCssFile(Asset::new('build/admin-detail.css')->onlyOnDetail())
        ->addJsFile(Asset::new('build/admin.js')->onlyWhenCreating())
        ->addWebpackEncoreEntry(Asset::new('admin-app')->ignoreOnForm())

        // you can also define the Symfony Asset package which the asset belongs to
        ->addCssFile(Asset::new('some-path/foo.css')->package('legacy_assets'))
    ;

.. tip::

    :doc:`Fields </fields>` can also add CSS and JavaScript assets to the
    rendered pages. :ref:`Read this section <custom-fields>` to learn how.

.. note::

    If you want to unload the default assets included by EasyAdmin, override the
    default ``layout.html.twig`` template and empty the ``head_stylesheets`` and
    ``head_javascript`` Twig blocks.

Customizing the Backend Design
------------------------------

The design of the backend is created with lots of CSS variables. This makes it
easier to customize it to your own needs. You'll find all variables in the
``vendor/easycorp/easyadmin-bundle/assets/css/easyadmin-theme/variables-theme.scss`` file.
To override any of them, create a CSS file and redefine the variable values:

.. code-block:: text

    /* public/css/admin.css */
    :root {
        /* make the backend contents as wide as the browser window */
        --body-max-width: 100%;
        /* change the background color of the <body> */
        --body-bg: #f5f5f5;
        /* make the base font size smaller */
        --font-size-base: 13px;
        /* remove all border radius to make corners straight */
        --border-radius: 0px;
    }

Then, load this CSS file in your dashboard and/or resource admin::

    use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

    class DashboardController extends AbstractDashboardController
    {
        // ...

        public function configureAssets(): Assets
        {
            return Assets::new()->addCssFile('css/admin.css');
        }
    }

.. note::

    Because of how Bootstrap styles are defined, it's not possible to use CSS
    variables to override every style. Sometimes you may need to also override
    the value of some `Sass`_ variables (which are defined in the
    ``assets/css/easyadmin-theme/variables-bootstrap.scss`` file).

CSS Selectors
~~~~~~~~~~~~~

The ``<body>`` element of every backend page includes different ``id`` and ``class``
attributes to help you target your own styles. The ``id`` follows this pattern:

==========  ==============================================
Page        ``<body>`` ID attribute
==========  ==============================================
``detail``  ``ea-detail-<entity_name>-<entity_id>``
``edit``    ``ea-edit-<entity_name>-<entity_id>``
``index``   ``ea-index-<entity_name>``
``new``     ``ea-new-<entity_name>``
==========  ==============================================

If you are editing for example the element with ``id = 200`` of the ``User`` entity,
the ``<body>`` of that page will be ``<body id="easyadmin-edit-User-200" ...>``.

The pattern of the ``class`` attribute is different because it applies several
CSS classes at the same time:

==========  ============================================
Page        ``<body>`` CSS class
==========  ============================================
``detail``  ``ea-detail ea-detail-<entity_name>``
``edit``    ``ea-edit ea-edit-<entity_name>``
``index``   ``ea-index ea-index-<entity_name>``
``new``     ``ea-new ea-new-<entity_name>``
==========  ============================================

If you are displaying for example the listing of ``User`` entity elements, the
``<body>`` of that page will be ``<body class="ea index index-User" ...>``.

Managing the Backend Assets with Webpack
----------------------------------------

EasyAdmin uses `Webpack`_ (via Symfony's `Webpack Encore`_) to manage its CSS
and JavaScript assets. This bundle provides both the source files and the
compiled versions of all assets, so you don't have to install Webpack to use
this bundle.

However, if you want total control over the backend styles, you can use Webpack
to integrate the SCSS and JavaScript source files provided in the ``assets/``
directory. The only caveat is that EasyAdmin doesn't use Webpack Encore yet when
loading the assets, so you can't use features like versioning. This will be
fixed in future versions.

.. _`Bootstrap 5`: https://github.com/twbs/bootstrap
.. _`Sass`: https://sass-lang.com/
.. _`Webpack`: https://webpack.js.org/
.. _`Webpack Encore`: https://symfony.com/doc/current/frontend.html
.. _`override templates from bundles`: https://symfony.com/doc/current/bundles/override.html#templates
.. _`customize individual form fields`: https://symfony.com/doc/current/form/form_customization.html
.. _`form fragment naming rules`: https://symfony.com/doc/current/form/form_themes.html#form-fragment-naming
.. _`form theme`: https://symfony.com/doc/current/form/form_themes.html
.. _`FontAwesome icons`: https://fontawesome.com/v6/search?m=free
.. _`Symfony UX Icons`: https://symfony.com/bundles/ux-icons/current/index.html
.. _`Tabler`: https://tabler.io/icons
