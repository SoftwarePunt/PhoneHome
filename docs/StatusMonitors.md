# Status monitors

Project specific status monitors can be registered to collect additional information. The results of these monitors will
be included in the collected data.

## Adding status monitors

Status monitors must inherit from `BaseStatusMonitor` and implement the `performCheck()` method, which returns a
`StatusMonitorResult`, containing a result code and optional message with details.

A minimal example of a status monitor is shown below:

```php
use SoftwarePunt\PhoneHome\Status\BaseStatusMonitor;

class SampleMonitor extends BaseStatusMonitor
{
    #[\Override] public function performCheck(): StatusMonitorResult
    {
        return new StatusMonitorResult(
            code: StatusMonitorCode::HEALTHY,
            message: "Lookin' good!"
        );
    }
}
```

To register a status monitor, include it in the `PhoneHome` client configuration by calling `addStatusMonitor()` with an
instance of the monitor before calling `send()`.

```php
$phoneHome = new PhoneHome('SET_ME');
$phoneHome->addStatusMonitor(new SampleMonitor());
$phoneHome->send();
```

Default implementations are provided for common use cases and are detailed below.

## File status monitor

The `FileStatusMonitor` checks for the existence and readability of a file on disk, with some optional constraints such as last modified time (max age).

### Use cases

Example use cases for the file monitor:

- **Configuration files:** Ensure that a configuration file exists and is readable.
- **Log files:** Check that a log file exists and is readable, and optionally check its size or last modified time.
- **Background jobs:** Upon successful completion of a background job, touch a file on disk to indicate success.

### Usage

```php
use SoftwarePunt\PhoneHome\Status\FileStatusMonitor;

// Basic monitor: checks that a file exists and is readable
$monitor = new FileStatusMonitor(
    id: "my_monitor",
    description: "Example of a basic file monitor",
    filePath: '/path/to/file.txt',
);

// Constraint: check that the file last modified time is not older than 1 hour
$monitor->expectMaxAge(new \DateInterval('PT1H'));

// Constraint: check that the file size has a minimum byte size of 1kb
$monitor->expectMinSizeBytes(1024);
// Alternate: expect a non empty file (equivalent to `expectMinSizeBytes(1)`):
$monitor->expectNonEmptyFile();
```

### Result codes

| Code        | Reason                                                         |
|-------------|----------------------------------------------------------------|
| `HEALTHY`   | File exists and is readable, all constraints passed            |
| `UNHEALTHY` | File does not exist, is not readable, or any constraint failed |