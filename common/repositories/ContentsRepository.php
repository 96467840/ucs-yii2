<?php

namespace common\repositories;

use common\infrastructure\AbstractRepository;
use common\models\Content;
use common\models\Template;

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
    public function getByExternal(string $external_id): ?Content
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
            ->select(['c.*'])
            ->asArray()
            ->all();

        return $contents;
    }
}