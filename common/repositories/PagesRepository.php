<?php

namespace common\repositories;

use common\helpers\TreeHelper;
use common\infrastructure\AbstractRepository;
use common\models\Page;
use common\models\Template;

/**
 * @method Page get(int $id)
 * @method Page|null find(int $id)
 * @method Page save(Page $model)
 *
 * @extends AbstractRepository<Page>
 */
class PagesRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(Page::class);
    }

    /**
     * Найти страницу по внешнему идентификатору
     *
     * @param string $external_id
     * @return Page|null
     */
    public function findByExternal(string $external_id): ?Page
    {
        /** @var Page|null $item */
        $item = Page::find()->where(['external_id' => $external_id])->one();

        return $item;
    }

    public function findByPath(string $path): ?Page
    {
        /** @var Page|null $item */
        $item = Page::find()->where(['path' => strtolower($path), 'is_blocked' => 0])->one();

        return $item;
    }

    public function findAllForMenus(): array
    {
        $items = Page::find()
            ->where(['is_blocked' => 0])
            ->asArray()
            ->select(['id', 'path', 'title', 'parent_id'])
            ->all();

        return TreeHelper::arrayToTree($items, 'id', 'parent_id');
    }

}