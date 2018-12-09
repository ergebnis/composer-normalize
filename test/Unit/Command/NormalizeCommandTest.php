<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/composer-normalize
 */

namespace Localheinz\Composer\Normalize\Test\Unit\Command;

use Composer\Command;
use Composer\Factory;
use Localheinz\Composer\Normalize\Command\NormalizeCommand;
use Localheinz\Json\Normalizer;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 */
final class NormalizeCommandTest extends Framework\TestCase
{
    use Helper;

    public function testExtendsBaseCommand(): void
    {
        $this->assertClassExtends(Command\BaseCommand::class, NormalizeCommand::class);
    }

    public function testHasNameAndDescription(): void
    {
        $command = new NormalizeCommand(
            $this->prophesize(Factory::class)->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $this->assertSame('normalize', $command->getName());
        $this->assertSame('Normalizes composer.json according to its JSON schema (https://getcomposer.org/schema.json).', $command->getDescription());
    }

    public function testHasFileArgument(): void
    {
        $command = new NormalizeCommand(
            $this->prophesize(Factory::class)->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasArgument('file'));

        $argument = $definition->getArgument('file');

        $this->assertFalse($argument->isRequired());
        $this->assertSame('Path to composer.json file', $argument->getDescription());
        $this->assertNull($argument->getDefault());
    }

    public function testHasDryRunOption(): void
    {
        $command = new NormalizeCommand(
            $this->prophesize(Factory::class)->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('dry-run'));

        $option = $definition->getOption('dry-run');

        $this->assertNull($option->getShortcut());
        $this->assertFalse($option->isValueRequired());
        $this->assertFalse($option->getDefault());
        $this->assertSame('Show the results of normalizing, but do not modify any files', $option->getDescription());
    }

    public function testHasIndentSizeOption(): void
    {
        $command = new NormalizeCommand(
            $this->prophesize(Factory::class)->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('indent-size'));

        $option = $definition->getOption('indent-size');

        $this->assertNull($option->getShortcut());
        $this->assertTrue($option->isValueRequired());
        $this->assertNull($option->getDefault());
        $this->assertSame('Indent size (an integer greater than 0); should be used with the --indent-style option', $option->getDescription());
    }

    public function testHasIndentStyleOption(): void
    {
        $command = new NormalizeCommand(
            $this->prophesize(Factory::class)->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('indent-style'));

        $option = $definition->getOption('indent-style');

        $this->assertNull($option->getShortcut());
        $this->assertTrue($option->isValueRequired());
        $this->assertNull($option->getDefault());

        $indentStyles = [
            'space' => ' ',
            'tab' => "\t",
        ];

        $description = \sprintf(
            'Indent style (one of "%s"); should be used with the --indent-size option',
            \implode('", "', \array_keys($indentStyles))
        );

        $this->assertSame($description, $option->getDescription());
    }

    public function testHasNoUpdateLockOption(): void
    {
        $command = new NormalizeCommand(
            $this->prophesize(Factory::class)->reveal(),
            $this->prophesize(Normalizer\NormalizerInterface::class)->reveal()
        );

        $definition = $command->getDefinition();

        $this->assertTrue($definition->hasOption('no-update-lock'));

        $option = $definition->getOption('no-update-lock');

        $this->assertNull($option->getShortcut());
        $this->assertFalse($option->isValueRequired());
        $this->assertFalse($option->getDefault());
        $this->assertSame('Do not update lock file if it exists', $option->getDescription());
    }
}
