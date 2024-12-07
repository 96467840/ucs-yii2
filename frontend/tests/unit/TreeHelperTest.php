<?php

namespace frontend\tests\unit;

use common\helpers\TreeHelper;

class TreeHelperTest extends \Codeception\Test\Unit
{


    /**
     * @param array $source
     * @param array $result
     * @dataProvider testTreeData
     */
    public function testArrayToTree(array $source, array $result)
    {
        $tree = TreeHelper::arrayToTree($source, 'external_id');
        //dd($tree);
        verify($tree)->equals($result);
    }

    public function testTreeData(): array
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
                            ]
                        ]
                    ],
                ]
            ],
        ];
    }


    /**
     * @param array $source
     * @param array $result
     * @dataProvider testDictionariesData
     */
    public function testArrayToDictionary(array $source, array $result)
    {
        verify(TreeHelper::arrayToDictionary($source, 'external_id'))->equals($result);
    }

    public function testDictionariesData(): array
    {
        return [
            [
                // source 1
                [
                    [
                        "external_id" => 'id1',
                        "data" => "d1",
                    ],
                    [
                        "external_id" => 'id2',
                        "data" => "d1sdfgwrsafg",
                    ],
                    [
                        "external_id" => 'id3',
                        "data" => "d333",
                    ],
                ],
                // result 1
                [
                    'id1' => [
                        "external_id" => 'id1',
                        "data" => "d1",
                    ],
                    'id2' => [
                        "external_id" => 'id2',
                        "data" => "d1sdfgwrsafg",
                    ],
                    'id3' => [
                        "external_id" => 'id3',
                        "data" => "d333",
                    ]
                ]
            ],
        ];
    }

}
