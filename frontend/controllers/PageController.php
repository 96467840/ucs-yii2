<?php

namespace frontend\controllers;

use common\models\Page;
use common\repositories\ContentsRepository;
use common\repositories\PagesRepository;
use Yii;
use yii\web\Controller;

class PageController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly ContentsRepository $contents,
        private readonly PagesRepository $pages,
        $config = []
    ) {
        parent::__construct($id, $module);
    }

    public function actionIndex(Page $page)
    {
        $contents = $this->contents->findAllForPage($page->id);
        $menu = $this->pages->findAllForMenus();
        /*return $page->title . '<hr>'
            . '<pre>'
            . (json_encode($contents, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: '?')
            . '</pre>';/**/
        return $this->render('index', ['page' => $page, 'contents' => $contents, 'menu' => $menu]);
    }
}