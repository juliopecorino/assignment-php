<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $parameters = $containerConfigurator->parameters();

    $containerConfigurator->import(SetList::COMMON);
    $containerConfigurator->import(SetList::CLEAN_CODE);
    $containerConfigurator->import(SetList::SYMFONY);
    $containerConfigurator->import(SetList::PSR_12);
    $containerConfigurator->import(SetList::PHP_CS_FIXER);
    $containerConfigurator->import(SetList::DOCTRINE_ANNOTATIONS);
    $containerConfigurator->import(SetList::SYMFONY_RISKY);

    $services->set(\PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff::class);
    $services->set(\PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer::class);
    $services->set(\PhpCsFixer\Fixer\Phpdoc\PhpdocNoPackageFixer::class);
    //$services->set(UselessFunctionDocCommentSniff::class);
    //$services->set(PropertyTypeHintSniff::class);
    //$services->set(\SlevomatCodingStandard\Sniffs\Namespaces\FullyQualifiedClassNameAfterKeywordSniff::class);
    $services->set(\PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer::class);
    $services->set(\PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer::class);
    $services->set(\PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer::class);
    $services->set(\PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer::class);
    $services->set(\PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer::class);
    $services->set(\PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer::class);
    $services->set(\PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer::class);
    $services->set(\PhpCsFixer\Fixer\CastNotation\ModernizeTypesCastingFixer::class);
    $services->set(\PhpCsFixer\Fixer\Casing\ConstantCaseFixer::class);
    $services->set(\PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer::class);
    $services->set(\PhpCsFixer\Fixer\Operator\ConcatSpaceFixer::class)
        ->call(
            'configure',
            [
                [
                    'spacing' => 'none',
                ],
            ]
        );
    $services->set(\PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer::class)
        ->call(
            'configure',
            [
                [
                    'import_classes' => true,
                    'import_constants' => true,
                    'import_functions' => false,
                ],
            ]
        );

    $parameters->set(
        Option::PATHS,
        [
            __DIR__.'/src',
            __DIR__.'/tests',
        ]
    );

    $parameters->set(
        Option::SKIP,
        [
            \PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer::class,
            \PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer::class,
            PhpCsFixer\Fixer\PhpUnit\PhpUnitInternalClassFixer::class,
            PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer::class,
        ]
    );
};