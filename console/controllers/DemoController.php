<?php

namespace console\controllers;


use common\services\ImportService;
use console\infrastructure\Controller;
use Yii;
use yii\console\ExitCode;
use yii\db\Exception;
use yii\helpers\Console;

class DemoController extends Controller
{
    public function __construct($id, $module, private ImportService $importService)
    {
        parent::__construct($id, $module);
    }

    /**
     * @var string controller default action ID.
     */
    public $defaultAction = 'load';

    public string $folder = 'demo';

    public function options($actionID)
    {
        return ['folder'];
    }

    public function optionAliases()
    {
        return ['f' => 'folder'];
    }

    /**
     * Загружаем демо данные из файла
     *
     * @return int
     */
    public function actionLoad(): int
    {
        $demo_path = Yii::getAlias('@demo');
        if (!$demo_path) {
            throw new Exception('Не заполнен алиас @demo');
        }

        $filename = $demo_path . '/' . $this->folder . '.json';
        if (is_file($filename)) {
            $json = json_decode(file_get_contents($filename) ?: '', true);

            $this->importService->import($json);

            $this->output('Импорт успешно завершен.', [Console::FG_GREEN, Console::BOLD]);

            return ExitCode::OK;
        }

        $this->output('Файл ' . $filename . ' с данными не найден.', [Console::FG_RED, Console::BOLD]);

        return ExitCode::UNSPECIFIED_ERROR;
    }


}