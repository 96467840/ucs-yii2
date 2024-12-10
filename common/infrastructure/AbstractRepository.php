<?php

declare(strict_types=1);

namespace common\infrastructure;

use common\exceptions\ActiveRecordDeleteFailedException;
use common\exceptions\ActiveRecordNotFoundException;
use common\exceptions\ActiveRecordSaveFailedException;
use Throwable;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\StaleObjectException;

/**
 * @template T of ActiveRecord
 */
abstract class AbstractRepository
{
    /**
     * @var class-string<T>
     */
    protected string $modelClass;

    protected ?string $softDeleteAttribute;

    /**
     * @param class-string<T> $modelClass
     * @param string|null $softDeleteAttribute
     */
    public function __construct(
        string $modelClass,
        ?string $softDeleteAttribute = null
    ) {
        $this->modelClass = $modelClass;
        $this->softDeleteAttribute = $softDeleteAttribute;
    }

    /**
     * @param int $id
     * @return T
     */
    public function get(int $id): ActiveRecord
    {
        $model = $this->find($id);

        if ($model) {
            return $model;
        }

        throw new ActiveRecordNotFoundException($this->modelClass);
    }

    /**
     * @param int $id
     * @return T|null
     */
    public function find(int $id): ?ActiveRecord
    {
        $findFunction = [$this->modelClass, 'findOne'];

        return $findFunction($id);
    }

    /**
     * @param T $model
     * @return T
     * @throws ActiveRecordSaveFailedException|Exception
     */
    public function save(ActiveRecord $model): ActiveRecord
    {
        if ($model->validate() && $model->save()) {
            return $model;
        }

        throw new ActiveRecordSaveFailedException($model);
    }

    /**
     * @param T $model
     * @throws Throwable
     * @throws Exception
     * @throws StaleObjectException
     */
    public function delete(ActiveRecord $model): void
    {
        if ($this->softDeleteAttribute) {
            $model->setAttribute($this->softDeleteAttribute, true);
            $this->save($model);
            return;
        }

        if ($model->delete()) {
            return;
        }

        throw new ActiveRecordDeleteFailedException($model);
    }
}
