<?php

declare(strict_types=1);

namespace Debugging;

use Eventware\Tests\TestCase;

/**
 * Use this test to debug artisan commands during development.
 * Xdebug may not work properly with artisan commands,
 * so running them through a test is a good workaround.
 */
class CommandTest extends TestCase
{
    public function test_the_command_to_debug(): void
    {
        $this->artisan('calendars:sync')->assertExitCode(0);
    }
}
