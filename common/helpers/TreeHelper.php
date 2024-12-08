<?php

namespace common\helpers;

/**
 *  Сервис для работы с деревьями. Массив в дерево и обратно
 */
class TreeHelper
{
    /**
     * Конвертируем массив в дерево
     *
     * @param array $array исходный массив
     * @param string $primaryKeyName первичный ключ (обычно id)
     * @param string $parentKeyName имя поля указателя на родителя
     * @param string $childrenName имя поля в которое попадут потомки
     * @return array
     */
    public static function arrayToTree(
        array $array,
        string $primaryKeyName = 'id',
        string $parentKeyName = 'parent_id',
        string $childrenName = 'children',
        string $sortChildrenBy = null
    ): array {
        $result = [];

        $dictionary = static::arrayToDictionary($array, $primaryKeyName);
        foreach ($dictionary as &$value) {
            if (!empty($value[$parentKeyName])) {
                $parentId = $value[$parentKeyName];
                if (!isset($dictionary[$parentId])) {
                    throw new \RuntimeException('Не могу найти родительский узел ' . $parentId);
                }
                if (!isset($dictionary[$parentId][$childrenName])) {
                    $dictionary[$parentId][$childrenName] = [];
                }
                $dictionary[$parentId][$childrenName][] = &$value;
            }
        }

        foreach ($dictionary as &$value) {
            if (empty($value[$parentKeyName])) {
                $result[] = $value;
            }
        }

        static::sortChildrenInTree($result, $childrenName, $sortChildrenBy);

        return $result;
    }

    public static function sortChildrenInTree(
        array &$tree,
        string $childrenKeyName = 'children',
        string $sortChildrenBy = null
    ): void {
        if (!$sortChildrenBy) {
            return;
        }
        foreach ($tree as &$item) {
            if (!empty($item[$childrenKeyName])) {
                static::sortChildrenInTree($item[$childrenKeyName], $childrenKeyName, $sortChildrenBy);
            }
        }
        usort($tree, fn($a, $b) => $a[$sortChildrenBy] <=> $b[$sortChildrenBy]);
    }

    /**
     * Конвертируем дерево в массив
     *
     * @param array $tree
     * @param string $childrenName
     * @return array
     */
    public static function treeToArray(
        array $tree,
        string $childrenName = 'children',
        bool $deleteChildrenFromResult = true,
    ): array {
        $result = [];

        foreach ($tree as $value) {
            $children = $value[$childrenName] ?? [];
            if ($deleteChildrenFromResult) {
                unset($value[$childrenName]);
            }

            $result[] = $value; // родители вперед, чтобы не нарушить FK

            if ($children) {
                $result = array_merge(
                    $result,
                    static::treeToArray($children, $childrenName, $deleteChildrenFromResult)
                );
            }
        }

        return $result;
    }

    /**
     * Конвертируем массив в справочник по полю $primaryKeyName ($item[$primaryKeyName] => $item)
     *
     * @param array $array
     * @param string $primaryKeyName
     * @return array
     */
    public static function arrayToDictionary(array $array, string $primaryKeyName = 'id'): array
    {
        $result = [];
        foreach ($array as $item) {
            if (empty($item[$primaryKeyName])) {
                throw new \RuntimeException(
                    'Отсутствует главный индекс в узле: '
                    . (json_encode($item, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '')
                );
            }
            $result[$item[$primaryKeyName]] = $item;
        }
        return $result;
    }
}