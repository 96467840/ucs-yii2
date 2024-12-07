<?php

namespace common\services;

use common\models\Content;
use common\models\Page;
use common\models\Template;
use common\repositories\ContentsRepository;
use common\repositories\PagesRepository;
use common\repositories\TemplatesRepository;
use http\Exception\RuntimeException;
use yii\db\Expression;

readonly class ImportService
{
    public function __construct(
        private PagesRepository $pages,
        private ContentsRepository $contents,
        private TemplatesRepository $templates,
    ) {
    }

    /**
     * Импорт данных
     * @psalm-param array{
     *      clear_old: bool,
     *      pages:   array{},
     *      templates: array{},
     *  } $json
     * @return void
     */
    public function import(array $json): void
    {
        $clear_old = !empty($json['clear_old']);
        if ($clear_old) {
            $this->clearOldWithExternal();
        }

        $template_ids = $this->importTemplates($clear_old, $json['templates'] ?? []);

        $page_ids = $this->importPages($clear_old, $json['pages'] ?? [], $template_ids);
    }

    /**
     * Импортируем шаблоны и возвращаем массив с картой соответствия внешних идентификаторов на внутренние
     *
     * @param bool $clear_old признак, того что удаляли старые записи, чтобы не делать поиск того чего уже нет
     * @param array $templates массив новых данных шаблонов
     * @return array<string, int>
     * @throws \yii\db\Exception
     */
    private function importTemplates(bool $clear_old, array $templates): array
    {
        /** @var array<string, int> $map */
        $map = []; // карта соответствия внешних id к внутренним

        foreach ($templates as $template) {
            $item = $clear_old ? null : $this->templates->getByExternal($template['external_id']);
            if (!$item) {
                $item = new Template();
            }
            $item->load($template, '');
            if (!$item->validate()) {
                throw new \RuntimeException(var_export($item->errors, true));
            }
            $this->templates->save($item);
            $map[$template['external_id']] = $item->id;
        }

        return $map;
    }

    /**
     * Проверяем целостность связи по parent_id - и всех родителей скидываем вперед
     *
     * @param array $pages массив страниц
     * @return array массив страниц
     */
    private function checkAndReorderPages(array $pages): array
    {
        // todo сделать переосортировку массива страниц чтобы все зависимые страницы были после своих парентов
        // просто соберем дерево и развернем дерево в массив

        return $pages;
    }

    private function importPages(bool $clear_old, array $pages, array $templates_ids_map): array
    {
        /** @var array<string, int> $map */
        $map = []; // карта соответствия внешних id к внутренним

        $pages = $this->checkAndReorderPages($pages);

        foreach ($pages as $page) {
            $item = $clear_old ? null : $this->pages->getByExternal($page['external_id']);
            if (!$item) {
                $item = new Page();
            }

            // ресолвим парента. в источнике ссылки на external_id.
            if (!empty($page['parent_id'])) {
                if (empty($map[$page['parent_id']])) {
                    // в теории мы это должны отловить в checkAndReorderPages($pages)
                    throw new RuntimeException('Не могу отресолвить внешний ID старницы');
                }
                $page['parent_id'] = $map[$page['parent_id']];
            }

            if (!empty($page['template_id'])) {
                if (empty($templates_ids_map[$page['template_id']])) {
                    throw new RuntimeException('Не могу отресолвить ID шаблона ' . $page['template_id']);
                }
            }

            $item->load($page, '');
            if (!$item->validate()) {
                throw new \RuntimeException(var_export($item->errors, true));
            }

            $this->pages->save($item);
            $map[$page['external_id']] = $item->id;
        }

        return $map;
    }

    /**
     * Удаляем все записи контента, страниц и шаблонов которые пришли по импорту (external_id не NULL))
     *
     * @return void
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    private function clearOldWithExternal(): void
    {
        // контент удаляем поштучно так как там могут быть файлы и их надо удалять
        foreach (
            Content::find()
                ->where(['not', ['external_id' => null]])
                ->batch(10) as $contents
        ) {
            foreach ($contents as $content) {
                $this->contents->delete($content);
            }
        }

        Page::deleteAll(['not', ['external_id' => null]]);

        Template::deleteAll(['not', ['external_id' => null]]);
    }

}