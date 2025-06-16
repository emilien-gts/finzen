<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_superfluous_phpdoc_tags' => false,
        'phpdoc_to_comment' => false,
        'phpdoc_summary' => false,
        'phpdoc_types' => false,
        'phpdoc_no_alias_tag' => false,
    ])
    ->setFinder($finder);
