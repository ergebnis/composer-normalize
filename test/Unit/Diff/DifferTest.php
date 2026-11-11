<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018-2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/composer-normalize
 */

namespace Ergebnis\Composer\Normalize\Test\Unit\Diff;

use Ergebnis\Composer\Normalize\Diff;
use Ergebnis\Composer\Normalize\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\Composer\Normalize\Diff\Differ
 */
final class DifferTest extends Framework\TestCase
{
    use Test\Util\Helper;

    public function testDiffReturnsEmptyStringWhenFromAndToAreIdentical(): void
    {
        $faker = self::faker();

        $string = \sprintf(
            "%s\n%s\n",
            $faker->sentence(),
            $faker->sentence(),
        );

        $differ = new Diff\Differ(
            $faker->word(),
            $faker->word(),
        );

        $diff = $differ->diff(
            $string,
            $string,
        );

        self::assertSame('', $diff);
    }

    /**
     * @dataProvider provideFromToAndHunks
     */
    public function testDiffReturnsUnifiedDiffWhenFromAndToAreDifferent(
        string $from,
        string $to,
        string $hunks
    ): void {
        $faker = self::faker();

        $fromLabel = $faker->word();
        $toLabel = $faker->word();

        $differ = new Diff\Differ(
            $fromLabel,
            $toLabel,
        );

        $expected = \sprintf(
            "--- %s\n+++ %s\n%s",
            $fromLabel,
            $toLabel,
            $hunks,
        );

        $diff = $differ->diff(
            $from,
            $to,
        );

        self::assertSame($expected, $diff);
    }

    /**
     * @return \Generator<string, array{0: string, 1: string, 2: string}>
     */
    public static function provideFromToAndHunks(): iterable
    {
        yield 'line changed' => [
            <<<'TXT'
a
b
c
d
e
f
g

TXT,
            <<<'TXT'
a
b
c
x
e
f
g

TXT,
            <<<'TXT'
@@ -1,7 +1,7 @@
 a
 b
 c
-d
+x
 e
 f
 g

TXT,
        ];

        yield 'line added at end' => [
            <<<'TXT'
a
b
c
d
e

TXT,
            <<<'TXT'
a
b
c
d
e
f

TXT,
            <<<'TXT'
@@ -3,3 +3,4 @@
 c
 d
 e
+f

TXT,
        ];

        yield 'line removed at start' => [
            <<<'TXT'
a
b
c
d
e

TXT,
            <<<'TXT'
b
c
d
e

TXT,
            <<<'TXT'
@@ -1,4 +1,3 @@
-a
 b
 c
 d

TXT,
        ];

        yield 'lines moved' => [
            <<<'TXT'
a
b
c

TXT,
            <<<'TXT'
c
a
b

TXT,
            <<<'TXT'
@@ -1,3 +1,3 @@
+c
 a
 b
-c

TXT,
        ];

        yield 'changes separated by fewer than six unchanged lines' => [
            <<<'TXT'
a
b
c
d
e
f
g

TXT,
            <<<'TXT'
x
b
c
d
e
f
y

TXT,
            <<<'TXT'
@@ -1,7 +1,7 @@
-a
+x
 b
 c
 d
 e
 f
-g
+y

TXT,
        ];

        yield 'changes separated by six unchanged lines' => [
            <<<'TXT'
a
b
c
d
e
f
g
h

TXT,
            <<<'TXT'
x
b
c
d
e
f
g
y

TXT,
            <<<'TXT'
@@ -1,4 +1,4 @@
-a
+x
 b
 c
 d
@@ -5,4 +5,4 @@
 e
 f
 g
-h
+y

TXT,
        ];

        yield 'from is empty' => [
            '',
            <<<'TXT'
a
b

TXT,
            <<<'TXT'
@@ -1,0 +1,2 @@
+a
+b

TXT,
        ];

        yield 'to is empty' => [
            <<<'TXT'
a
b

TXT,
            '',
            <<<'TXT'
@@ -1,2 +1,0 @@
-a
-b

TXT,
        ];

        yield 'final new line added' => [
            <<<'TXT'
a
b
TXT,
            <<<'TXT'
a
b

TXT,
            <<<'TXT'
@@ -1,2 +1,2 @@
 a
-b
\ No newline at end of file
+b

TXT,
        ];

        yield 'final new line removed' => [
            <<<'TXT'
a
b

TXT,
            <<<'TXT'
a
b
TXT,
            <<<'TXT'
@@ -1,2 +1,2 @@
 a
-b
+b
\ No newline at end of file

TXT,
        ];

        yield 'carriage return and line feed' => [
            "a\r\nb\r\nc\r\n",
            "a\r\nx\r\nc\r\n",
            "@@ -1,3 +1,3 @@\n a\r\n-b\r\n+x\r\n c\r\n",
        ];
    }
}
