<?php

namespace frontend\infrastructure;

use common\repositories\PagesRepository;
use yii\base\BaseObject;
use yii\web\UrlRuleInterface;

class PageUrlRule extends BaseObject implements UrlRuleInterface
{

    public function __construct(private readonly PagesRepository $pages, array $config = [])
    {
        parent::__construct($config);
    }/**/

    /**
     * @inheritDoc
     */
    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();
        if ($manager->suffix) {
            $pathInfo = preg_replace(
                '/' . str_replace('.', '\\.', $manager->suffix) . '$/',
                '',
                $pathInfo
            );
        }
        if (!$pathInfo) {
            // todo это возможно в случае страницы по умолчанию, но пока пропутсим это
            return false;
        }
        $page = $this->pages->findByPath($pathInfo);
        if ($page) {
            return ['page/index', ['page' => $page]];
        }
        return false;  // данное правило не применимо
    }

    /**
     * @inheritDoc
     */
    public function createUrl($manager, $route, $params)
    {
        if ($route === 'page/index') {
            if (isset($params['path'])) {
                return $params['path'];
            }
        }
        return false;  // данное правило не применимо
    }
}