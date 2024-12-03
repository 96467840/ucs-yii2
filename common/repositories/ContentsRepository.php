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
}