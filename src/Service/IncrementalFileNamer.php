<?php

namespace App\Service;

use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Naming\ConfigurableInterface;
use Vich\UploaderBundle\Exception\NameGenerationException;
use Vich\UploaderBundle\Naming\Polyfill\FileExtensionTrait;

/**
 * IncrementalFileNamer
 *
 * @author Ivan Ivanov <rikostik@gmail.com>
 */
class IncrementalFileNamer implements NamerInterface, ConfigurableInterface
{
    use FileExtensionTrait;

    /**
     * @var int
     */
    private static $index = 1;

    /**
     * @var string
     */
    private $tokenPath;

    /**
     * @var string
     */
    private static $token;

    /**
     * @param array $options Options for this namer. The following options are accepted:
     *                       - transliterate: whether the filename should be transliterated or not
     */
    public function configure(array $options): void
    {
        if (empty($options['token'])) {
            throw new \InvalidArgumentException('Option "property" is missing or empty.');
        }

        $this->tokenPath = $options['token'];
    }

    /**
     * {@inheritdoc}
     */
    public function name($object, PropertyMapping $mapping): string
    {   
        if (empty($this->tokenPath)) {
            throw new \LogicException('The property to use can not be determined. Did you call the configure() method?');
        }
        try {
            if ($this::$token == $this->getPropertyValue($object, $this->tokenPath)) {
                $this::$index++;
            } else {
                $this::$token = $this->getPropertyValue($object, $this->tokenPath);
                $this::$index = 1;
            }
        } catch (NoSuchPropertyException $e) {
            throw new NameGenerationException(\sprintf('File name could not be generated: property %s does not exist.', $this->tokenPath), $e->getCode(), $e);
        }

        if (empty($this->getPropertyValue($object, $this->tokenPath))) {
            throw new NameGenerationException(\sprintf('File name could not be generated: property %s is empty.', $this->tokenPath));
        }
        $file = $mapping->getFile($object);
        if (!$extension = $this->getExtension($file)) {
            $extension = "png";
        }

        return 'image_'.$this::$index.'.'.$extension;
    }
    private function getPropertyValue($object, $tokenPath)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($object, $tokenPath);
    }
}
