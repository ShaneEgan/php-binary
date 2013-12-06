<?php
/**
 * php-binary
 * A PHP library for parsing structured binary streams
 *
 * @package  php-binary
 * @author Damien Walsh <me@damow.net>
 */
namespace Binary;

use Binary\Fields\Properties\Property;
use Binary\Fields\Properties\Backreference;

/**
 * Schema
 * Represents the internal structure of a binary field file.
 *
 * @since 1.0
 */
class Schema
{
    /**
     * @var array The fields contained by this schema.
     */
    public $fields = array();

    /**
     * Initialise a new schema with a definition in the form of an array of fields.
     *
     * @param array $definition The field set to initialise the schema with.
     * @return $this
     */
    public function initWithSchemaDefinition(array $definition)
    {
        foreach ($definition as $fieldName => $field) {
            $this->addDefinedField($fieldName, $field);
        }

        return $this;
    }

    /**
     * Add a new field to this schema instance, or to an existing CompoundField.
     *
     * @param string $fieldName The name of the field to add.
     * @param array $definition The definition (from JSON) of the field to add.
     * @param Fields\CompoundField $targetField The target compound field to add the new field to.
     */
    private function addDefinedField($fieldName, array $definition, Fields\CompoundField $targetField = null)
    {
        $className = __NAMESPACE__ . '\\Fields\\' . $definition['_type'];
        $newField = new $className;

        // Set the properties on the field
        foreach ($definition as $propertyName => $propertyValue) {

            if ($propertyName[0] === '_') {
                // Don't add special-meaning _ fields
                continue;
            }

            if ($propertyValue[0] === '@') {
                // Property is referencing an already-parsed field value
                $backreference = new Backreference();
                $backreference->setPath(substr($propertyValue, 1));
                $newField->{$propertyName} = $backreference;
            } else {
                $newField->{$propertyName} = new Property($propertyValue);
            }

        }

        // Add the field name
        $newField->name = $fieldName;

        // Are we adding a compound field?
        if (is_a($newField, __NAMESPACE__ . '\\Fields\\CompoundField')) {
            if (isset($definition['_fields'])) {
                // Adding a compound field that has some subfields
                foreach ($definition['_fields'] as $subFieldName => $subFieldDefinition) {
                    $this->addDefinedField($subFieldName, $subFieldDefinition, $newField);
                }
            }
        }

        if ($targetField) {
            // Adding the field to an existing compound field
            $targetField->addField($newField);
        } else {
            // Adding the field to this schema
            $this->addField($newField);
        }
    }

    /**
     * @param Fields\FieldInterface $field The field to add to the schema.
     * @return $this
     */
    public function addField(Fields\FieldInterface $field)
    {
        $this->fields[] = $field;
        return $this;
    }

    /**
     * @param $stream Streams\StreamInterface The stream to parse.
     * @return DataSet
     */
    public function readStream(Streams\StreamInterface $stream)
    {
        $result = new DataSet();

        foreach ($this->fields as $field) {
            $field->read($stream, $result);
        }

        return $result;
    }

    public function writeStream(Streams\StreamInterface $stream, DataSet $result)
    {
        foreach ($this->fields as $field) {
            $field->write($stream, $result);
        }
    }
}
