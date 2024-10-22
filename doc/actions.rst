Actions
=======

**Actions** are each of the tasks that you can perform on CRUD pages. In the
``index``  page for example, you have tasks to "edit" and "delete" each entity
displayed in the listing and you have another task to "create" a new entity.

Actions are configured in the ``configureActions()`` method of your
:doc:`dashboard </dashboards>` or :doc:`CRUD controller </crud>`::

    namespace App\Controller\Admin;

    use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

    class ProductCrudController extends AbstractCrudController
    {
        // ...

        public function configureActions(Actions $actions): Actions
        {
            // ...
        }
    }

Action Names and Constants
--------------------------

Some methods require as argument the name of some action. In addition to plain
strings with the action names (``'index'``, ``'detail'``, ``'edit'``, etc.) you
can also use constants for these values: ``Action::INDEX``, ``Action::DETAIL``,
``Action::EDIT``, etc. (they are defined in the ``EasyCorp\Bundle\EasyAdminBundle\Config\Action`` class).

Built-in Actions
----------------

These are the built-in actions included by default in each page:

* Page ``Crud::PAGE_INDEX`` (``'index'``):

  * Added by default globally: ``Action::NEW``
  * Added by default per entry: ``Action::EDIT``, ``Action::DELETE``
  * Other available actions per entry: ``Action::DETAIL``

* Page ``Crud::PAGE_DETAIL`` (``'detail'``):

  * Added by default: ``Action::EDIT``, ``Action::DELETE``, ``Action::INDEX``
  * Other available actions: -

* Page ``Crud::PAGE_EDIT`` (``'edit'``):

  * Added by default: ``Action::SAVE_AND_RETURN``, ``Action::SAVE_AND_CONTINUE``
  * Other available actions: ``Action::DELETE``, ``Action::DETAIL``, ``Action::INDEX``

* Page ``Crud::PAGE_NEW`` (``'new'``):

  * Added by default: ``Action::SAVE_AND_RETURN``, ``Action::SAVE_AND_ADD_ANOTHER``
  * Other available actions: ``Action::SAVE_AND_CONTINUE``, ``Action::INDEX``

Adding Actions
--------------

Use the ``add()`` method to add any built-in actions and your own custom actions
(which are explained later in this article)::

    use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
        ;
    }

Removing Actions
----------------

Removing actions makes them unavailable in the interface, so the user can't
click on buttons/links to run those actions. However, users can *hack* the URL
to run the action. To fully disable an action, use the ``disable()``
method explained later::

    use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
        ;
    }

Updating an Action
------------------

This is mostly useful to change built-in actions (e.g. to change their icon,
update or remove their label, etc.). The ``update()`` method expects a callable
and EasyAdmin passes the action to it automatically::

    use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setIcon('fa fa-file-alt')->setLabel(false);
            })

            // in PHP 7.4 and newer you can use arrow functions
            // ->update(Crud::PAGE_INDEX, Action::NEW,
            //     fn (Action $action) => $action->setIcon('fa fa-file-alt')->setLabel(false))
        ;
    }

Displaying Actions Conditionally
--------------------------------

Some actions must displayed only when some conditions met. For example, a
"View Invoice" action may be displayed only when the order status is "paid".
Use the ``displayIf()`` method to configure when the action should be visible
to users::

    use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

    public function configureActions(Actions $actions): Actions
    {
        $viewInvoice = Action::new('View Invoice', 'fas fa-file-invoice')
            ->displayIf(static function ($entity) {
                return $entity->isPaid();
            });

            // in PHP 7.4 and newer you can use arrow functions
            // ->displayIf(fn ($entity) => $entity->isPaid())

        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, $viewInvoice);
    }

.. note::

    The ``displayIf()`` method also works for :ref:`global actions <global-actions>`.
    However, your closure won't receive the object that represents the current
    entity because global actions are not associated to any specific entity.

Disabling Actions
-----------------

Disabling an action means that it's not displayed in the interface and the user
can't run the action even if they *hack* the URL. If they try to do that, they
will see a "Forbidden Action" exception.

Actions are disabled globally, you cannot disable them per page::

    use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            // this will forbid to create or delete entities in the backend
            ->disable(Action::NEW, Action::DELETE)
        ;
    }

Restricting Actions
-------------------

Instead of disabling actions, you can restrict their execution to certain users.
Use the ``setPermission()`` to define the Symfony Security permission needed to
view and run some action.

Permissions are defined globally; you cannot define different permissions per page::

    use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
        ;
    }

Reordering Actions
------------------

