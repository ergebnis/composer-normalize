<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2023 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/composer-normalize
 */

namespace Ergebnis\Composer\Normalize\Test\Util;

final class Scenario
{
    private function __construct(
        private readonly CommandInvocation $commandInvocation,
        private readonly State $initialState,
    ) {
    }

    public static function fromCommandInvocationAndInitialState(
        CommandInvocation $commandInvocation,
        State $initialState,
    ): self {
        return new self(
            $commandInvocation,
            $initialState,
        );
    }

    public function directory(): Directory
    {
        return $this->initialState->directory();
    }

    public function commandInvocation(): CommandInvocation
    {
        return $this->commandInvocation;
    }

    public function initialState(): State
    {
        return $this->initialState;
    }

    public function currentState(): State
    {
        return State::fromDirectory($this->initialState->directory());
    }

    /**
     * @param array<string, bool|int|string> $parameters
     *
     * @return array<string, bool|int|string>
     */
    public function consoleParametersWith(array $parameters): array
    {
        return \array_merge(
            $this->consoleParameters(),
            $parameters,
        );
    }

    /**
     * @return array<string, bool|string>
     */
    public function consoleParameters(): array
    {
        $parameters = [
            'command' => 'normalize',
        ];

        if ($this->commandInvocation->is(CommandInvocation::usingFileArgument())) {
            return \array_merge($parameters, [
                'file' => \sprintf(
                    '%s/composer.json',
                    $this->initialState->directory()->path(),
                ),
            ]);
        }

        if ($this->commandInvocation->is(CommandInvocation::usingWorkingDirectoryOption())) {
            return \array_merge($parameters, [
                '--working-dir' => $this->initialState->directory()->path(),
            ]);
        }

        return $parameters;
    }

    public function composerJsonFileReference(): string
    {
        if ($this->commandInvocation->is(CommandInvocation::usingFileArgument())) {
            return \sprintf(
                '%s/composer.json',
                $this->initialState->directory()->path(),
            );
        }

        return './composer.json';
    }
}
