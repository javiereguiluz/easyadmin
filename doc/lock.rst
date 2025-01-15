Object Locking in EasyAdmin
===========================

Object locking prevents conflicts when multiple users edit the same item simultaneously, ensuring data integrity and avoiding overwrites. This feature is essential for environments where multiple administrators manage the same data, such as a back-office application using **EasyAdmin**.

How It Works
------------

When two users try to edit the same entity at the same time, the system uses the `lockVersion` field to detect that one of the users has already made changes. If the second user tries to submit their changes without reloading, EasyAdmin will notify them with a flash message, instructing them to reload the page to see the most recent version of the object.

Use Case Example
----------------

Imagine two users, Alice and Bob, both working on the same product record in the back-office system:

1. **Alice** starts editing a product, say "Product A".
2. **Bob** also starts editing "Product A" at the same time, unaware that Alice is editing it.
3. **Alice** saves her changes, which updates the `lockVersion` in the database.
4. **Bob** tries to submit his changes, but the system detects that the `lockVersion` has changed and warns him that someone else has already modified the product.

This ensures that Bob cannot overwrite Alice’s changes without seeing the most recent version of the data.

Implementation Example
----------------------

1. **Add the `VersionableTrait` to the Entity**

You need to add the `VersionableTrait` to any entity you want to support locking. This trait automatically manages the `lockVersion` field.

Example:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use EasyCorp\Bundle\EasyAdminBundle\Form\Extension\VersionableTrait;

#[ORM\Entity]
class Product
{
    use VersionableTrait; // Enables version locking

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    private string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
```

2. **Implement `LockableInterface`**

Next, implement the `LockableInterface` from EasyAdmin to signal that the entity supports locking.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\LockableInterface;
use EasyCorp\Bundle\EasyAdminBundle\Form\Extension\VersionableTrait;

#[ORM\Entity]
class Product implements LockableInterface
{
    use VersionableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    private string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
```

3. **Handling the Conflict in EasyAdmin**

When a conflict occurs (i.e., if two users are editing the same entity), EasyAdmin will automatically display a message to the second user. The message will inform the user that the entity was modified by someone else and prompt them to reload the page.

```
This record has been modified by another user since you started editing.
Your changes cannot be saved to prevent data loss.
Click here to reload this page and get the latest version.
```

### Summary of Benefits
----------------------

- **Data Integrity**: Ensures that edits from multiple users do not conflict or overwrite each other.
- **User Awareness**: Users are notified in real-time when another user has made changes to the same object.
- **Easy Integration**: By implementing the `LockableInterface` and using the `VersionableTrait`, you can seamlessly add object locking without disrupting existing workflows.

This feature ensures smoother collaboration, reduces the risk of data errors, and provides a better experience for administrators working with shared data in EasyAdmin.

