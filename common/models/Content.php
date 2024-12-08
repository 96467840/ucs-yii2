<?php

namespace common\models;

use common\traits\ExtendedDataTrait;
use Yii;

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
 * @property string|null $type Тип контента
 * @property string|null $text Текстовое представление контента
 * @property float|null $float Дробное представление контента
 * @property int|null $integer Целочисленное представление контента
 * @property string|null $string Строковое представление контента
 * @property float|null $float_max Максимальное значение дробного представления контента
 * @property int|null $integer_max Максимальное значение целочисленного представления контента
 * @property string|null $string_max Максимальное значение строкового представления контента
 * @property int $is_full_text_search Если 1, то единица контента участвует в полнотекстовом поиске
 * @property int $is_blocked Для блокирования
 * @property string|null $extended_data
 * @property string|null $external_id
 * @property int|null $priority
 *
 * @property Content[] $contents
 * @property ContentPage[] $contentsPages
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
            [['created_at', 'updated_at', 'extended_data'], 'safe'],
            [
                [
                    'page_id',
                    'template_id',
                    'parent_id',
                    'integer',
                    'integer_max',
                    'is_full_text_search',
                    'is_blocked',
                    'priority'
                ],
                'integer'
            ],
            [['text'], 'string'],
            [['float', 'float_max'], 'number'],
            [['title', 'key', 'type', 'external_id'], 'string', 'max' => 255],
            [['string', 'string_max'], 'string', 'max' => 1024],
            [
                ['page_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Page::class,
                'targetAttribute' => ['page_id' => 'id']
            ],
            [
                ['parent_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Content::class,
                'targetAttribute' => ['parent_id' => 'id']
            ],
            [
                ['template_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Template::class,
                'targetAttribute' => ['template_id' => 'id']
            ],
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
            'type' => 'Тип контента',
            'text' => 'Текстовое представление контента',
            'float' => 'Дробное представление контента',
            'integer' => 'Целочисленное представление контента',
            'string' => 'Строковое представление контента ',
            'float_max' => 'Максимальное значение дробного представления контента',
            'integer_max' => 'Максимальное значение целочисленного представления контента',
            'string_max' => 'Максимальное значение строкового представления контента ',
            'is_full_text_search' => 'Если 1, то единица контента участвует в полнотекстовом поиске',
            'is_blocked' => 'Для блокирования',
            'extended_data' => 'Extended Data',
            'external_id' => 'External ID',
            'priority' => 'Priority',
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
     * Gets query for [[ContentsPages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContentsPages()
    {
        return $this->hasMany(ContentPage::class, ['content_id' => 'id']);
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
