<?php

namespace frontend\tests\unit\services;


use common\helpers\TreeHelper;
use frontend\services\LoadTemplateService;
use frontend\services\RenderContentService;

class RenderContentServiceTest extends \Codeception\Test\Unit
{

    private const array TEMPLATES = [
        'page' => '<div>{{top}}</div><div>{{contents}}</div><div>{{bottom}}</div>',
        'simple' => '|||contents|||',
        'bold' => '<b>|||contents|||</b>',
    ];

    private function getService(): RenderContentService
    {
        return new RenderContentService(
            $this->make(LoadTemplateService::class, [
                'loadTemplateByKey' => fn(string $key) => self::TEMPLATES[$key] ?? ''
            ]),
        );
    }

    /**
     * @param array $source
     * @param array $globals
     * @param string $result
     * @dataProvider testRenderData
     */
    public function testRender($contents, $globals, $html)
    {
        $service = $this->getService();
        $result = $service->renderTemplate(TreeHelper::arrayToTree($contents), $globals, null, 'page');
        //dd($result);

        verify($result)->equals($html);
    }

    private function testRenderData(): array
    {
        return [
            [
                // source 1
                [
                    [
                        'id' => 1,
                        "key" => "",
                        "type" => "",
                        "parent_id" => null,
                        //"template_key" => 'bold',
                        "template" => '<b>|||contents|||</b>|||sign|||',
                    ],
                    [
                        'id' => 2,
                        "key" => "",
                        "type" => "string",
                        "parent_id" => 1,
                        'string' => 'hello world',
                    ],
                    [
                        'id' => 3,
                        "key" => "",
                        "type" => "string",
                        'string' => '!',
                    ],
                ],
                // result tree
                [],
                // string content
                '<div></div><div><b>hello world</b>!</div><div></div>',
            ]
        ];
    }

}
