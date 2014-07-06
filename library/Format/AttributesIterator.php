<?php
/**
 * Created by PhpStorm.
 * User: Константин
 * Date: 25.06.14
 * Time: 23:55
 */

/**
 * Class AttributesIterator
 */
class Format_AttributesIterator implements Iterator
{
    /**
     * @var int
     */
    private $index = 0;

    /**
     * Constant
     */
    const ID = "id";
    const IS_RANGE = "is_range";
    const VALUE = "value";
    const FROM = "from";
    const TO = "to";

    /**
     * Attributes
     *
     * @var array
     */
    private $attributes = array();

    /**
     * Set attributes
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->attributes[$this->index];
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getID()
    {
        return $this->attributes[$this->index][self::ID];
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->attributes[$this->index][self::VALUE];
    }

    /**
     * Is range
     *
     * @return bool
     */
    public function IsRange()
    {
        return (bool)$this->attributes[$this->index][self::IS_RANGE];
    }

    /**
     * Get value from
     *
     * @return integer
     */
    public function getValueFrom()
    {
        return $this->attributes[$this->index][self::VALUE][self::FROM];
    }

    /**
     * Get value to
     *
     * @return integer
     */
    public function getValueTo()
    {
        return $this->attributes[$this->index][self::VALUE][self::TO];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        ++$this->index;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->attributes[$this->index]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->index = 0;
    }

} 