<?php

namespace common\repositories;

use common\helpers\TreeHelper;
use common\infrastructure\AbstractRepository;
use common\models\Content;
use common\models\Template;
use yii\helpers\ArrayHelper;

/**
 * @method Content get(int $id)
 * @method Content|null find(int $id)
 * @method Content save(Content $model)
 *
 * @extends AbstractRepository<Content>
 */
class ContentsRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(Content::class);
    }

    /**
     * Найти контент по внешнему идентификатору
     *
     * @param string $external_id
     * @return Content|null
     */
    public function findByExternal(string $external_id): ?Content
    {
        /** @var Content|null $item */
        $item = Content::find()->where(['external_id' => $external_id])->one();

        return $item;
    }

    public function findAllForPage(int $page_id): array
    {
        $contents = Content::find()->alias('c')
            ->leftJoin(Template::tableName() . ' as t', 't.id = c.template_id')
            ->where(['c.page_id' => $page_id, 'c.is_blocked' => 0])
            ->select([
                'c.*',
                't.key as template_key',
                't.template as template',
            ])
            ->asArray()
            ->all();

        return TreeHelper::arrayToTree($contents);
    }
}