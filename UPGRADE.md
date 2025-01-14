Upgrade between EasyAdmin 4.x versions
======================================

EasyAdmin 4.22.0
----------------

The `referrerUrl` property and the `getReferrerUrl()` method of `BatchActionDto`
are deprecated. This is similar to the rest of deprecations of features related
to the "referrer URL".

The referrer URL is now handled automatically inside EasyAdmin. In your own
batch actions, you can redirect to a specific URL (built with the `AdminUrlGenerator`)
or get the referrer URL from the HTTP headers provided by browsers:

```php
// Before
return $this->redirect($batchActionDto->getReferrer());

// After
return $this->redirect($adminContext->getRequest()->headers->get('referer'));
```

EasyAdmin 4.20.0
----------------

### Country Flags now Use a Flag Twig Component

Instead of rendering country flags (in `CountryField`) using an `<img>` tag,
they are now rendered as plain `<svg>` files using a Twig component. This change
removes hundreds of lines in our `manifest.json` file and also removes a JavaScript
dependency. Flags still look and work the same as before.

If you used the included country flags in your own templates (which is rare and
not documented) you need to do the following change:

```twig
{# Before #}
<img class="country-flag" height="17" alt="{{ country_name }}" title="{{ country_name }}" src="{{ asset('images/flags/' ~ flag_code ~ '.svg', ea.assets.defaultAssetPackageName) }}">

{# After #}
<twig:ea:Flag countryCode="{{ flag_code }}" height="17" />
```

EasyAdmin 4.18.0
----------------

### Reverted FontAwesome Icon Changes

In EasyAdmin 4.16.0, we introduced a feature allowing the use of custom icon
sets in addition to the default FontAwesome icons. As part of this update,
FontAwesome icons were changed to be rendered as inline SVGs instead of being
applied via CSS classes. **This change has been reverted in this version**,
restoring the previous behavior so you can continue using FontAwesome icons as
before in EasyAdmin.

EasyAdmin 4.17.0
----------------

### Pretty URLs Changed Their Url Patterns

This is a small BC break. When using pretty URLs, the generated URLs used
underscores and they now use dashes and snake case. For example, before an URL
could be `/admin/blog_post/batchDelete` and now it's `/admin/blog-post/batch-delete`

The route names remain the same (e.g. `admin_blog_post_batch_delete`) and you
probably always generate URLs using the route name, so this BC break won't impact you.

EasyAdmin 4.16.0
----------------

### FontAwesome Icons Are Now Inlined as SVGs

**REVERTED** This change was reverted in EasyAdmin 4.18.0. If you use FontAwesome
icons, you don't have to do any change.

EasyAdmin 4.14.0
----------------

### Added Pretty URLs Support

Starting from 4.14.0 version, EasyAdmin includes a custom route loader that
can generate pretty URLs in your backend. Enable this feature by creating the
following routing file in your application:

```yaml
# config/routes/easyadmin.yaml
easyadmin:
    resource: .
    type: easyadmin.routes
```

EasyAdmin 4.11.0
----------------

### Updated the `MenuItemMatcherInterface`

The `MenuItemMatcherInterface` has changed as follows:

  * The `isSelected(MenuItemDto $menuItemDto)` method has been removed
  * The `isExpanded(MenuItemDto $menuItemDto)` method has been removed
  * A new `markSelectedMenuItem(array<MenuItemDto> $menuItems)` method has been added

Read the comments in the code of the `MenuItemMatcher` class to learn about the
new menu item matching logic.

EasyAdmin 4.10.0
----------------

### Updated the Default Title of Detail Page

The default title of the `detail` page in previous versions was `%entity_as_string%`
which is a placeholder that refers to the value returned by the `__toString()`
method of the entity.

This can potentially result in a XSS vulnerability because page titles and other
elements are rendered with the `raw` Twig filter (to allow you to customize the
contents with HTML tags).

Starting from EasyAdmin 4.10.0, the default page title is `%entity_label_singular% <small>(#%entity_short_id%)</small>`,
which only contains safe items that will never result in a XSS issue. If you
want to keep the previous page title (because you don't include user-generated
contents in `__toString()` or because you sanitize all user-submitted data) you
can add the following to your dashboard and all your CRUD controllers will use
that page title:

    class DashboardController extends AbstractDashboardController
    {
        // ...

        public function configureCrud(Crud $crud): Crud
        {
            return $crud
                // ...
                ->setPageTitle('detail', '%entity_as_string%')
            ;
        }
    }

EasyAdmin 4.8.11
----------------

EasyAdmin URLs no longer include the `referrer` query parameter, and the
`AdminContext:getReferrer()` method is deprecated.

This change is part of the long-term project to simplify URLs, with the goal of
using pretty URLs in the future. If you still need to access the referrer, you
can retrieve it from the HTTP headers provided by browsers:

