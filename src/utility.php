<?php
declare(strict_types = 1);

/**
 * Read and ENV file an returns the content as array
 */
function read_env_file(string $file): array
{
    $result = [];
    $rows = file($file);
    foreach ($rows as $row) {
        list($key, $value) = explode('=', $row, 2);
        $result[trim($key)] = trim($value);
    }
    return $result;
}
/**
 * Returns the number of core available in the system
 */
function get_processor_cores_number(): int
{
    if (PHP_OS_FAMILY == 'Windows') {
        $cores = shell_exec('echo %NUMBER_OF_PROCESSORS%');
    } else {
        $cores = shell_exec('nproc');
    }

    return (int) $cores;
}