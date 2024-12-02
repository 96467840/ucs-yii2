<?php

use console\models\BaseMigration;

class m241202_105012_content extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%contents}}', [
            'id' => $this->primaryKey(20),
            'created_at' => $this->getTypeForCreatedAtField(),
            'updated_at' => $this->getTypeForUpdatedAtField(),

            'page_id' => $this->integer()->null()->comment('ID страницы.'),
            'template_id' => $this->integer()->null()->comment('ID шаблона.'),
            'parent_id' => $this->integer(20)->null()->comment('ID родительского узла.'),

            'title' => $this->string()->comment('Название единицы контента'),

            'key' => $this->string()->comment('Ключ контента'),
            'type' => $this->string()->comment('Тип контента: list, list_item, text'),

            'float' => $this->float()->comment('Дробное представление контента'),
            'integer' => $this->bigInteger()->comment('Целочисленное представление контента'),
            'text' => $this->text()->comment('Текстовое представление контента'),

            'is_full_text_search' => $this->smallInteger()->notNull()->defaultValue(0)
                ->comment('Если 1, то единица контента участвует в полнотекстовом поиске'),
            'is_blocked' => $this->smallInteger()->notNull()->defaultValue(0)->comment('Для блокирования'),

            'extended_data' => $this->json()->null(),
        ], $this->getTableOptions());

        $this->createIndex(
            'contents_is_blocked_idx',
            '{{%contents}}',
            ['is_blocked']
        );
        $this->createIndex(
            'contents_is_full_text_search_idx',
            '{{%contents}}',
            ['is_full_text_search']
        );

        $this->addFK('contents', ['parent_id'], 'contents', ['id'], 'RESTRICT');
        $this->addFK('contents', ['page_id'], 'pages', ['id'], 'SET NULL');
        $this->addFK('contents', ['template_id'], 'templates', ['id'], 'RESTRICT');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%contents}}');
    }
}
