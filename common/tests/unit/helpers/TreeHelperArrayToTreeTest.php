<?php

namespace common\tests\unit\helpers;

use common\helpers\TreeHelper;

class TreeHelperArrayToTreeTest extends \Codeception\Test\Unit
{

    #region testArrayToTree
    /**
     * @param array $source
     * @param array $result
     * @dataProvider testTreeData
     */
    public function testArrayToTree(array $source, array $result)
    {
        $tree = TreeHelper::arrayToTree(
            $source,
            'external_id',
            'parent_id',
            'children',
            'external_id'
        );
        verify($tree)->equals($result);
    }

    private function testTreeData(): array
    {
        return [
            [
                // source 1
                [
                    [
                        "external_id" => 'id1',
                        "parent_id" => "id2",
                    ],
                    [
                        "external_id" => 'id2',
                        "parent_id" => null,
                    ],
                    [
                        "external_id" => 'id3',
                        "parent_id" => "id2",
                    ],
                    [
                        "external_id" => 'id4',
                        "parent_id" => null,
                    ],
                    [
                        "external_id" => 'id5',
                        "parent_id" => 'id3',
                    ],
                ],
                // result 1
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
                                'children' => [
                                    [
                                        "external_id" => 'id5',
                                        "parent_id" => 'id3',
                                    ]
                                ],
                            ]
                        ]
                    ],
                    [
                        "external_id" => 'id4',
                        "parent_id" => null,
                    ],
                ]
            ],
        ];
    }
    #endregion

}