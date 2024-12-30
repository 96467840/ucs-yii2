<?php

namespace frontend\services;

use http\Exception\RuntimeException;
use yii\helpers\StringHelper;

class RenderContentService
{
    /**
     * ограничитель переменной в шаблоне
     */
    const VARIABLE_BOUNDARY = '|||';
    const VARIABLE_PREFIX = '{{';
    const VARIABLE_POSTFIX = '}}';

    const GLOBAL_PREFIX = '__';

    const TYPE_TEXT = 'text';
    const TYPE_STRING = 'string';
    //const TYPE_IMAGE = 'image';
    const TYPE_INT = 'integer';

    const HTML_TYPES = [
        self::TYPE_TEXT,
        self::TYPE_STRING,
    ];

    const TYPES = [
        self::TYPE_TEXT,
        self::TYPE_STRING,
        //self::TYPE_IMAGE,
        self::TYPE_INT,
    ];

    public function __construct(private readonly LoadTemplateService $templatesService)
    {
    }

    private function parseTemplate(string $template): array
    {
        return explode(
            self::VARIABLE_BOUNDARY,
            str_replace(
                [self::VARIABLE_PREFIX, self::VARIABLE_POSTFIX],
                [self::VARIABLE_BOUNDARY, self::VARIABLE_BOUNDARY],
                $template
            )
        );
    }

    public function build(array $template): string
    {
        return implode('', $template);
    }

    public function renderTemplate(array $contents, array &$globals, ?string $template, ?string $template_key): string
    {
        $children = [];
        foreach ($contents as $content) {
            $key = $content['key'] ?: 'contents'; // если ключа нет, то это основное поле контента
            /*if (empty($children[$key])) {
                $children[$key] = [];
            }/**/
            //$children[$key] = array_merge($children[$key] ?? [], $this->renderSingle($content, $globals));
            $children[$key] = ($children[$key] ?? '') . $this->renderSingle($content, $globals);
        }

        // выставим глобальные переменные в $globals
        foreach ($children as $key => $child) {
            if ($this->isGlobalKey($key)) {
                $globals[$key] = $child;
            }
        }

        // по шаблонам
        $template = $template ?? '';
        if (empty($template)) {
            $template = $this->templatesService->loadTemplateByKey($template_key);
        }

        /**
         * пример щаблона: |||top||||||banner|||<b>|||contents|||</b>
         * ['','top','','banner','<b>','contents','</b>']
         *
         * надо бы сделать поддержку: {{{top}}}{{{banner}}}<b>{{{contents}}}</b>
         * todo сделать предобработку шаблона: заменить '}}}' и '{{{' на '|||'
         */
        //$templateArray = explode(self::VARIABLE_BOUNDARY, $template);
        $templateArray = $this->parseTemplate($template);
        if (count($templateArray) == 1) {
            // шаблон не содержит переменных, пока не знаю зачем так нужно (установка $globals?), но пока разрешим
            // возвращаем сам шаблон
            return $template;
        }

        if (count($templateArray) % 2 == 0) {
            // ошибка шаблона. в шаблоне self::VARIABLE_BOUNDARY всегда парами и потмоу ожидаем нечетное количество
            // todo создать свой TemplateException
            throw new RuntimeException('Не корректный шаблон: ' . $template);
        }

        $result = [];

        // на не четных местах в $templateArray должны быть имена ключей которые нам надо заменить на данные из $children
        // четные узлы просто переносим в $result это часть шаблона
        for ($i = 0; $i < count($templateArray); $i++) {
            if ($i % 2 == 0) {
                $result[] = $templateArray[$i];
            } else {
                $key = $templateArray[$i];
                // заменяем на значение пременной если оно есть в $children
                // если нет значения, то вернуть что-то что потом можно будет почистить
                // имя переменной нельзя ибо а как потом отличить 'banner' от 'текст банера'
                if (isset($children[$key])) {
                    $result[] = $children[$key];
                    /*foreach ($children[$key] as $node) {
                        $result[] = $node;
                    }/**/
                } else {
                    // контента в узле нет
                    if ($this->isGlobalKey($key) && !empty($globals[$key])) {
                        foreach ($globals[$key] as $node) {
                            $result[] = $node;
                        }
                    } else {
                        // todo подумать как вернуть имя переменной чтобы можно было заполнить на верхнем уровне (массив или через глобальные)
                        $result[] = '';
                    }
                }
            }
        }

        return implode('', $result);
        /*$result = [];
        foreach ($resultArrays as $key => $resultArray) {
            $result[$key] = implode('', $resultArray);
        }

        return $resultArrays;
        /**/
    }

    public function renderSingle(array $content, array &$globals): string
    {
        // простейшие типы
        if (in_array($content['type'], self::TYPES)) {
            return $content[$content['type']];
        }

        return $this->renderTemplate(
            $content['children'],
            $globals,
            $content['template'] ?? null,
            $content['template_key'] ?? null
        );
        /*
        // выставим глобальные переменные в $globals
        foreach ($children as $key => $child) {
            if ($this->isGlobalKey($key)) {
                $globals[$key] = $child;
            }
        }

        //$templateArray = explode(self::VARIABLE_BOUNDARY, $template);
        $templateArray = $this->parseTemplate($template);
        if (count($templateArray) == 1) {
            // шаблон не содержит переменных, пока не знаю зачем так нужно (установка $globals?), но пока разрешим
            // возвращаем сам шаблон
            return [$template];
        }

        if (count($templateArray) % 2 == 0) {
            // ошибка шаблона. в шаблоне self::VARIABLE_BOUNDARY всегда парами
            // todo создать свой TemplateException
            throw new RuntimeException('Не корректный шаблон: ' . $template);
        }

        $result = [];

        // на не четных местах в $templateArray должны быть имена ключей которые нам надо заменить на данные из $children
        // четные узлы просто переносим в $result это часть шаблона
        for ($i = 0; $i < count($templateArray); $i++) {
            if ($i % 2 == 0) {
                $result[] = $templateArray[$i];
            } else {
                $key = $templateArray[$i];
                // заменяем на значение пременной если оно есть в $children
                // если нет значения, то вернуть что-то что потом можно будет почистить
                // имя переменной нельзя ибо а как потом отличить 'banner' от 'текст банера'
                if (!empty($children[$key])) {
                    foreach ($children[$key] as $node) {
                        $result[] = $node;
                    }
                } else {
                    // контента в узле нет
                    if ($this->isGlobalKey($key) && !empty($globals[$key])) {
                        foreach ($globals[$key] as $node) {
                            $result[] = $node;
                        }
                    } else {
                        // todo подумать как вернуть имя переменной чтобы можно было заполнить на верхнем уровне (массив или через глобальные)
                        $result[] = $key;
                    }
                }
            }
        }

        return $result;
        /**/
    }

    private function isGlobalKey(string $key): bool
    {
        return StringHelper::startsWith($key, self::GLOBAL_PREFIX);
    }

}