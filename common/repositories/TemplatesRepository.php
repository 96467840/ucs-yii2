<?php

namespace common\repositories;

use common\infrastructure\AbstractRepository;
use common\models\Template;

/**
 * @method Template get(string $id)
 * @method Template|null find(string $id)
 * @method Template save(Template $model)
 *
 * @extends AbstractRepository<Template>
 */
class TemplatesRepository extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct(Template::class);
    }
}