<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Entity\Bill;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Entity\Customer;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Entity\Page;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Entity\Website;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 30; ++$i) {
            $category = (new Category())
                ->setName('Category '.$i)
                ->setSlug('category-'.$i);

            $this->addReference('category'.$i, $category);
            $manager->persist($category);
        }

        for ($i = 0; $i < 5; ++$i) {
            $user = (new User())
                ->setName('User '.$i)
                ->setEmail('user'.$i.'@example.com');

            $this->addReference('user'.$i, $user);
            $manager->persist($user);
        }

        for ($i = 0; $i < 20; ++$i) {
            $blogPost = (new BlogPost())
                ->setTitle('Blog Post '.$i)
                ->setSlug('blog-post-'.$i)
                ->setContent('Lorem Ipsum Dolor Sit Amet.')
                ->setCreatedAt(new \DateTimeImmutable('2020-11-'.($i + 1).' 09:00:00'))
                ->setPublishedAt(new \DateTimeImmutable('2020-11-'.($i + 1).' 11:00:00'))
                ->addCategory($this->getReference('category'.($i % 10), Category::class))
                ->setAuthor($this->getReference('user'.($i % 5), User::class));

            if ($i < 10) {
                $blogPost->setPublisher(
                    $this->getReference('user'.(($i + 1) % 5), User::class)
                );
            }

            $manager->persist($blogPost);
        }

        $this->addAssociationFixtures($manager);

        $manager->flush();
    }

    private function addAssociationFixtures(ObjectManager $manager)
    {
        // Customer <-Many-To-Many-> Bill

        // Add 10 Bills
        for ($i = 0; $i < 10; ++$i) {
            $bill = (new Bill())
                ->setName('Bill '.$i);

            $this->addReference('bill'.$i, $bill);
            $manager->persist($bill);
        }

        // Pregenerated random amount of elements for each Bill
        $manyToManyAmount = [3, 0, 0, 1, 4, 0, 2, 2, 1, 1];

        // Amount of elements is the sum of the manyToManyAmount array (Generation was implemented in a way that does not include duplicates)
        $manyToManyMapping = [6, 5, 6, 3, 1, 7, 2, 9, 4, 6, 2, 6, 1, 4, 0, 2, 4, 1, 6, 0, 9, 6, 7, 9, 8, 5, 2, 4, 9, 4, 0, 8, 7, 1, 3, 2, 1, 8, 4, 9, 7, 5, 3, 0, 6];
        $manyToManyIndex = 0;

        // Add 10 Customer
        for ($i = 0; $i < 10; ++$i) {
            $customer = (new Customer())
                ->setName('Customer '.$i);

            $amount = $manyToManyAmount[$i];

            if ($amount > 0) {
                for ($j = 0; $j < $amount; ++$j) {
                    $customer->addBill(
                        $this->getReference('bill'.$manyToManyMapping[$manyToManyIndex++], Bill::class)
                    );
                }
            }

            $this->addReference('customer'.$i, $customer);
            $manager->persist($customer);
        }

        // Page <-Many-To-One-> Website

        // Add 10 Page
        for ($i = 0; $i < 10; ++$i) {
            $page = (new Page())
                ->setName('Page '.$i);

            $this->addReference('page'.$i, $page);
            $manager->persist($page);
        }

        // Pregenerated random amount of elements for each Website
        $oneToManyAmount = [4, 1, 2, 5, 4, 4, 0, 4, 4, 3];

        // Amount of elements is the sum of the oneToManyAmount array (Generation was implemented in a way that does not include duplicates)
        $oneToManyMapping = [3, 8, 0, 7, 4, 0, 5, 2, 0, 1, 0, 8, 2, 4, 3, 3, 2, 5, 4, 7, 9, 2, 0, 1, 9, 6, 8, 5, 2, 9, 5, 0, 6, 1, 3, 8, 6, 9, 2, 0, 4, 8, 3, 7, 1];
        $oneToManyIndex = 0;

        // Add 10 Website
        for ($i = 0; $i < 10; ++$i) {
            $website = (new Website())
                ->setName('Website '.$i);

            $amount = $oneToManyAmount[$i];

            if ($amount > 0) {
                for ($j = 0; $j < $amount; ++$j) {
                    $website->addPage(
                        $this->getReference('page'.$oneToManyMapping[$oneToManyIndex++], Page::class)
                    );
                }
            }

            $this->addReference('website'.$i, $website);
            $manager->persist($website);
        }
    }
}
