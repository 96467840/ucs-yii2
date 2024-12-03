<?php

namespace console\models;

use yii\db\ColumnSchemaBuilder;
use yii\db\Migration;

class BaseMigration extends Migration
{
    /**
     *  Вернем тип для поля created_at
     * @return ColumnSchemaBuilder
     */
    protected function getTypeForCreatedAtField(): ColumnSchemaBuilder
    {
        return $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP');
    }

    /**
     * Вернем тип для поля updated_at
     * @psalm-return string | ColumnSchemaBuilder
     */
    protected function getTypeForUpdatedAtField(): mixed
    {
        if ($this->db->driverName === 'mysql') {
            return 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
        }
        return $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP');
    }

    /**
     *
     * @return string|null
     */
    protected function getTableOptions(): ?string
    {
        if ($this->db?->driverName === 'mysql') {
            // https://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            return 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        return null;
    }

    /**
     * Обертка для создания ФК
     *
     * @param $table
     * @param $columns
     * @param $refTable
     * @param $refColumns
     * @param $delete
     * @param $update
     * @return void
     */
    protected function addFK($table, $columns, $refTable, $refColumns, $delete = 'CASCADE', $update = 'CASCADE'): void
    {
        $_table = '{{%' . $table . '}}';
        $_refTable = '{{%' . $refTable . '}}';

        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $this->addForeignKey(
            $table . '-' . implode('-', $columns) . '-fk',
            $_table,
            $columns,
            $_refTable,
            $refColumns,
            $delete,
            $update
        );
    }

}