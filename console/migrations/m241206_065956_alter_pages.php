<?php

use console\models\BaseMigration;

class m241206_065956_alter_pages extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%pages}}', 'template_id', $this->integer());
        $this->addFK('pages', ['template_id'], 'templates', ['id'], 'SET NULL');

        $this->addColumn('{{%pages}}', 'priority', $this->integer()->notNull()->defaultValue(0));
        $this->createIndex(
            'contents_priority_idx',
            '{{%pages}}',
            ['priority']
        );

        $this->addColumn('{{%contents}}', 'priority', $this->integer());
        $this->createIndex(
            'contents_priority_idx',
            '{{%contents}}',
            ['priority']
        );

        $this->createTable('{{%contents_pages}}', [
            'id' => $this->primaryKey(20),
            'created_at' => $this->getTypeForCreatedAtField(),
            'updated_at' => $this->getTypeForUpdatedAtField(),

            'page_id' => $this->integer()->notNull()->comment('ID страницы.'),
            'content_id' => $this->integer()->notNull()->comment('ID контента.'),

            'template_id' => $this->integer()->null()->comment('ID шаблона. Для переопределения шаблона'),

            'priority' => $this->integer()->notNull()->defaultValue(0)->comment('Переопределение порядка.'),

            'is_blocked' => $this->smallInteger()->notNull()->defaultValue(0)->comment('Для блокирования'),

            'extended_data' => $this->json()->null(),
        ], $this->getTableOptions());

        $this->createIndex(
            'contents_pages_priority_idx',
            '{{%contents_pages}}',
            ['priority']
        );
        $this->createIndex(
            'contents_pages_is_blocked_idx',
            '{{%contents_pages}}',
            ['is_blocked']
        );

        $this->addFK('contents_pages', ['content_id'], 'contents', ['id']);
        $this->addFK('contents_pages', ['page_id'], 'pages', ['id']);
        $this->addFK('contents_pages', ['template_id'], 'templates', ['id'], 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%contents_pages}}');

        $this->dropForeignKey('pages-template_id-fk', '{{%pages}}');
        $this->dropColumn('{{%pages}}', 'template_id');
        $this->dropColumn('{{%pages}}', 'priority');
        $this->dropColumn('{{%contents}}', 'priority');
    }

}
