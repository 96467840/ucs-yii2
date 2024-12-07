<?php

namespace common\models;

use common\traits\ExtendedDataTrait;

/**
 * This is the model class for table "pages".
 *
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $path URI. Полный путь без домена: "about/info/detail"
 * @property string|null $title Название страницы
 * @property string|null $seo SEO данные
 * @property string|null $full_text_search Слова для полнотекстового поиска
 * @property int $is_blocked Для блокирования
 * @property int|null $parent_id ID родительского узла. Для подразделов
 * @property string|null $extended_data
 * @property string|null $external_id
 *
 * @property Content[] $contents
 * @property Page[] $pages
 * @property Page $parent
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
            [['created_at', 'updated_at', 'seo', 'extended_data', 'external_id'], 'safe'],
            [['path'], 'required'],
            [['full_text_search'], 'string'],
            [['is_blocked', 'parent_id'], 'integer'],
            [['path', 'title'], 'string', 'max' => 255],
            [['path'], 'unique'],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Page::class, 'targetAttribute' => ['parent_id' => 'id']],
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
            'path' => 'URI. Полный путь без домена: \"about/info/detail\"',
            'title' => 'Название страницы',
            'seo' => 'SEO данные',
            'full_text_search' => 'Слова для полнотекстового поиска',
            'is_blocked' => 'Для блокирования',
            'parent_id' => 'ID родительского узла. Для подразделов',
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
        return $this->hasMany(Content::class, ['page_id' => 'id']);
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
}
