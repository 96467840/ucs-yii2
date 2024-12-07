<?php

namespace common\repositories;

use common\infrastructure\AbstractRepository;
use common\models\Content;

/**
 * @method Content get(string $id)
 * @method Content|null find(string $id)
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

}