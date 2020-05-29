<?php

namespace App\Form;

use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Exception\NameGenerationException;

/**
 * IncrementalFileNamer
 *
 * @author Ivan Ivanov <rikostik@gmail.com>
 */
class IncrementalFileNamer implements NamerInterface, ConfigurableInterface
{
    /**
     * @var int
     */
    private $start = 0;

    /**
     * @var string
     */
    private $propertyPath;

    public function __construct(int $max)
    {
        $this->max = $max;
    }

    /**
     * @param array $options Options for this namer. The following options are accepted:
     *                       - transliterate: whether the filename should be transliterated or not
     */
    public function configure(array $options): void
    {
        if (empty($options['property'])) {
            throw new \InvalidArgumentException('Option "property" is missing or empty.');
        }

        $this->propertyPath = $options['property'];
        $this->start = isset($options['start']) ? (int) $options['start'] : $this->max;
    }

    /**
     * {@inheritdoc}
     */
    public function name($object, PropertyMapping $mapping): string
    {   
        if (empty($this->propertyPath)) {
            throw new \LogicException('The property to use can not be determined. Did you call the configure() method?');
        }
        $file = $mapping->getFile($object);
        try {
            $id = $this->getPropertyValue($object, $this->propertyPath);
        } catch (NoSuchPropertyException $e) {
            throw new NameGenerationException(\sprintf('File name could not be generated: property %s does not exist.', $this->propertyPath), $e->getCode(), $e);
        }

        if (empty($id)) {
            throw new NameGenerationException(\sprintf('File name could not be generated: property %s is empty.', $this->propertyPath));
        }
        /* @var $file UploadedFile */
        $index = $id-$this->start;

        return 'image_'.$index;
    }
    private function getPropertyValue($object, $propertyPath)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($object, $propertyPath);
    }
}
