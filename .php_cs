<?php

$finder = PhpCsFixer\Finder::create();
$finder
    ->exclude([
        'docker',
        'docs',
        'node_modules',
        'public',
        'resources',
        'storage',
        'vendor',
    ])->in(__DIR__);

$config = PhpCsFixer\Config::create();

$config->setFinder($finder);
$config->setRules(
    [
        '@Symfony' => true,
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'case',
                'continue',
                'declare',
                'default',
                'die',
                'do',
                'exit',
                'for',
                'foreach',
                'if',
                'return',
                'switch',
                'throw',
                'try',
                'while',
                'yield',
            ],
        ],
    ]
);

return $config;