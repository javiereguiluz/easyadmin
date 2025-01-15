<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\TextEditorType;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class TextEditorField implements FieldInterface
{
    use FieldTrait;

    public const OPTION_NUM_OF_ROWS = 'numOfRows';
    public const OPTION_TRIX_EDITOR_CONFIG = 'trixEditorConfig';

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplateName('crud/field/text_editor')
            ->setFormType(TextEditorType::class)
            ->addCssClass('field-text_editor')
            ->addCssFiles(Asset::fromEasyAdminAssetPackage('field-text-editor.css')->onlyOnForms())
            ->addJsFiles(Asset::fromEasyAdminAssetPackage('field-text-editor.js')->onlyOnForms())
            ->setDefaultColumns('col-md-9 col-xxl-7')
            ->setCustomOption(self::OPTION_NUM_OF_ROWS, null)
            ->setCustomOption(self::OPTION_TRIX_EDITOR_CONFIG, null);
    }

    public function setNumOfRows(int $rows): self
    {
        if ($rows < 1) {
            throw new \InvalidArgumentException(sprintf('The argument of the "%s()" method must be 1 or higher (%d given).', __METHOD__, $rows));
        }

        $this->setCustomOption(self::OPTION_NUM_OF_ROWS, $rows);

        return $this;
    }

    public function setTrixEditorConfig(array $config): self
    {
        $this->setCustomOption(self::OPTION_TRIX_EDITOR_CONFIG, $config);

        return $this;
    }
}
