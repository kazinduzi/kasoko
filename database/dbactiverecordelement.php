<?php defined('KAZINDUZI_PATH') or exit('No direct script access allowed');


/**
 * Description of DbActiveRecordElement
 *
 * @author Emmanuel_Leonie
 */

/**
 * Query ActiveRecordElement Class.
 *
 * This class helps building varius elems for SQL statments
 */
class DbActiveRecordElement
{
    /**
     * @var        string    The name of the element.
     */
    protected $name = null;

    /**
     * @var        array    An array of elements.
     */
    protected $elements = null;

    /**
     * @var    string    Separator for query's member.
     */
    protected $sep = null;

    /**
     * Constructor.
     *
     * @param    string $name The name of the element.
     * @param    mixed $elements String or array.
     * @param    string $sep The sep for elements.
     *
     * @return    DatabaseSQLElement
     *
     */
    public function __construct($name, $elements, $sep = ',')
    {
        $this->elements = array();
        $this->name = $name;
        $this->sep = $sep;
        // Let's append members to each other.
        $this->append($elements);
    }

    /**
     * Appends element parts to the internal list.
     *
     * @param mixed    String or array.
     *
     * @return void
     */
    public function append($elements)
    {
        if (is_array($elements)) {
            $this->elements = array_unique(array_merge($this->elements, $elements));
        } else {
            $this->elements = array_unique(array_merge($this->elements, array($elements)));
        }
    }

    /**
     * Magic function to convert the query element to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return PHP_EOL . trim($this->name) . ' ' . implode($this->sep, $this->elements);
    }
}
