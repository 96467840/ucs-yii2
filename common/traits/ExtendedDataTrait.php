<?php

namespace common\traits;

use ErrorException;
use yii\helpers\ArrayHelper;

/**
 * Работа с полем extended_data где храним доп инфу по объекту
 *
 * Trait ExtendedDataTrait
 */
trait ExtendedDataTrait
{
    /**
     * Чтобы можно было перопределить имя поля
     *
     * @return string
     * @psalm-suppress UndefinedThisPropertyFetch
     */
    private function getStorageName(): string
    {
        if (property_exists($this, 'storageAttribute')) {
            return $this->storageAttribute;
        }

        return 'extended_data';
    }


    /**
     * @throws ErrorException
     */
    private function assertStorageExists(): void
    {
        /** @psalm-suppress UndefinedMethod */
        if (!array_key_exists($this->getStorageName(), $this->getAttributes())) {
            throw new ErrorException(get_class($this) . 'dont have ' . $this->getStorageName() . ' storage');
        }
    }


    private function getData(): array
    {
        $this->assertStorageExists();

        return $this->attributes[$this->getStorageName()] ?? [];
    }

    private function setData(array $data): void
    {
        $this->assertStorageExists();
        $this->{$this->getStorageName()} = $data;
    }

    /**
     * Установка значения в хранилище
     *
     * @param string $key
     * @param mixed $value
     */
    public function setExtendedDataItem(string $key, mixed $value): void
    {
        $data = $this->getData();
        ArrayHelper::setValue($data, $key, $value);
        $this->setData($data);
    }

    /**
     * Получение значения по ключу
     *
     * @param string $key
     * @param mixed $returnIfNotExist
     * @return mixed|null
     */
    public function getExtendedDataItem(string $key, mixed $returnIfNotExist = null): mixed
    {
        $data = $this->getData();

        return ArrayHelper::getValue($data, $key, $returnIfNotExist);
    }

    /**
     * Удаление ключа
     *
     * @param string $key
     * @return void
     */
    public function removeExtendedDataItem(string $key): void
    {
        $data = $this->getData();
        ArrayHelper::remove($data, $key);
        $this->setData($data);
    }
}
