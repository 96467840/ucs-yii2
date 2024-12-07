<?php

namespace common\repositories;

use common\infrastructure\AbstractRepository;
use common\models\Page;
use common\models\Template;

/**
 * @method Page get(string $id)
 * @method Page|null find(string $id)
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
    public function getByExternal(string $external_id): ?Page
    {
        /** @var Page|null $item */
        $item = Page::find()->where(['external_id' => $external_id])->one();

        return $item;
    }

}