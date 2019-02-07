#!/usr/bin/env php
<?php
/**
 * @license http://hardsoft321.org/license/ GPLv3
 * @author Evgeny Pervushin <pea@lab321.ru>
 */

use GetOpt\GetOpt;
use GetOpt\Command;
use GetOpt\Option;
use GetOpt\Operand;
use GetOpt\ArgumentException;

define('NAME', 'smog');
define('VERSION', '1.0.0-alpha');
define('SMOG_ROOT', __DIR__);

require __DIR__ . '/vendor/autoload.php';

$getopt = new GetOpt([
    [null, 'version', GetOpt::NO_ARGUMENT, 'Show version information and quit'],
    ['?', 'help', GetOpt::NO_ARGUMENT, 'Show this help and quit'],
]);

$getopt->addCommands([
    Command::create('bean', '\smog\module\Bean::create')
        ->addOptions([
            ['m', 'module', GetOpt::REQUIRED_ARGUMENT
                , 'Module name'],
            ['o', 'object', GetOpt::REQUIRED_ARGUMENT
                , 'Object name'],
            ['t', 'table', GetOpt::REQUIRED_ARGUMENT
                , 'Table name'],
        ])
        ->setDescription('Creates bean class file.'),
    Command::create('vardefs', '\smog\module\Vardefs::create')
        ->addOptions([
            ['m', 'module', GetOpt::REQUIRED_ARGUMENT
                , 'Module name'],
            ['o', 'object', GetOpt::REQUIRED_ARGUMENT
                , 'Object name'],
            ['f', 'fields', GetOpt::REQUIRED_ARGUMENT
                , 'Fields'],
            ['t', 'table', GetOpt::REQUIRED_ARGUMENT
                , 'Table name'],
            ['b', 'implements', GetOpt::REQUIRED_ARGUMENT
                , 'Templates'],
        ])
        ->setDescription('Creates vardefs.'),
    Command::create('menu', '\smog\module\Menu::create')
        ->setDescription('Creates menu.'),
    Command::create('language', '\smog\module\Language::create')
        ->setDescription('Creates languages.'),
    Command::create('view.detail', '\smog\module\view\Detail::create')
        ->setDescription('Creates view.detail.php'),
    Command::create('view.edit', '\smog\module\view\Edit::create')
        ->setDescription('Creates view.edit.php'),
    Command::create('controller', '\smog\module\Controller::create')
        ->setDescription('Creates controller.php'),
    Command::create('metadata', '\smog\module\Metadata::createAll')
        ->setDescription('Creates all metadata files'),
    Command::create('related', '\smog\custom\Extension\RelatedToModule::create')
        ->setDescription('Creates links and subpanels for this module in other modules'),
    Command::create('m2m', '\smog\custom\Extension\M2M::create')
        ->addOptions([
            ['l', 'left', GetOpt::REQUIRED_ARGUMENT
                , 'Left Hand Side Module name'],
            ['r', 'right', GetOpt::REQUIRED_ARGUMENT
                , 'Right Hand Side Module name'],
        ])
        ->setDescription('Creates many-to-many relationships'),
]);

try {
    $getopt->process();
} catch (ArgumentException $ex) {
    fwrite(STDERR, $ex->getMessage() . PHP_EOL);
    fwrite(STDERR, 'Use --help option' . PHP_EOL);
    exit(1);
}

if ($getopt->getOption('version')) {
    echo sprintf('%s: %s' . PHP_EOL, NAME, VERSION);
    exit;
}

if ($getopt->getOption('help')) {
    echo $getopt->getHelpText();
    exit;
}

$command = $getopt->getCommand();
if (!$command) {
    fwrite(STDERR, 'Use --help option' . PHP_EOL);
    exit(2);
} else {
    try {
        call_user_func($command->getHandler(), $getopt->getOptions());
    }
    catch (\smog\ArgumentException $ex) {
        fwrite(STDERR, $ex->getMessage() . PHP_EOL);
        fwrite(STDERR, 'See ' . NAME . ' ' . $command->getName(). ' --help' . PHP_EOL);
        exit(3);
    }
    catch (\Exception $ex) {
        fwrite(STDERR, $ex->getMessage() . PHP_EOL);
        exit(5);
    }
}
