<?php
/** 
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     Amazon_SimpleDB
 *  @copyright   Copyright 2007 Amazon Technologies, Inc.
 *  @link        http://aws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2007-11-07
 */
/******************************************************************************* 
 *    __  _    _  ___ 
 *   (  )( \/\/ )/ __)
 *   /__\ \    / \__ \
 *  (_)(_) \/\/  (___/
 * 
 *  Amazon Simple DB PHP5 Library
 *  Generated: Wed Jan 02 01:46:17 PST 2008
 * 
 */

/**
 *  @see Amazon_SimpleDB_Model
 */
require_once ('Amazon/SimpleDB/Model.php');  

    

/**
 * Amazon_SimpleDB_Model_PutAttributes
 * 
 * Properties:
 * <ul>
 * 
 * <li>DomainName: string</li>
 * <li>ItemName: string</li>
 * <li>Attribute: Amazon_SimpleDB_Model_ReplaceableAttribute</li>
 *
 * </ul>
 */ 
class Amazon_SimpleDB_Model_PutAttributes extends Amazon_SimpleDB_Model
{


    /**
     * Construct new Amazon_SimpleDB_Model_PutAttributes
     * 
     * @param mixed $data DOMElement or Associative Array to construct from. 
     * 
     * Valid properties:
     * <ul>
     * 
     * <li>DomainName: string</li>
     * <li>ItemName: string</li>
     * <li>Attribute: Amazon_SimpleDB_Model_ReplaceableAttribute</li>
     *
     * </ul>
     */
    public function __construct($data = null)
    {
        $this->_fields = array (
        'DomainName' => array('FieldValue' => null, 'FieldType' => 'string'),
        'ItemName' => array('FieldValue' => null, 'FieldType' => 'string'),
        'Attribute' => array('FieldValue' => array(), 'FieldType' => array('Amazon_SimpleDB_Model_ReplaceableAttribute')),
        );
        parent::__construct($data);
    }

        /**
     * Gets the value of the DomainName property.
     * 
     * @return string DomainName
     */
    public function getDomainName() 
    {
        return $this->_fields['DomainName']['FieldValue'];
    }

    /**
     * Sets the value of the DomainName property.
     * 
     * @param string DomainName
     * @return this instance
     */
    public function setDomainName($value) 
    {
        $this->_fields['DomainName']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the DomainName and returns this instance
     * 
     * @param string $value DomainName
     * @return Amazon_SimpleDB_Model_PutAttributes instance
     */
    public function withDomainName($value)
    {
        $this->setDomainName($value);
        return $this;
    }


    /**
     * Checks if DomainName is set
     * 
     * @return bool true if DomainName  is set
     */
    public function isSetDomainName()
    {
        return !is_null($this->_fields['DomainName']['FieldValue']);
    }

    /**
     * Gets the value of the ItemName property.
     * 
     * @return string ItemName
     */
    public function getItemName() 
    {
        return $this->_fields['ItemName']['FieldValue'];
    }

    /**
     * Sets the value of the ItemName property.
     * 
     * @param string ItemName
     * @return this instance
     */
    public function setItemName($value) 
    {
        $this->_fields['ItemName']['FieldValue'] = $value;
        return $this;
    }

    /**
     * Sets the value of the ItemName and returns this instance
     * 
     * @param string $value ItemName
     * @return Amazon_SimpleDB_Model_PutAttributes instance
     */
    public function withItemName($value)
    {
        $this->setItemName($value);
        return $this;
    }


    /**
     * Checks if ItemName is set
     * 
     * @return bool true if ItemName  is set
     */
    public function isSetItemName()
    {
        return !is_null($this->_fields['ItemName']['FieldValue']);
    }

    /**
     * Gets the value of the Attribute.
     * 
     * @return array of ReplaceableAttribute Attribute
     */
    public function getAttribute() 
    {
        return $this->_fields['Attribute']['FieldValue'];
    }

    /**
     * Sets the value of the Attribute.
     * 
     * @param mixed array or variable number of ReplaceableAttribute Attribute
     * @return this instance
     */
    public function setAttribute(array $value) 
    {
        foreach (func_get_args() as $attribute) {
            if (!$this->_isNumericArray($attribute)) {
                $attribute =  array ($attribute);    
            }
            $this->_fields['Attribute']['FieldValue'] = $attribute;
        }
        return $this;
    }


    /**
     * Sets single or multiple values of Attribute list via variable number of arguments. 
     * For example, to set the list with two elements, simply pass two values as arguments to this function
     * <code>withAttribute($attribute1, $attribute2)</code>
     * 
     * @param ReplaceableAttribute  $replaceableAttributeArgs one or more Attribute
     * @return Amazon_SimpleDB_Model_PutAttributes  instance
     */
    public function withAttribute($replaceableAttributeArgs)
    {
        foreach (func_get_args() as $attribute) {
            $this->_fields['Attribute']['FieldValue'][] = $attribute;
        }
        return $this;
    }   



    /**
     * Checks if Attribute list is non-empty
     * 
     * @return bool true if Attribute list is non-empty
     */
    public function isSetAttribute()
    {
        return count ($this->_fields['Attribute']['FieldValue']) > 0;
    }




    /**
     * Representation of action that returns associative array of AWS Query Parameters
     * 
     * @return array AWS Query parameters - name and value pairs
     */
    public function toMap()
                                        {
        $parameters = array();
        $parameters["Action"] = 'PutAttributes';
        if ($this->isSetDomainName()) {
            $parameters["DomainName"] =  $this->getDomainName();
        }
        if ($this->isSetItemName()) {
            $parameters["ItemName"] =  $this->getItemName();
        }
        foreach ($this->getAttribute() as $attributeIndex => $attribute) {
            if ($attribute->isSetName()) {
                $parameters["Attribute" . "."  . ($attributeIndex + 1) . "." . "Name"] =  $attribute->getName();
            }
            if ($attribute->isSetValue()) {
                $parameters["Attribute" . "."  . ($attributeIndex + 1) . "." . "Value"] =  $attribute->getValue();
            }
            if ($attribute->isSetReplace()) {
                $parameters["Attribute" . "."  . ($attributeIndex + 1) . "." . "Replace"] =  $attribute->getReplace() ? "true" : "false";
            }

        }
        return $parameters;
    }

}