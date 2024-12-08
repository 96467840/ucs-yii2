<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "contents_pages".
 *
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property int $page_id ID страницы.
 * @property int $content_id ID контента.
 * @property int|null $template_id ID шаблона. Для переопределения шаблона
 * @property int $priority Переопределение порядка.
 * @property int $is_blocked Для блокирования
 * @property string|null $extended_data
 *
 * @property Content $content
 * @property Page $page
 * @property Template $template
 */
class ContentPage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contents_pages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'extended_data'], 'safe'],
            [['page_id', 'content_id'], 'required'],
            [['page_id', 'content_id', 'template_id', 'priority', 'is_blocked'], 'integer'],
            [['content_id'], 'exist', 'skipOnError' => true, 'targetClass' => Content::class, 'targetAttribute' => ['content_id' => 'id']],
            [['page_id'], 'exist', 'skipOnError' => true, 'targetClass' => Page::class, 'targetAttribute' => ['page_id' => 'id']],
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
            'content_id' => 'ID контента.',
            'template_id' => 'ID шаблона. Для переопределения шаблона',
            'priority' => 'Переопределение порядка.',
            'is_blocked' => 'Для блокирования',
            'extended_data' => 'Extended Data',
        ];
    }

    /**
     * Gets query for [[Content]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContent()
    {
        return $this->hasOne(Content::class, ['id' => 'content_id']);
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
     * Gets query for [[Template]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(Template::class, ['id' => 'template_id']);
    }
}