Use the ``reorder()`` to define the order in which actions are displayed
in some page::

    use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...

            // you can reorder built-in actions...
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::DELETE, Action::EDIT])

            // ...and your own custom actions too
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, 'viewInvoice', Action::DELETE, Action::EDIT])

            // you can pass only a few actions to this method and the rest of actions
            // will be appended in their original order. In the following example, the
            // DELETE and EDIT actions are missing but they will be added automatically
            // after DETAIL and 'viewInvoice' actions
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, 'viewInvoice'])
        ;
    }

Dropdown and Inline Entity Actions
----------------------------------

In the ``index`` page, the entity actions (``edit``, ``delete``, etc.) are
displayed by default in a dropdown. This is done to better display the field
contents on each row. If you prefer to display all the actions *inlined*
(that is, without a dropdown) use the ``showEntityActionsInlined()`` method::

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
                ->showEntityActionsInlined()
            ;
        }
    }

.. _actions-custom:

Adding Custom Actions
---------------------

In addition to the built-in actions provided by EasyAdmin, you can create your
own actions. First, define the basics of your action (name, label, icon) with
the ``Action`` class constructor::

    // the only mandatory argument is the internal name of the action (which is
    // used to add the action to some pages, to reorder the action position, etc.)
    $viewInvoice = Action::new('viewInvoice');

    // the second optional argument is the label visible to end users
    $viewInvoice = Action::new('viewInvoice', 'Invoice');
    // not defining the label explicitly or setting it to NULL means
    // that the label is autogenerated from the name (e.g. 'viewInvoice' -> 'View Invoice')
    $viewInvoice = Action::new('viewInvoice', null);
    // set the label to FALSE to not display any label for this action (but make sure
    // to display an icon for the action; otherwise users can't see or click on the action)
    $viewInvoice = Action::new('viewInvoice', false);

    // the third optional argument is the full CSS class of a FontAwesome icon
    // see https://fontawesome.com/v6/search?m=free
    $viewInvoice = Action::new('viewInvoice', 'Invoice', 'fa fa-file-invoice');

Then you can configure the basic HTML/CSS attributes of the button/element
that will represent the action::

    $viewInvoice = Action::new('viewInvoice', 'Invoice', 'fa fa-file-invoice')
        // renders the action as a <a> HTML element
        ->displayAsLink()
        // renders the action as a <button> HTML element
        ->displayAsButton()
        // a key-value array of attributes to add to the HTML element
        ->setHtmlAttributes(['data-foo' => 'bar', 'target' => '_blank'])
        // removes all existing CSS classes of the action and sets
        // the given value as the CSS class of the HTML element
        ->setCssClass('btn btn-primary action-foo')
        // adds the given value to the existing CSS classes of the action (this is
        // useful when customizing a built-in action, which already has CSS classes)
        ->addCssClass('some-custom-css-class text-danger')

.. note::

    When using ``setCssClass()`` or ``addCssClass()`` methods, the action loses
    the default CSS classes applied by EasyAdmin (``.btn`` and
    ``.action-<the-action-name>``). You might want to add those CSS classes
    manually to make your actions look as expected.

Once you've configured the basics, use one of the following methods to define
which method is executed when clicking on the action:

* ``linkToCrudAction()``: to execute some method of the current CRUD controller;
* ``linkToRoute()``: to execute some regular Symfony controller via its route;
* ``linkToUrl()``: to visit an external URL (useful when your action is not
  served by your application).

The following example shows all kinds of actions in practice::

    namespace App\Controller\Admin;

    use App\Entity\Invoice;
    use App\Entity\Order;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

    class OrderCrudController extends AbstractCrudController
    {
        // ...

        public function configureActions(Actions $actions): Actions
        {
            // this action executes the 'renderInvoice()' method of the current CRUD controller
            $viewInvoice = Action::new('viewInvoice', 'Invoice', 'fa fa-file-invoice')
                ->linkToCrudAction('renderInvoice');

            // if the method is not defined in a CRUD controller, link to its route
            $sendInvoice = Action::new('sendInvoice', 'Send invoice', 'fa fa-envelope')
                // if the route needs parameters, you can define them:
                // 1) using an array
                ->linkToRoute('invoice_send', [
                    'send_at' => (new \DateTime('+ 10 minutes'))->format('YmdHis'),
                ])

                // 2) using a callable (useful if parameters depend on the entity instance)
                // (the type-hint of the function argument is optional but useful)
                ->linkToRoute('invoice_send', function (Order $order): array {
                    return [
                        'uuid' => $order->getId(),
                        'method' => $order->getUser()->getPreferredSendingMethod(),
                    ];
                });

            // this action points to the invoice on Stripe application
            $viewStripeInvoice = Action::new('viewInvoice', 'Invoice', 'fa fa-file-invoice')
                ->linkToUrl(function (Order $entity) {
                    return 'https://www.stripe.com/invoice/'.$entity->getStripeReference();
                });

            return $actions
                // ...
                ->add(Crud::PAGE_DETAIL, $viewInvoice)
                ->add(Crud::PAGE_DETAIL, $sendInvoice)
                ->add(Crud::PAGE_DETAIL, $viewStripeInvoice)
            ;
        }

        public function renderInvoice(AdminContext $context)
        {
            $order = $context->getEntity()->getInstance();

            // add your logic here...
        }
    }

