<?php

namespace common\tests\unit\helpers;

use common\helpers\TreeHelper;

class TreeHelperArrayToDictionaryTest extends \Codeception\Test\Unit
{

    /**
     * @param array $source
     * @param array $result
     * @dataProvider testDictionariesData
     */
    public function testArrayToDictionary(array $source, array $result)
    {
        verify(TreeHelper::arrayToDictionary($source, 'external_id'))->equals($result);
    }

    private function testDictionariesData(): array
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
