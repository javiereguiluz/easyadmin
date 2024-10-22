Tests
=====

As EasyAdmin is based on Symfony, you can add functional tests for the admin pages
extending the ``WebTestCase`` class and using the `Symfony functional testing workflow`_ .

However, as EasyAdmin uses specific defined ways of displaying the data in its
CRUD pages, a custom test class is provided: ``AbstractCrudTestCase``. The
class is based on traits which defines custom asserts and helpers:

#. `Functional Test Case Example`_
#. `Url Generation`_
#. `Asserts`_
#. `Selector Helpers`_


Functional Test Case Example
----------------------------

Suppose you have a :doc:`Dashboard </dashboards>` named ``App\Controller\Admin\AppDashboardController``
and a ``Category`` :doc:`Crud Controller </crud>` named ``App\Controller\Admin\CategoryCrudController``.
Here's an example of a functional test class for that controller.

First, your test class need to extend the ``AbstractCrudTestCase``::

    # tests/Admin/Controller/CategoryCrudControllerTest.php
    namespace App\Tests\Admin\Controller;

    use App\Controller\Admin\AppDashboardController;
    use App\Controller\Admin\CategoryCrudController;
    use EasyCorp\Bundle\EasyAdminBundle\Test\AbstractCrudTestCase;

    final class CategoryCrudControllerTest extends AbstractCrudTestCase
    {
        protected function getControllerFqcn(): string
        {
            return CategoryCrudController::class;
        }

        protected function getDashboardFqcn(): string
        {
            return AppDashboardController::class;
        }

        public function testIndexPage(): void
        {
            // this examples doesn't use security; in your application you may
            // need to ensure that the user is logged before the test
            $this->client->request("GET", $this->generateIndexUrl());
            static::assertResponseIsSuccessful();
        }
    }

URL Generation
--------------

Used by the ``AbstractCrudTestCase``, ``CrudTestUrlGeneration`` is an
URL generation trait which helps to generate the specific of the EasyAdmin
URLs.

.. note::

    The trait can be used on its own but, in that case, the class that is using
    it needs either:

    * to define the two functions ``getControllerFqcn()`` and ``getDashboardFqcn()``
    * to add the DashboardFqcn (class name) and ControllerFqcn (class name) as
      input to the URL generation functions

Here is the list of the URL generation functions that are all providing URL
based on provided Dashboard and Controller class names:

* ``getCrudUrl()``: is the main function that allows for a complete generation
  with all possible options;
* ``generateIndexUrl()``: generates the URL for the index page (based on the
  Dashboard and Controller defined);
* ``generateNewFormUrl()``: generates the URL for the New form page (based on
  the Dashboard and Controller defined);
* ``generateEditFormUrl()``: generates the URL for the Edit form page of a
  specific entity (based on the Dashboard and Controller defined and the entity ID);
* ``generateDetailUrl()``: generates the URL for the Detail page of a specific
  entity (based on the Dashboard and Controller defined and the entity ID);
* ``generateFilterRenderUrl()``: generates the URL to get the rendering of the
  filters (based on the Dashboard and Controller defined).

Asserts
-------

Used by the ``AbstractCrudTestCase``, are two traits filled with specific
asserts for EasyAdmin web testing:

* ``CrudTestIndexAsserts``: providing asserts for the index page of EasyAdmin;
* ``CrudTestFormAsserts`` : providing asserts for the form page of EasyAdmin.

.. note:: 

    The trait can be used on its own but, in that case, the class that is using
    it needs both:

    * a class property ``client``: instance of ``Symfony\Bundle\FrameworkBundle\KernelBrowser``
    * a class property ``entitytManager``: instance of ``Doctrine\ORM\EntityManagerInterface``

CrudTestIndexAsserts
~~~~~~~~~~~~~~~~~~~~

As EasyAdmin uses specific layout, the goal of these asserts is to ease the way
you're testing your EasyAdmin backend by providing specific asserts linked to
the index page.

The following asserts are provided:

* ``assertIndexFullEntityCount()``
* ``assertIndexPageEntityCount()``
* ``assertIndexPagesCount()``
* ``assertIndexEntityActionExists()``
* ``assertIndexEntityActionNotExists()``
* ``assertIndexEntityActionTextSame()``
* ``assertIndexEntityActionNotTextSame()``
* ``assertGlobalActionExists()``
* ``assertGlobalActionNotExists()``
* ``assertGlobalActionDisplays()``
* ``assertGlobalActionNotDisplays()``
* ``assertIndexColumnExists()``
* ``assertIndexColumnNotExists()``
* ``assertIndexColumnHeaderContains()``
* ``assertIndexColumnHeaderNotContains()``

CrudTestFormAsserts
~~~~~~~~~~~~~~~~~~~

As EasyAdmin uses specific layout, the goal of these asserts is to ease the way
you're testing your EasyAdmin backend by providing specific asserts linked to
the **form** (new or edit) page.

The following asserts are provided:

* ``assertFormFieldExists()``
* ``assertFormFieldNotExists()``
* ``assertFormFieldHasLabel()``
* ``assertFormFieldNotHasLabel()``

Selector Helpers
----------------

Used by the Asserts to locate elements, the Trait ``CrudTestSelectors`` is
defining a specific amounts of selector helpers linked to the specifics of
EasyAdmin layout.

.. note::

    The trait can be used on its own. It only defines selector strings.

The following helpers are provided:

* ``getActionSelector()``
* ``getGlobalActionSelector()``
* ``getIndexEntityActionSelector()``
* ``getIndexEntityRowSelector()``
* ``getIndexColumnSelector()``
* ``getIndexHeaderColumnSelector()``
* ``getIndexHeaderRowSelector()``
* ``getFormEntity()``
* ``getEntityFormSelector()``
* ``getFormFieldIdValue()``
* ``getFormFieldSelector()``
* ``getFormFieldLabelSelector()``

.. _`Symfony functional testing workflow`: https://symfony.com/doc/current/testing.html#application-tests
