<?php

/**
 * Copyright (c) 2026 Auxioma Web Agency
 * https://trouvemoi.eu
 *
 * Ce fichier fait partie du projet Trouvemoi.eu développé par Auxioma Web Agency.
 * Tous droits réservés.
 *
 * Ce code source, son architecture, sa structure, ses scripts et ses composants
 * sont la propriété exclusive de Auxioma Web Agency et de ses partenaires.
 *
 * Toute reproduction, modification, distribution, publication ou utilisation,
 * totale ou partielle, sans autorisation écrite préalable est strictement interdite.
 *
 * Ce code est confidentiel et propriétaire.
 * Droit applicable : Monde.
 */

use TwigCsFixer\Config\Config;
use TwigCsFixer\File\Finder;
use TwigCsFixer\Rules\File\FileExtensionRule;
use TwigCsFixer\Rules\Punctuation\PunctuationSpacingRule;
use TwigCsFixer\Rules\Whitespace\EmptyLinesRule;
use TwigCsFixer\Ruleset\Ruleset;
use TwigCsFixer\Standard\TwigCsFixer;

$ruleset = new Ruleset();

// Standard principal
$ruleset->addStandard(new TwigCsFixer());

// Exemple : ajouter une règle
$ruleset->addRule(new FileExtensionRule());

// Exemple : retirer une règle du standard si tu ne la veux pas
// $ruleset->removeRule(EmptyLinesRule::class);

// Exemple : surcharger une règle existante
$ruleset->overrideRule(new PunctuationSpacingRule(
    ['}' => 1],
    ['{' => 1],
));

$finder = new Finder();
$finder
    ->in(__DIR__.'/templates')
    ->exclude('vendor')
    ->exclude('var');

$config = new Config();
$config->setRuleset($ruleset);
$config->setFinder($finder);

// optionnel
// $config->allowNonFixableRules();
// $config->setCacheFile(__DIR__ . '/.twig-cs-fixer.cache');

return $config;
    