<?php

namespace console\infrastructure;

use yii\helpers\Console;

class Controller extends \yii\console\Controller
{
    protected function output(string $content, array $args = []): void
    {
        Console::output(Console::ansiFormat($content, $args));
    }

    protected function execute(string $command)
    {
        $this->output('Running command: ' . $command, [Console::FG_GREY, Console::BOLD]);
        exec($command);
    }
}
