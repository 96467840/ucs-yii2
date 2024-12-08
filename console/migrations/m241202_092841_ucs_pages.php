<?php

use console\models\BaseMigration;

class m241202_092841_ucs_pages extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%pages}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->getTypeForCreatedAtField(),
            'updated_at' => $this->getTypeForUpdatedAtField(),

            'path' => $this->string()->notNull()->unique()->comment('URI. Полный путь без домена'),
            'title' => $this->string()->comment('Название страницы'),
            'seo' => $this->json()->comment('SEO данные'),

            'full_text_search' => $this->text()->comment('Слова для полнотекстового поиска'),

            'is_blocked' => $this->smallInteger()->notNull()->defaultValue(0)->comment('Для блокирования'),

            'parent_id' => $this->integer()->comment('ID родительского узла. Для подразделов'),

            'extended_data' => $this->json()->null(),
        ], $this->getTableOptions());

        $this->createIndex(
            'pages_is_blocked_idx',
            '{{%pages}}',
            ['is_blocked']
        );

        $this->addFK('pages', ['parent_id'], 'pages', ['id'], 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%pages}}');
    }
}
