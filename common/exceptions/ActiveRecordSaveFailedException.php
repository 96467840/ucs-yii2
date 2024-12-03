<?php


namespace common\exceptions;


use RuntimeException;
use Throwable;
use yii\db\ActiveRecord;
use Yii;
use yii\helpers\StringHelper;

class ActiveRecordSaveFailedException extends RuntimeException
{
    public function __construct(private readonly ActiveRecord $model, $code = 0, Throwable $previous = null)
    {
        $class = get_class($model);

        $attributes = $model->attributes;
        $attributes['content'] = StringHelper::truncate($attributes['content'], 30);

        Yii::error(
            "Error while saving active {$class} record model" . PHP_EOL .
            (json_encode(
                [
                    'class' => $class,
                    'errors' => $model->errors,
                    'attributes' => $attributes
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            ) ?: '')
        );

        parent::__construct('Error while saving ' . $class . ' model', $code, $previous);
    }

    public function getModel(): ActiveRecord
    {
        return $this->model;
    }
}