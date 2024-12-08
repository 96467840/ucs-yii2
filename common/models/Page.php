<?php

namespace common\models;

use common\traits\ExtendedDataTrait;
use Yii;

/**
 * This is the model class for table "pages".
 *
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $path URI. Полный путь без домена
 * @property string|null $title Название страницы
 * @property string|null $seo SEO данные
 * @property string|null $full_text_search Слова для полнотекстового поиска
 * @property int $is_blocked Для блокирования
 * @property int|null $parent_id ID родительского узла. Для подразделов
 * @property string|null $extended_data
 * @property string|null $external_id
 * @property int|null $template_id
 * @property int $priority
 *
 * @property Content[] $contents
 * @property ContentPage[] $contentsPages
 * @property Page[] $pages
 * @property Page $parent
 * @property Template $template
 */
class Page extends \yii\db\ActiveRecord
{
    use ExtendedDataTrait;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'seo', 'extended_data'], 'safe'],
            [['path'], 'required'],
            [['full_text_search'], 'string'],
            [['is_blocked', 'parent_id', 'template_id', 'priority'], 'integer'],
            [['path', 'title', 'external_id'], 'string', 'max' => 255],
            [['path'], 'unique'],
            [
                ['parent_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Page::class,
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
            'path' => 'URI. Полный путь без домена',
            'title' => 'Название страницы',
            'seo' => 'SEO данные',
            'full_text_search' => 'Слова для полнотекстового поиска',
            'is_blocked' => 'Для блокирования',
            'parent_id' => 'ID родительского узла. Для подразделов',
            'extended_data' => 'Extended Data',
            'external_id' => 'External ID',
            'template_id' => 'Template ID',
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
        return $this->hasMany(Content::class, ['page_id' => 'id']);
    }

    /**
     * Gets query for [[ContentsPages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContentPages()
    {
        return $this->hasMany(ContentPage::class, ['page_id' => 'id']);
    }

    /**
     * Gets query for [[Pages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPages()
    {
        return $this->hasMany(Page::class, ['parent_id' => 'id']);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Page::class, ['id' => 'parent_id']);
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
