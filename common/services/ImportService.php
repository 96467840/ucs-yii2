<?php

namespace common\services;

use common\helpers\TreeHelper;
use common\models\Content;
use common\models\Page;
use common\models\Template;
use common\repositories\ContentsRepository;
use common\repositories\PagesRepository;
use common\repositories\TemplatesRepository;
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

        $this->importContents($clear_old, $json['contents'] ?? [], $template_ids, $page_ids);
    }

    /**
     * Импортируем контент
     *
     * @param bool $clear_old
     * @param array $contents
     * @param array $templates_ids_map
     * @param array $pages_ids_map
     * @return void
     * @throws \yii\db\Exception
     */
    private function importContents(
        bool $clear_old,
        array $contents,
        array $templates_ids_map,
        array $pages_ids_map
    ): void {
        $map = [];
        foreach ($contents as $content) {
            $item = $clear_old ? null : $this->contents->getByExternal($content['external_id']);
            if (!$item) {
                $item = new Content();
            }

            // ресолвим парента. в источнике ссылки на external_id.
            if (!empty($content['parent_id'])) {
                if (empty($map[$content['parent_id']])) {
                    throw new \RuntimeException(
                        'Не могу отресолвить внешний ID элемента контента ' . $content['parent_id']
                    );
                }
                $content['parent_id'] = $map[$content['parent_id']];
            }

            if (!empty($content['template_id'])) {
                if (empty($templates_ids_map[$content['template_id']])) {
                    throw new \RuntimeException('Не могу отресолвить ID шаблона ' . $content['template_id']);
                }
                $content['template_id'] = $templates_ids_map[$content['template_id']];
            }

            if (!empty($content['page_id'])) {
                if (empty($pages_ids_map[$content['page_id']])) {
                    throw new \RuntimeException('Не могу отресолвить ID страницы ' . $content['page_id']);
                }
                $content['page_id'] = $pages_ids_map[$content['page_id']];
            }

            $item->load($content, '');
            if (!$item->validate()) {
                throw new \RuntimeException(var_export($item->errors, true));
            }

            $this->contents->save($item);
            $map[$content['external_id']] = $item->id;

            if (!empty($content['pages'])) {
                // todo
            }
        }
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
        $tree = TreeHelper::arrayToTree($pages, 'external_id', 'parent_id');

        return TreeHelper::treeToArray($tree);
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
                    throw new \RuntimeException('Не могу отресолвить внешний ID страницы');
                }
                $page['parent_id'] = $map[$page['parent_id']];
            }

            if (!empty($page['template_id'])) {
                if (empty($templates_ids_map[$page['template_id']])) {
                    throw new \RuntimeException('Не могу отресолвить ID шаблона ' . $page['template_id']);
                }
                $page['template_id'] = $templates_ids_map[$page['template_id']];
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