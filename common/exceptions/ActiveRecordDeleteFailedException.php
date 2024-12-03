<?php


namespace common\exceptions;


use RuntimeException;
use Throwable;
use Yii;
use yii\db\ActiveRecord;

class ActiveRecordDeleteFailedException extends RuntimeException
{
    public function __construct(private readonly ActiveRecord $model, $code = 0, Throwable $previous = null)
    {
        $class = get_class($model);
        $attributes = $model->attributes;

        Yii::error(
            'Error while deleting ' . $class . ' model' . PHP_EOL .
            (json_encode(
                [
                    'class' => $class,
                    'errors' => $model->errors,
                    'attributes' => $attributes
                ],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            ) ?: '')
        );

        parent::__construct('Error while deleting ' . $class . ' model', $code, $previous);
    }

    public function getModel(): ActiveRecord
    {
        return $this->model;
    }
}