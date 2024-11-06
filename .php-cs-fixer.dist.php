<?php

use PhpCsFixer\Config;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var');

return (new Config())
    ->setRiskyAllowed(true)
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setFinder($finder)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'yoda_style' => false, // Override @Symfony
        'phpdoc_align' => [
            'align' => 'left',
        ],
        'concat_space' => [
            'spacing' => 'one',
        ], // Override @Symfony
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ], // Override @Symfony
        'nullable_type_declaration' => [
            'syntax' => 'union',
        ], // Override @Symfony
    ]);
