<?php

namespace common\models;

use common\traits\ExtendedDataTrait;

/**
 * This is the model class for table "templates".
 *
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $key Код шаблона
 * @property string $title Название шаблона
 * @property string|null $template HTML шаблон.
 * @property int $is_blocked Для блокирования
 * @property string|null $extended_data
 * @property string|null $external_id
 *
 * @property Content[] $contents
 */
class Template extends \yii\db\ActiveRecord
{
    use ExtendedDataTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'templates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'extended_data', 'external_id'], 'safe'],
            [['key', 'title'], 'required'],
            [['template'], 'string'],
            [['is_blocked'], 'integer'],
            [['key', 'title'], 'string', 'max' => 255],
            [['key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'key' => 'Код шаблона',
            'title' => 'Название шаблона',
            'template' => 'HTML шаблон.',
            'is_blocked' => 'Для блокирования',
            'extended_data' => 'Extended Data',
        ];
    }

    /**
     * Gets query for [[Contents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContents()
    {
        return $this->hasMany(Content::class, ['template_id' => 'id']);
    }
}
