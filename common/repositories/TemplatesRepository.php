<?php

namespace common\repositories;

use common\infrastructure\AbstractRepository;
use common\models\Template;

/**
 * @method Template get(int $id)
 * @method Template|null find(int $id)
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

    /**
     * Найти шаблон по внешнему идентификатору
     *
     * @param string $external_id
     * @return Template|null
     */
    public function findByExternal(string $external_id): ?Template
    {
        /** @var Template|null $item */
        $item = Template::find()->where(['external_id' => $external_id])->one();

        return $item;
    }
}