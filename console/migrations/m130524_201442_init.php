<?php

use console\models\BaseMigration;

class m130524_201442_init extends BaseMigration
{
    public function up()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->getTypeForCreatedAtField(),
            'updated_at' => $this->getTypeForUpdatedAtField(),

            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),

            'extended_data' => $this->json()->null(),
        ], $this->getTableOptions());

        // сгенерим дефолтного админа в дев среде
        if (YII_ENV_DEV) {
            $password_hash = Yii::$app->getSecurity()->generatePasswordHash('1');

            $this->insert(
                '{{%user}}',
                [
                    'username' => 'admin',
                    'email' => 'admin@example.com',
                    'password_hash' => $password_hash,
                    'auth_key' => ''
                ]
            );
        }
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
