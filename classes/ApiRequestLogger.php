<?php

declare(strict_types=1);

namespace Xitara\ERecht24\Classes;

use Log;
use Throwable;

final class ApiRequestLogger
{
    public static function started(string $operation, string $credentialMode, array $context = []) : void
    {
        Log::debug('eRecht24 API request started.', self::context($operation, $credentialMode, $context));
    }

    public static function succeeded(
        string $operation,
        string $credentialMode,
        ?int $statusCode,
        array $context = []
    ) : void {
        Log::info('eRecht24 API request succeeded.', self::context($operation, $credentialMode, array_merge(
            $context,
            ['status_code' => $statusCode]
        )));
    }

    public static function failed(
        string $operation,
        string $credentialMode,
        ?int $statusCode,
        string $error,
        array $context = []
    ) : void {
        Log::error('eRecht24 API request failed.', self::context($operation, $credentialMode, array_merge(
            $context,
            [
                'status_code' => $statusCode,
                'error' => $error,
            ]
        )));
    }

    public static function exception(
        string $operation,
        string $credentialMode,
        Throwable $exception,
        array $context = []
    ) : void {
        Log::error('eRecht24 API request raised an exception.', self::context(
            $operation,
            $credentialMode,
            array_merge($context, ['exception_class' => get_class($exception)])
        ));
    }

    public static function skipped(
        string $operation,
        string $credentialMode,
        string $reason,
        array $context = []
    ) : void {
        Log::warning('eRecht24 API request skipped.', self::context($operation, $credentialMode, array_merge(
            $context,
            ['reason' => $reason]
        )));
    }

    private static function context(string $operation, string $credentialMode, array $context) : array
    {
        return array_merge([
            'operation' => $operation,
            'credential_mode' => $credentialMode,
        ], $context);
    }
}
