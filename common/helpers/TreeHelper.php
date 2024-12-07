<?php

namespace common\helpers;

/**
 *  Сервис для работы с деревьями. Массив в дерево и обратно
 */
class TreeHelper
{
    public static function arrayToTree(
        array $array,
        string $primaryKeyName = 'id',
        string $relationName = 'parent_id',
        string $childrenName = 'children'
    ): array {
        $result = [];

        $dictionary = static::arrayToDictionary($array, $primaryKeyName);
        foreach ($dictionary as $key => $value) {
            if (!empty($value[$relationName])) {
                if (!isset($dictionary[$value[$relationName]])) {
                    throw new \RuntimeException('Не могу найти родительский узел ' . $value[$relationName]);
                }
                if (!isset($dictionary[$value[$relationName]][$childrenName])) {
                    $dictionary[$value[$relationName]][$childrenName] = [];
                }
                $dictionary[$value[$relationName]][$childrenName][] = $value;
            }
        }

        foreach ($dictionary as $value) {
            if (empty($value[$relationName])) {
                $result[] = $value;
            }
        }

        return $result;
    }

    public static function arrayToDictionary(array $array, string $primaryKeyName = 'id'): array
    {
        $result = [];
        foreach ($array as $item) {
            if (empty($item[$primaryKeyName])) {
                throw new \RuntimeException(
                    'Отсутствует главный индекс в узле: '
                    . json_encode($item, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                );
            }
            $result[$item[$primaryKeyName]] = $item;
        }
        return $result;
    }
}