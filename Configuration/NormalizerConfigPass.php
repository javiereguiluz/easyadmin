<?php

/*
 * This file is part of the EasyAdminBundle.
 *
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JavierEguiluz\Bundle\EasyAdminBundle\Configuration;

/**
 * Transforms the two simple configuration formats into the full expanded
 * configuration. This allows to reuse the same method to process any of the
 * different configuration formats.
 *
 * These are the two simple formats allowed:
 *
 * # Config format #1: no custom entity name
 * easy_admin:
 *     entities:
 *         - AppBundle\Entity\User
 *
 * # Config format #2: simple config with custom entity name
 * easy_admin:
 *     entities:
 *         User: AppBundle\Entity\User
 *
 * And this is the full expanded configuration syntax generated by this method:
 *
 * # Config format #3: expanded entity configuration with 'class' parameter
 * easy_admin:
 *     entities:
 *         User:
 *             class: AppBundle\Entity\User
 *
 * By default the entity name is used as its label (showed in buttons, the
 * main menu, etc.). That's why the config format #3 can optionally define
 * a custom entity label
 *
 * easy_admin:
 *     entities:
 *         User:
 *             class: AppBundle\Entity\User
 *             label: 'Clients'
 *
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class NormalizerConfigPass implements ConfigPassInterface
{
    public function process(array $backendConfiguration)
    {
        $backendConfiguration = $this->normalizeEntityConfiguration($backendConfiguration);
        $backendConfiguration = $this->normalizeFormViewConfiguration($backendConfiguration);
        $backendConfiguration = $this->normalizeViewConfiguration($backendConfiguration);
        $backendConfiguration = $this->normalizePropertyConfiguration($backendConfiguration);

        return $backendConfiguration;
    }

    private function normalizeEntityConfiguration($backendConfiguration)
    {
        $normalizedConfiguration = array();

        foreach ($backendConfiguration['entities'] as $entityName => $entityConfiguration) {
            // normalize config formats #1 and #2 to use the 'class' option as config format #3
            if (!is_array($entityConfiguration)) {
                $entityConfiguration = array('class' => $entityConfiguration);
            }

            // if config format #3 is used, ensure that it defines the 'class' option
            if (!isset($entityConfiguration['class'])) {
                throw new \RuntimeException(sprintf('The "%s" entity must define its associated Doctrine entity class using the "class" option.', $entityName));
            }

            // if config format #1 is used, the entity name is the numeric index
            // of the configuration array. In this case, autogenerate the entity
            // name using its class name
            if (is_numeric($entityName)) {
                $entityClassParts = explode('\\', $entityConfiguration['class']);
                $entityClassName = end($entityClassParts);
                $entityName = $this->getUniqueEntityName($entityClassName, array_keys($normalizedConfiguration));
            } else {
                // if config format #2 and #3 are used, make sure that the entity
                // name is valid as a PHP method name (this is required to allow
                // extending the backend with a custom controller)
                if (!$this->isValidMethodName($entityName)) {
                    throw new \InvalidArgumentException(sprintf('The name of the "%s" entity contains invalid characters (allowed: letters, numbers, underscores; the first character cannot be a number).', $entityName));
                }
            }

            // if config format #3 defines the 'label' option, use its value.
            // otherwise, use the entity name as its label
            if (!isset($entityConfiguration['label'])) {
                $entityConfiguration['label'] = $entityName;
            }

            $entityConfiguration['name'] = $entityName;
            $normalizedConfiguration[$entityName] = $entityConfiguration;
        }

        $backendConfiguration['entities'] = $normalizedConfiguration;

        return $backendConfiguration;
    }

    private function normalizeFormViewConfiguration(array $backendConfiguration)
    {
        foreach ($backendConfiguration['entities'] as $entityName => $entityConfiguration) {
            if (isset($entityConfiguration['form'])) {
                $entityConfiguration['new'] = isset($entityConfiguration['new']) ? array_replace($entityConfiguration['form'], $entityConfiguration['new']) : $entityConfiguration['form'];
                $entityConfiguration['edit'] = isset($entityConfiguration['edit']) ? array_replace($entityConfiguration['form'], $entityConfiguration['edit']) : $entityConfiguration['form'];
            }

            $backendConfiguration['entities'][$entityName] = $entityConfiguration;
        }

        return $backendConfiguration;
    }

    private function normalizeViewConfiguration(array $backendConfiguration)
    {
        foreach ($backendConfiguration['entities'] as $entityName => $entityConfiguration) {
            foreach (array('edit', 'list', 'new', 'search', 'show') as $view) {
                if (!isset($entityConfiguration[$view])) {
                    $entityConfiguration[$view] = array('fields' => array());
                }

                if (!isset($entityConfiguration[$view]['fields'])) {
                    $entityConfiguration[$view]['fields'] = array();
                }

                if (in_array($view, array('edit', 'new')) && !isset($entityConfiguration[$view]['form_options'])) {
                    $entityConfiguration[$view]['form_options'] = array();
                }
            }

            $backendConfiguration['entities'][$entityName] = $entityConfiguration;
        }

        return $backendConfiguration;
    }

    /**
     * Fields can be defined using two different formats:.
     *
     * # Config format #1: simple configuration
     * easy_admin:
     *     Client:
     *         # ...
     *         list:
     *             fields: ['id', 'name', 'email']
     *
     * # Config format #2: extended configuration
     * easy_admin:
     *     Client:
     *         # ...
     *         list:
     *             fields: ['id', 'name', { property: 'email', label: 'Contact' }]
     *
     * This method processes both formats to produce a common form field configuration
     * format used in the rest of the application.
     */
    private function normalizePropertyConfiguration(array $backendConfiguration)
    {
        foreach ($backendConfiguration['entities'] as $entityName => $entityConfiguration) {
            foreach (array('edit', 'list', 'new', 'search', 'show') as $view) {
                $fields = array();
                foreach ($entityConfiguration[$view]['fields'] as $field) {
                    if (!is_string($field) && !is_array($field)) {
                        throw new \RuntimeException(sprintf('The values of the "fields" option for the "%s" view of the "%s" entity can only be strings or arrays.', $view, $entityConfiguration['class']));
                    }

                    if (is_string($field)) {
                        // Config format #1: field is just a string representing the entity property
                        $fieldConfiguration = array('property' => $field);
                    } else {
                        // Config format #1: field is an array that defines one or more
                        // options. Check that the mandatory 'property' option is set
                        if (!array_key_exists('property', $field)) {
                            throw new \RuntimeException(sprintf('One of the values of the "fields" option for the "%s" view of the "%s" entity does not define the "property" option.', $view, $entityConfiguration['class']));
                        }

                        $fieldConfiguration = $field;
                    }

                    // for 'image' type fields, if the entity defines an 'image_base_path'
                    // option, but the field does not, use the value defined by the entity
                    if (isset($fieldConfiguration['type']) && 'image' === $fieldConfiguration['type']) {
                        if (!isset($fieldConfiguration['base_path']) && isset($entityConfiguration['image_base_path'])) {
                            $fieldConfiguration['base_path'] = $entityConfiguration['image_base_path'];
                        }
                    }

                    $fieldName = $fieldConfiguration['property'];
                    $fields[$fieldName] = $fieldConfiguration;
                }

                $backendConfiguration['entities'][$entityName][$view]['fields'] = $fields;
            }
        }

        return $backendConfiguration;
    }

    /**
     * Checks whether the given string is valid as a PHP method name.
     *
     * @param string $name
     *
     * @return bool
     */
    private function isValidMethodName($name)
    {
        return 0 !== preg_match('/^-?[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $name);
    }

    /**
     * The name of the entity is included in the URLs of the backend to define
     * the entity used to perform the operations. Obviously, the entity name
     * must be unique to identify entities unequivocally.
     *
     * This method ensures that the given entity name is unique among all the
     * previously existing entities passed as the second argument. This is
     * achieved by iteratively appending a suffix until the entity name is
     * guaranteed to be unique.
     *
     * @param string $entityName
     * @param array  $existingEntityNames
     *
     * @return string The entity name transformed to be unique
     */
    private function getUniqueEntityName($entityName, array $existingEntityNames)
    {
        $uniqueName = $entityName;

        $i = 2;
        while (in_array($uniqueName, $existingEntityNames)) {
            $uniqueName = $entityName.($i++);
        }

        return $uniqueName;
    }
}
