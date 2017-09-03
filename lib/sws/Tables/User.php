<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/3
 * Time: 下午1:05
 */

namespace Sws\Tables;

use Swoole\Table;

/**
 * Class User
 * @package Sws\Tables
 */
class User
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
     * MemTable constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $table = new Table(1024);



//        $table->column('password', Table::TYPE_STRING, 64);
        // $table->column('num', Table::TYPE_FLOAT);

        $table->create();

        $this->name = $name;
    }

    public function structure()
    {
        return [
            'id' => [Table::TYPE_INT, 10],
            'username' => [Table::TYPE_STRING, 32],
            'nickname' => [Table::TYPE_STRING, 32],
            'password' => [Table::TYPE_STRING, 64],
        ];
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
}
