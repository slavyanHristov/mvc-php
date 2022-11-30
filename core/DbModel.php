<?php

namespace app\core;

abstract class DbModel extends Model
{
    abstract protected static function tableName(): string;

    abstract protected function attributes(): array;

    abstract public static function primaryKey(): string;

    public static function prepare($sql)
    {
        return Application::$app->db->prepare($sql);
    }

    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn ($attr) => ":$attr", $attributes);
        $statement = self::prepare("INSERT INTO $tableName (" . implode(',', $attributes) . ")
            VALUES(" . implode(',', $params) . ")
        ");

        foreach ($attributes as $attribute) {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }
        $statement->execute();
        return true;
    }

    public static function findOne($where)
    {
        $tableName = static::tableName(); //TODO: Difference between static:: and self::
        $attributes = array_keys($where);
        $sql = implode("AND ", array_map(fn ($attr) => "$attr = :$attr", $attributes));
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach ($where as $key => $value) {
            $statement->bindValue("$key", $value);
        }

        $statement->execute();
        return $statement->fetchObject(static::class);
        // SELECT * FROM $tableName WHERE $sql;

    }
}
