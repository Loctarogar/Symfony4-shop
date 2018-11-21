<?php
declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->exclude('Resources')
    ->in(['src']);

return PhpCsFixer\Config::create()
    ->setRules(
        [
            '@PSR1'                        => true,
            '@PSR2'                        => true,
            '@Symfony'                     => true,
            '@PHP70Migration'              => true,
            '@PHP71Migration'              => true,
            '@DoctrineAnnotation'          => true,
            'strict_param'                 => true,
            'strict_comparison'            => true,
            'declare_strict_types'         => true,
            'array_indentation'            => true,
            'combine_consecutive_issets'   => true,
            'combine_consecutive_unsets'   => true,
            'mb_str_functions'             => true,
            'fully_qualified_strict_types' => false,
            'blank_line_after_opening_tag' => false,
            'phpdoc_return_self_reference' => true,
            'phpdoc_annotation_without_dot' => true,
            'blank_line_before_statement'  => [
                'statements' => [
                    'break',
                    'continue',
                    'return',
                    'throw',
                    'try',
                    'yield',
                ],
            ],
            'array_syntax'                 => ['syntax' => 'short'],
            'concat_space'                 => ['spacing' => 'one'],
        ]
    )
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->setFinder($finder);