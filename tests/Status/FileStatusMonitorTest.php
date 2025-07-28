<?php

namespace Status;

use SoftwarePunt\PhoneHome\Status\FileStatusMonitor;
use PHPUnit\Framework\TestCase;
use SoftwarePunt\PhoneHome\Status\StatusMonitorCode;

class FileStatusMonitorTest extends TestCase
{
    public function testBasicMonitor(): void
    {
        $rndTempFile = sys_get_temp_dir() . '/' . uniqid('testfile_', true) . '.txt';
        @unlink($rndTempFile);

        $monitor = new FileStatusMonitor(
            id: 'test',
            description: 'test',
            filePath: $rndTempFile
        );

        $this->assertEquals(
            expected: StatusMonitorCode::UNHEALTHY->value,
            actual: $monitor->performCheck()->code->value,
            message: 'FileStatusMonitor should return UNHEALTHY for non-existing file'
        );

        try {
            @touch($rndTempFile);
            $this->assertFileExists($rndTempFile, 'Sanity check: Temporary file should exist after touch');

            $this->assertEquals(
                expected: StatusMonitorCode::HEALTHY->value,
                actual: $monitor->performCheck()->code->value,
                message: 'FileStatusMonitor should return HEALTHY for existing and readable file'
            );
        } finally {
            @unlink($rndTempFile);
        }
    }

    public function testMonitorWithMaxAge(): void
    {
        $rndTempFile = sys_get_temp_dir() . '/' . uniqid('testfile_', true) . '.txt';
        @unlink($rndTempFile);

        $monitor = new FileStatusMonitor(
            id: 'test',
            description: 'test',
            filePath: $rndTempFile,
            expectedMaxAge: new \DateInterval('PT1H') // 1 hour
        );

        try {
            @touch($rndTempFile, time() - 7200); // Set file age to 2 hours

            $this->assertFileExists($rndTempFile,
                'Sanity check: Temporary file should exist after touch');
            $this->assertLessThanOrEqual(time() - 7200, filemtime($rndTempFile),
                'Sanity check: filemtime should be older than 1 hour');

            $this->assertEquals(
                expected: StatusMonitorCode::UNHEALTHY->value,
                actual: $monitor->performCheck()->code->value,
                message: 'FileStatusMonitor should return UNHEALTHY for file older than expected max age'
            );

            @touch($rndTempFile, time() - (3600 - 60)); // Set file age to 1 hour - 1 minute

            $this->assertEquals(
                expected: StatusMonitorCode::HEALTHY->value,
                actual: $monitor->performCheck()->code->value,
                message: 'FileStatusMonitor should return HEALTHY for file exactly at expected max age'
            );
        } finally {
            @unlink($rndTempFile);
        }
    }
}
