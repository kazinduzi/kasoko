<?php defined('KAZINDUZI_PATH') or exit('No direct script access allowed');

/**
 * Description of customer
 *
 * @author Emmanuel_Leonie
 */
class customer extends Model
{
    const MODEL_TABLE_NAME = 'customer';
    const PRIMARY_KEY_FIELD = 'customer_id';


    /**
     * Table name
     * @var string
     */
    public $table = self::MODEL_TABLE_NAME;

    /**
     * The primary key
     * @var string
     */
    protected $pk = self::PRIMARY_KEY_FIELD;

    /**
     * The customer id for this model
     * @var integer
     */
    protected $id;

    /**
     * Constructor
     *
     * @param mixed $id
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    /**
     *
     * @param string $email
     * @return type
     */
    public function findByEmail($email)
    {
        $where = sprintf("email='%s'", $email);
        return $this->findByAttr('*', $where);
    }

    /**
     * Add customer by array
     *
     * @param array $data
     * @return \customer
     * @throws \Exception
     */
    public function addByArray(array $data)
    {
        if (!$data) {
            throw new \Exception('Invalid data for model provided at line:' . __LINE__);
        }
        $this->values = $data;
        $this->saveRecord();
        return $this;
    }

    /**
     * Edit customer by array
     *
     * @param array $data
     * @return type
     * @throws \Exception
     */
    public function editByArray(array $data)
    {
        if (!$data[$this->pk]) {
            throw new \Exception('Invalid category id is provided at line:' . __LINE__);
        }
        $this->values = $data;
        return $this->saveRecord();
    }

    public function delete()
    {
        return parent::delete();
    }

}