<?php

namespace common\tests\unit\helpers;

use common\helpers\TreeHelper;

class TreeHelperTreeToArrayTest extends \Codeception\Test\Unit
{

    /**
     * @param array $source
     * @param array $result
     * @dataProvider testTreeData
     */
    public function testTreeToArray(array $source, array $result)
    {
        $tree = TreeHelper::treeToArray($source, 'children');
        verify($tree)->equals($result);
    }

    private function testTreeData(): array
    {
        return [
            [
                // source 1
                [
                    [
                        "external_id" => 'id2',
                        "parent_id" => null,
                        'children' => [
                            [
                                "external_id" => 'id1',
                                'parent_id' => 'id2',
                            ],
                            [
                                "external_id" => 'id3',
                                'parent_id' => 'id2',
                            ]
                        ]
                    ],
                    [
                        "external_id" => 'id4',
                        "parent_id" => null,
                    ],
                ],
                // result 1
                [
                    [
                        "external_id" => 'id2',
                        "parent_id" => null,
                    ],
                    [
                        "external_id" => 'id1',
                        "parent_id" => "id2",
                    ],
                    [
                        "external_id" => 'id3',
                        "parent_id" => "id2",
                    ],
                    [
                        "external_id" => 'id4',
                        "parent_id" => null,
                    ],
                ],
            ],

        ];
    }

}