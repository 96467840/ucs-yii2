<?php

namespace common\models;

use common\traits\ExtendedDataTrait;

/**
 * This is the model class for table "contents".
 *
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property int|null $page_id ID страницы.
 * @property int|null $template_id ID шаблона.
 * @property int|null $parent_id ID родительского узла.
 * @property string|null $title Название единицы контента
 * @property string|null $key Ключ контента
 * @property string|null $type Тип контента: list, list_item, text
 * @property float|null $float Дробное представление контента
 * @property int|null $integer Целочисленное представление контента
 * @property string|null $text Текстовое представление контента
 * @property int $is_full_text_search Если 1, то единица контента участвует в полнотекстовом поиске
 * @property int $is_blocked Для блокирования
 * @property string|null $extended_data
 * @property string|null $external_id
 *
 * @property Content[] $contents
 * @property Page $page
 * @property Content $parent
 * @property Template $template
 */
class Content extends \yii\db\ActiveRecord
{
    use ExtendedDataTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'extended_data', 'external_id'], 'safe'],
            [['page_id', 'template_id', 'parent_id', 'integer', 'is_full_text_search', 'is_blocked'], 'integer'],
            [['float'], 'number'],
            [['text'], 'string'],
            [['title', 'key', 'type'], 'string', 'max' => 255],
            [['page_id'], 'exist', 'skipOnError' => true, 'targetClass' => Page::class, 'targetAttribute' => ['page_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Content::class, 'targetAttribute' => ['parent_id' => 'id']],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => Template::class, 'targetAttribute' => ['template_id' => 'id']],
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
            'page_id' => 'ID страницы.',
            'template_id' => 'ID шаблона.',
            'parent_id' => 'ID родительского узла.',
            'title' => 'Название единицы контента',
            'key' => 'Ключ контента',
            'type' => 'Тип контента: list, list_item, text',
            'float' => 'Дробное представление контента',
            'integer' => 'Целочисленное представление контента',
            'text' => 'Текстовое представление контента',
            'is_full_text_search' => 'Если 1, то единица контента участвует в полнотекстовом поиске',
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
        return $this->hasMany(Content::class, ['parent_id' => 'id']);
    }

    /**
     * Gets query for [[Page]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Page::class, ['id' => 'page_id']);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Content::class, ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[Template]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Template::class, ['id' => 'template_id']);
    }
}