.. _global-actions:

Global Actions
--------------

On pages that list entries (e.g. ``Crud::PAGE_INDEX``) you can configure actions
per entry as well as global actions. Global actions are displayed above the
listed entries.

An example of creating a custom action and adding it globally to the ``index``
page::

    $goToStripe = Action::new('goToStripe')
        ->linkToUrl('https://www.stripe.com/')
        ->createAsGlobalAction()
    ;

    $actions->add(Crud::PAGE_INDEX, $goToStripe);

Batch Actions
-------------

Batch actions are a special kind of action which is applied to multiple items at
the same time. They are only available in the ``index`` page.

Imagine that you manage users with a ``User`` entity and a common task is to
approve their sign ups. Instead of creating a normal ``approve`` action as
explained in the previous sections, create a batch action to be more productive
and approve multiple users at once.

First, add it to your action configuration using the ``addBatchAction()`` method::

    namespace App\Controller\Admin;

    use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
    use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

    class UserCrudController extends AbstractCrudController
    {
        // ...

        public function configureActions(Actions $actions): Actions
        {
            return $actions
                // ...
                ->addBatchAction(Action::new('approve', 'Approve Users')
                    ->linkToCrudAction('approveUsers')
                    ->addCssClass('btn btn-primary')
                    ->setIcon('fa fa-user-check'))
            ;
        }
    }

Batch actions support the same configuration options as the other actions and
they can link to a CRUD controller method, to a Symfony route or to some URL.
If there's at least one batch action, the backend interface is updated to add some
"checkboxes" that allow selecting more than one row of the index listing.

When the user clicks on the batch action link/button, a form is submitted using
the ``POST`` method to the action or route configured in the action. The easiest
way to get the submitted data is to type-hint some argument of your batch action
method with the ``EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto`` class.
If you do that, EasyAdmin will inject a DTO with all the batch action data::

    namespace App\Controller\Admin;

    use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
    use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;

    class UserCrudController extends AbstractCrudController
    {
        // ...

        public function approveUsers(BatchActionDto $batchActionDto)
        {
            $className = $batchActionDto->getEntityFqcn();
            $entityManager = $this->container->get('doctrine')->getManagerForClass($className);
            foreach ($batchActionDto->getEntityIds() as $id) {
                $user = $entityManager->find($className, $id);
                $user->approve();
            }

            $entityManager->flush();

            return $this->redirect($batchActionDto->getReferrerUrl());
        }
    }

.. note::

    As an alternative, instead of injecting the ``BatchActionDto`` variable, you can
    also inject Symfony's ``Request`` object to get all the raw submitted batch data
    (e.g. ``$request->request->all('batchActionEntityIds')``).

.. _actions-integrating-symfony:

Integrating Symfony Actions
---------------------------

If the action logic is small and directly related to the backend, it's OK to add
it to the :doc:`CRUD controller </crud>`, because that simplifies a lot its
integration in EasyAdmin. However, sometimes you have some logic that it's too
complex or used in other parts of the Symfony application, so you can't move it
to the CRUD controller. This section explains how to integrate an existing Symfony
action in EasyAdmin so you can reuse the backend layout, menu and other features.

Imagine that your Symfony application has an action to calculate some business
stats about your clients (average order amount, yearly number of purchases, etc.)
All this is calculated in a ``BusinessStatsCalculator`` service, so you can't
create a CRUD controller to display that information. Instead, create a normal
Symfony controller called ``BusinessStatsController``::

    // src/Controller/Admin/BusinessStatsController.php
    namespace App\Controller\Admin;

    use App\Stats\BusinessStatsCalculator;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Routing\Attribute\Route;
    use Symfony\Component\Security\Http\Attribute\IsGranted;

    #[IsGranted('ROLE_ADMIN')]
    class BusinessStatsController extends AbstractController
    {
        public function __construct(BusinessStatsCalculator $businessStatsCalculator)
        {
            $this->businessStatsCalculator = $businessStatsCalculator;
        }

        #[Route("/admin/business-stats", name="admin_business_stats")]
        public function index()
        {
            return $this->render('admin/business_stats/index.html.twig', [
                'data' => $this->businessStatsCalculator->getStatsSummary(),
            ]);
        }

        #[Route("/admin/business-stats/{id}", name="admin_business_stats_customer")]
        public function customer(Customer $customer)
        {
            return $this->render('admin/business_stats/customer.html.twig', [
                'data' => $this->businessStatsCalculator->getCustomerStats($customer),
            ]);
        }
    }

