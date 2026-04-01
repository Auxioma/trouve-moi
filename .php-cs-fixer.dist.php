<?php

$fileHeaderComment = <<<'COMMENT'
Copyright (c) 2026 Auxioma Web Agency
https://trouvemoi.eu

Ce fichier fait partie du projet Trouvemoi.eu développé par Auxioma Web Agency.
Tous droits réservés.

Ce code source, son architecture, sa structure, ses scripts et ses composants
sont la propriété exclusive de Auxioma Web Agency et de ses partenaires.

Toute reproduction, modification, distribution, publication ou utilisation,
totale ou partielle, sans autorisation écrite préalable est strictement interdite.

Ce code est confidentiel et propriétaire.
Droit applicable : Monde.
COMMENT;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude([
        'config',
        'var',
        'vendor',
        'node_modules',
        'public/bundles',
        'public/build',
    ])
    ->notPath([
        'public/index.php',
        'importmap.php',
    ]);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,

        'header_comment' => [
            'header' => $fileHeaderComment,
            'separate' => 'both',
            'comment_type' => 'PHPDoc',
        ],

        'linebreak_after_opening_tag' => true,
        'mb_str_functions' => true,
        'no_php4_constructor' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'php_unit_strict' => true,
        'phpdoc_order' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'blank_line_between_import_groups' => false,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/var/.php-cs-fixer.cache');