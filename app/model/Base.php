<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Base
 *
 * @author Jiri Kvapil
 */
namespace App\Model;

class Base extends \Nette\Object
{
    /** @var Nette\Database\Context */
    public $database;

    /** @var string */
    protected $tableName;

    /**
     * @param Nette\Database\Connection $db
     * @throws Nette\InvalidStateException
     */
    public function __construct(\Nette\Database\Context $db)
    {
            $this->database = $db;

            if($this->tableName === NULL) {
                    $class = get_class($this);
                    throw new Nette\InvalidStateException("Název tabulky musí být definován v $class::\$tableName.");
            }
    }

    protected function getTable() {
            return $this->database->table($this->tableName);
    }

    /**
     * Vrací vysledek custom dotazu
     * @return Nette\Database\Context\ResultSet
     */
    public function query($query) {
        return $this->database->query($query);
    }

    /**
     * Vrací všechny záznamy z databáze
     * @return \Nette\Database\Table\Selection
     */
    public function findAll() {
            return $this->getTable();
    }

    /**
     * Vrací vyfiltrované záznamy na základě vstupního pole
     * (pole array('name' => 'David') se převede na část SQL dotazu WHERE name = 'David')
     * @param array $by
     * @return \Nette\Database\Table\Selection
     */
    public function findBy(array $by) {
            return $this->getTable()->where($by);
    }

    /**
     * To samé jako findBy akorát vrací vždy jen jeden záznam
     * @param array $by
     * @return \Nette\Database\Table\ActiveRow|FALSE
     */
    public function findOneBy(array $by) {
            return $this->findBy($by)->limit(1)->fetch();
    }

    /**
     * Vrací záznam s daným primárním klíčem
     * @param int $id
     * @return \Nette\Database\Table\ActiveRow|FALSE
     */
    public function find($id) {
            return $this->getTable()->get($id);
    }

    /**
     * Upraví záznam
     * @param array $data
     */
    public function update($data) {
            $this->findBy(array('id'=> $data['id']))->update($data);
    }

    /**
     * Vloží nový záznam a vrátí jeho ID
     * @param array $data
     * @return \Nette\Database\Table\ActiveRow
     */
    public function insert($data) {
            return $this->getTable()->insert($data);
    }

    public function delete($id) {
            $this->getTable()->where('id',$id)->delete();
    }

    public function deleteBy(array $by) {
            $this->findBy($by)->delete();
    }
}