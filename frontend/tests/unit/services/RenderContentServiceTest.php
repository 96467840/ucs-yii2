<?php

namespace frontend\tests\unit\services;


use common\helpers\TreeHelper;
use frontend\services\LoadTemplateService;
use frontend\services\RenderContentService;

class RenderContentServiceTest extends \Codeception\Test\Unit
{

    private const array TEMPLATES = [
        'page' => '{{__seotitle}}<div>{{top}}</div><div>{{contents}}</div><div>{{bottom}}</div>',
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
    public function testRender(
        string $testName,
        ?string $pageTemplate,
        ?string $pageTemplateKey,
        array $contents,
        array $globals,
        string $html
    ) {
        $service = $this->getService();
        $testGlobals = [];
        $result = $service->renderTemplate(
            TreeHelper::arrayToTree($contents),
            $testGlobals,
            $pageTemplate,
            $pageTemplateKey
        );
        //dd($result);

        verify($result)->equals($html, '"' . $testName . '" failed!');
        verify($testGlobals)->equals($globals, '"' . $testName . ' globals failed!');
    }

    private function testRenderData(): array
    {
        return [
            # region simple test
            [
                // test name
                'simple test',
                // page template
                null,
                // page template key
                'page',
                // source 1
                [
                    [
                        'id' => 1,
                        "key" => "",
                        "type" => "",
                        "parent_id" => null,
                        //"template_key" => 'bold',
                        "template" => '<b>|||contents|||</b>|||bottom|||',
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
                    [
                        'id' => 4,
                        "key" => "bottom",
                        "type" => "string",
                        'string' => 'content bottom',
                    ],
                    [
                        'id' => 5,
                        "key" => "__seotitle",
                        "type" => "string",
                        'string' => 'seo title',
                    ],
                ],
                // globals
                [
                    '__seotitle' => 'seo title',
                ],
                // result content
                'seo title<div></div><div><b>hello world</b>!</div><div>content bottom</div>',
            ],
            # endregion

            # region list test
            [
                // test name
                'list test',
                // page template
                null,
                // page template key
                'page',
                // source 1
                [
                    [
                        'id' => 1,
                        "key" => "",
                        "type" => "",
                        "parent_id" => null,
                        //"template_key" => 'bold',
                        "template" => '<b>|||contents|||</b>|||bottom|||',
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
                    [
                        'id' => 4,
                        "key" => "bottom",
                        "type" => "string",
                        'string' => 'content bottom',
                    ],
                    [
                        'id' => 5,
                        "key" => "__seotitle",
                        "type" => "string",
                        'string' => 'seo title',
                    ],
                ],
                // globals
                [
                    '__seotitle' => 'seo title',
                ],
                // result content
                'seo title<div></div><div><b>hello world</b>!</div><div>content bottom</div>',
            ],
            # endregion
        ];
    }

}
