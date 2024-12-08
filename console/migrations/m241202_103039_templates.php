<?php

use console\models\BaseMigration;

class m241202_103039_templates extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%templates}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->getTypeForCreatedAtField(),
            'updated_at' => $this->getTypeForUpdatedAtField(),

            'key' => $this->string()->notNull()->unique()->comment('Код шаблона'),
            'title' => $this->string()->notNull()->comment('Название шаблона'),
            'template' => $this->text()->comment('HTML шаблон.'),

            'is_blocked' => $this->smallInteger()->notNull()->defaultValue(0)->comment('Для блокирования'),

            'extended_data' => $this->json()->null(),
        ], $this->getTableOptions());

        $this->createIndex(
            'templates_is_blocked_idx',
            '{{%templates}}',
            ['is_blocked']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%templates}}');
    }
}
