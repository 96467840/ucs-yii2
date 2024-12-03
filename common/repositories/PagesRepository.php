<?php

namespace common\repositories;

use common\infrastructure\AbstractRepository;
use common\models\Page;

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
}