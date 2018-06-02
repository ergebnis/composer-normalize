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

namespace Localheinz\Composer\Normalize\Test\Unit;

use Composer\Composer;
use Composer\IO;
use Composer\Plugin;
use Localheinz\Composer\Normalize\Command\NormalizeCommand;
use Localheinz\Composer\Normalize\NormalizePlugin;
use Localheinz\Composer\Normalize\Normalizer;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;

/**
 * @internal
 */
final class NormalizePluginTest extends Framework\TestCase
{
    use Helper;

    /**
     * @dataProvider providerInterfaceName
     *
     * @param string $interfaceName
     */
    public function testImplementsPluginInterface(string $interfaceName)
    {
        $this->assertClassImplementsInterface($interfaceName, NormalizePlugin::class);
    }

    public function providerInterfaceName(): \Generator
    {
        $interfaces = [
            Plugin\PluginInterface::class,
            Plugin\Capable::class,
            Plugin\Capability\CommandProvider::class,
        ];

        foreach ($interfaces as $interface) {
            yield $interface => [
                $interface,
            ];
        }
    }

    public function testGetCapabilitiesReturnsCapabilities(): void
    {
        $plugin = new NormalizePlugin();

        $plugin->activate(
            $this->prophesize(Composer::class)->reveal(),
            $this->prophesize(IO\IOInterface::class)->reveal()
        );

        $expected = [
            Plugin\Capability\CommandProvider::class => NormalizePlugin::class,
        ];

        $this->assertSame($expected, $plugin->getCapabilities());
    }

    public function testProvidesNormalizeCommand(): void
    {
        $plugin = new NormalizePlugin();

        $plugin->activate(
            $this->prophesize(Composer::class)->reveal(),
            $this->prophesize(IO\IOInterface::class)->reveal()
        );

        $commands = $plugin->getCommands();

        $this->assertCount(1, $commands);

        $command = \array_shift($commands);

        $this->assertInstanceOf(NormalizeCommand::class, $command);
        $this->assertAttributeInstanceOf(Normalizer\ComposerJsonNormalizer::class, 'normalizer', $command);
    }
}
