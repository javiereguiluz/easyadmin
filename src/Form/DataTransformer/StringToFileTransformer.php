<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class StringToFileTransformer implements DataTransformerInterface
{
    private string $uploadDir;
    private $uploadFilename;
    private $uploadValidate;
    private bool $multiple;

    public function __construct(string $uploadDir, callable $uploadFilename, callable $uploadValidate, bool $multiple)
    {
        $this->uploadDir = $uploadDir;
        $this->uploadFilename = $uploadFilename;
        $this->uploadValidate = $uploadValidate;
        $this->multiple = $multiple;
    }

    public function transform($value): mixed
    {
        if (null === $value || [] === $value) {
            return null;
        }

        if (!$this->multiple) {
            return $this->doTransform($value);
        }

        if (!\is_array($value)) {
            throw new TransformationFailedException('Expected an array or null.');
        }

        return array_map([$this, 'doTransform'], $value);
    }

    public function reverseTransform($value): mixed
    {
        if (null === $value || [] === $value) {
            return null;
        }

        if (!$this->multiple) {
            return $this->doReverseTransform($value);
        }

        if (!\is_array($value)) {
            throw new TransformationFailedException('Expected an array or null.');
        }

        return array_map([$this, 'doReverseTransform'], $value);
    }

    private function doTransform($value): ?File
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof File) {
            return $value;
        }

        if (!\is_string($value)) {
            throw new TransformationFailedException('Expected a string or null.');
        }

        if (is_file($this->uploadDir.$value)) {
            return new File($this->uploadDir.$value);
        }

        return null;
    }

    private function doReverseTransform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof UploadedFile) {
            if (!$value->isValid()) {
                throw new TransformationFailedException($value->getErrorMessage());
            }

            $filename = ($this->uploadFilename)($value);

            return ($this->uploadValidate)($filename);
        }

        if ($value instanceof File) {
            return str_replace($this->uploadDir, '', $value->getPathname());
        }

        throw new TransformationFailedException('Expected an instance of File or null.');
    }
}