```php
// Before
return $this->redirect($context->getReferrer());

// After
return $this->redirect($context->getRequest()->headers->get('referer'));
```

EasyAdmin 4.8.0
---------------

### Form Panels are now called Form Fieldsets

You can still use `FormField::addPanel()` but it's deprecated and it will be
removed in EasyAdmin 5.0.0. To fix the deprecation, "Find & Replace" in your IDE:

    // Before
    yield FormField::addPanel('...');

    // After
    yield FormField::addFieldset('...');

If your application uses custom advanced features, you might need to change some
other occurrences of "panel" such as CSS styles (`.form-panel` -> `.form-fieldset`)
and form attributes in `CrudFormType` (`$formFieldOptions['ea_form_panel']` ->
`$formFieldOptions['ea_form_fieldset'] = $currentFormFieldset`)

EasyAdmin 4.6.0
---------------

### New formatted value for Country field

This is a backward compatibility break that only affects you if you customize
the default `crud/field/country.html.twig` template or if you use a custom
template fo render `Country` fields.

Starting from this EasyAdmin version, `Country` fields allow to select more
than one value. That's why the type of the formatted value has changed from
`?string` to `?array`. E.g. if the value of your entity property is `ES`;
before, `field.formattedValue` stored the string `'Spain'` and now it stores
the array `['ES' => 'Spain']`.

The country code (used to display the country flag) is now the key of the new
array. Before, you had to use an internal propery called `flagCode` which has
been removed.

EasyAdmin 4.4.0
---------------

### Multilingual dashboards

EasyAdmin now supports multilingual dashboards. First, add the `_locale` parameter
in the URL of your dashboard (e.g. `/admin/{_locale}`) to enable the default
Symfony locale listener that handles the locale switching. If you can't do this,
you'll need to implement your own logic to handle the request locale in a way
compatible with Symfony.

After that, call the `setLocales()` method in the dashboard configuration class,
passing an array of locales that should be exposed in the interface.

EasyAdmin 4.2.0
---------------

### Signature changes

We've changed how translations are managed internally in EasyAdmin. Before we
passed translated contents to templates. Now we pass Symfony's "translatable"
objects to templates.

This means that many classes have been changed to allow using `TranslatableMessage` objects
in places where previously only `string`, `false` or `null` were allowed.
Return types were also loosened to allow returning `TranslatableMessage` where applicable.

In practice this should not affect to most applications because `TranslatableMessage`
objects gracefully transform to strings when needed. However, you might need to
update some checks where you only expected string scalar values and now you might
also get `TranslatableMessage` objects.

Full list of changes in final classes:

    Config\Action (new, setLabel); only docblocks and deprecation logic
    Config\Menu*MenuItem (constructors)
    Config\MenuItem (linkTo*, section, subMenu)
    Dto\ActionDto (getLabel, setLabel and private field)
    Dto\CrudDto (getEntityLabelInSingular, setEntityLabelInSingular,getEntityLabelInPlural, setEntityLabelInPlural, setCustomPageTitle, getHelpMessage, setHelpMessage)
    Dto\FieldDto (getLabel, setLabel, getHelp, setHelp)
    Dto\FilterDto (getLabel, setLabel); only docblocks
    Dto\MenuItemDto (getLabel, setLabel)
    Field*Field (new); only docblocks
    Field\FormField (addPanel, addTab)

List of signature changes in non-final classes and traits:

    Config\Crud (setHelp)
    Field\FieldTrait (setLabel, setHelp); setLabel only in docblock

### New setTranslatableChoices() method in `ChoiceField`

Use this method when defining choice labels with translatable objects. For example:

    yield ChoiceField::new('...')->setTranslatableChoices([
        'paid' => t('Paid Invoice'),
        'pending' => t('Invoice Sent but Unpaid'),
        'refunded' => 'Refunded Invoice', // You can mix strings with TranslatableMessage objects
    ]);

Upgrade between EasyAdmin 4.x versions
======================================

EasyAdmin 4.1.0
---------------

### Updated Country Field Flags

Flags that are optionally displayed in `CountryField` have been redesigned and
updated their format from `.png` to `.svg`. This doesn't require any change in
your application, but if you are using the flag images in your own custom designs,
update the path of the images:

```
# Before
<img alt="Flag of Panama" src="/bundles/easyadmin/images/flags/PA.png">

# After
<img alt="Flag of Panama" src="/bundles/easyadmin/images/flags/PA.svg">
```

### Removed URL signatures

Backend URLs no longer include signatures, because they don't provide any
additional security. The following classes and methods are deprecated:

  * `AdminUrlGenerator::addSignature()` method
  * `AdminUrlGenerator::getSignature()` method
  * `UrlSigner` class and service
  * `Dashboard::disableUrlSignatures()` method

The validity of URL signatures is no longer checked either. If you add signatures
manually, you'll need to check them too.
