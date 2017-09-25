<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/3
 * Time: 上午1:17
 */

namespace Sws\Memory;

use Swoole\Table;

/**
 * Class BaseTable
 * @package Sws\Memory
 *
 * Table使用共享内存来保存数据，在创建子进程前，务必要执行Table->create()
 * swoole_server中使用Table，Table->create() 必须在 swoole_server->start()前执行
 *
 * @method mixed set(string $key, array $values)
 * @method array get($key, $field = null)
 * @method bool exist(string $key)
 * @method bool del(string $key)
 * @method false|int incr(string $key, string $column, int $incrBy = 1)
 * @method false|int decr(string $key, string $column, int $decrBy = 1)
 */
class MemTable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Table
     */
    private $table;

    /**
     * @var int
     */
    private $size;

    /**
     * @var array[]
     * [
     *  field => [type, length]
     * ]
     */
    private $columns;

    /**
     * MemTable constructor.
     * @param string $name
     * @param int $size
     * @param array $columns
     */
    public function __construct($name, $size = 0, array $columns = [])
    {
        $this->name = $name;
        $this->size = $size;
        $this->columns = $columns;
    }

    /**
     * @param string $name
     * @param string $type
     * @param int $length
     * Table::TYPE_INT 默认为4个字节，可以设置1，2，4，8一共4种长度
     * Table::TYPE_STRING 设置后，设置的字符串不能超过此长度
     * Table::TYPE_FLOAT 会占用8个字节的内存
     * @return $this
     */
    public function addColumn($name, $type, $length = 0)
    {
        $this->columns[$name] = [$type, $length];

        return $this;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function addColumns(array $columns)
    {
        foreach ($columns as $column => $opt) {
            $this->columns[$column] = (array)$opt;
        }

        return $this;
    }

    /**
     * @return array
     */
//    abstract public function structure();
//    {
//        return [
//            'id' => [Table::TYPE_INT, 10],
//            'username' => [Table::TYPE_STRING, 32],
//            'nickname' => [Table::TYPE_STRING, 32],
//            'password' => [Table::TYPE_STRING, 64],
//        ];
//    }

    /**
     * @return bool
     */
    public function create()
    {
        return $this->table->create();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setSize(int $size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return Table
     */
    public function getTable(): Table
    {
        return $this->table;
    }

    /**
     * @param Table $table
     */
    public function setTable(Table $table)
    {
        $this->table = $table;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     * @return $this
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, array $args = [])
    {
        if (method_exists($this->table, $method)) {
            return $this->table->$method(...$args);
        }

        throw new \RuntimeException('Call a not exists method.');
    }
}