This is a normal Symfony controller (it doesn't extend any EasyAdmin class) with
some logic which renders the result in Twig templates (which will be shown later).
The first step to integrate this into your EasyAdmin backend is to add it to the
main menu using the ``configureMenuItems()`` method::

    // src/Controller/Admin/DashboardController.php
    namespace App\Controller\Admin;

    use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
    use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

    class DashboardController extends AbstractDashboardController
    {
        // ...

        public function configureMenuItems(): iterable
        {
            // ...

            yield MenuItem::linktoRoute('Stats', 'fa fa-chart-bar', 'admin_business_stats');
        }
    }

If you reload your backend and click on that new menu item, you'll see an error
because the templates used by the BusinessStatsController are not created yet.
Check out the URL of the page and you'll see the trick used by EasyAdmin to
integrate Symfony actions.

Instead of the expected ``/admin/business-stats`` clean URL, the generated URL
is ``/admin?menuIndex=...&submenuIndex=...&routeName=admin_business_stats``.
This is an admin URL, so EasyAdmin can create the :ref:`admin context <admin-context>`,
load the appropriate menu, etc. However, thanks to the ``routeName`` query string
parameter, EasyAdmin knows that it must forward the request to the Symfony
controller that serves that route, and does that transparently to you.

.. note::

    Handling route parameters in this way is fine in most situations. However,
    sometimes you need to handle route arguments as proper Symfony route arguments.
    For example, if you want to pass the ``_switch_user`` query parameter for
    Symfony's impersonation feature, you can do this::

        // you can generate the full URL with Symfony's URL generator:
        $impersonate = Action::new('impersonate')->linkToUrl(
            $urlGenerator->generate('admin', ['_switch_user' => 'user@example.com'], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        // or you can add the query string parameter directly:
        $impersonate = Action::new('impersonate')
            ->linkToRoute('some_route')
            ->setQueryParameter('_switch_user', 'user@example.com');

Now, create the template used by the ``index()`` method, which lists a summary
of the stats of all customers and includes a link to the detailed stats of each
of them:

.. code-block:: twig

    {# templates/admin/business_stats/index.html.twig #}
    {% extends '@EasyAdmin/page/content.html.twig' %}

    {% block content_title 'Business Stats' %}
    {% block main %}
        <table>
            <thead> {# ... #} </thead>
            <tbody>
                {% for customer_data in data %}
                    <tr>
                        {# ... #}

                        <td>
                            <a href="{{ ea_url().setRoute('admin_business_stats_customer', { id: customer_data.id }) }}">
                                View Details
                            </a>
                        </td>
                    </tr>
                {% endfor %}
            </tbody>
        </table>
    {% endblock %}

The Twig template extends the :ref:`content page template <content_page_template>`
provided by EasyAdmin to reuse all the backend design. The rest of the template
is normal Twig code, except for the URL generation. Instead of using Symfony's
``path()`` function, you must use the :ref:`ea_url() function <ea-url-function>`
and pass the Symfony route name and parameter.

Similar to what happened before, the generated URL is not the expected
``/admin/business-stats/5`` but
``/admin?routeName=admin_business_stats_customer&routeParams%5Bid%5D=5``.
But that's fine. EasyAdmin will run the ``customer()`` method of your
BusinessStatsController, so you can render another Twig template with the
customer stats.

Generating URLs to Symfony Actions Integrated in EasyAdmin
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

As explained in detail in the previous section, when integrating a Symfony
action in an EasyAdmin backend, you need to generate URLs a bit differently.
Instead of using Symfony's UrlGenerator service or the ``$this->generateUrl()``
shortcut in a controller, you must use the AdminUrlGenerator service provided
by EasyAdmin::

    // src/Controller/SomeController.php
    namespace App\Controller;

    use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    class SomeController extends AbstractController
    {
        private $adminUrlGenerator;

        public function __construct(AdminUrlGenerator $adminUrlGenerator)
        {
            $this->adminUrlGenerator = $adminUrlGenerator;
        }

        public function someMethod()
        {
            $url = $this->adminUrlGenerator->setRoute('admin_business_stats_customer', [
                'id' => $this->getUser()->getId(),
            ])->generateUrl();

            // ...
        }
    }

