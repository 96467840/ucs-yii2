<?php

use yii\db\Migration;

class m241204_091150_add_external_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%pages}}', 'external_id', $this->string());
        $this->addColumn('{{%contents}}', 'external_id', $this->string());
        $this->addColumn('{{%templates}}', 'external_id', $this->string());

        $this->createIndex(
            'pages_external_id_idx',
            '{{%pages}}',
            ['external_id']
        );

        $this->createIndex(
            'contents_external_id_idx',
            '{{%contents}}',
            ['external_id']
        );

        $this->createIndex(
            'templates_external_id_idx',
            '{{%templates}}',
            ['external_id']
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%pages}}', 'external_id');
        $this->dropColumn('{{%templates}}', 'external_id');
        $this->dropColumn('{{%contents}}', 'external_id');
    }
}
