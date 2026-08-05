<?php

declare(strict_types=1);

namespace Xitara\ERecht24\Classes;

use Xitara\ERecht24\Models\Settings;
use Xitara\ERecht24\Models\Text;

class StatusRepository
{
    private const STATUS_KEY = 'document_statuses';
    private const MESSAGE_KEY = 'push_message';

    public static function markDocumentSuccess(
        string $type,
        array $languages,
        array $changed,
        string $warning,
        string $source,
        ?string $providerModifiedAt
    ) : void {
        $statuses = self::statuses();
        $now = date('c');

        foreach ($languages as $lang) {
            $isChanged = !empty($changed[$lang]);
            $reason = null;
            $message = null;

            if ($warning !== '') {
                $reason = 'provider_warning';
                $message = $warning;
            } elseif ($isChanged) {
                $reason = 'changed';
                $message = trans('xitara.erecht24::lang.dashboard.warning_changed');
            }

            $statuses[self::key($type, $lang)] = [
                'state' => $reason === null ? 'current' : 'warning',
                'reason' => $reason,
                'message' => $message,
                'checked_at' => $now,
                'changed_at' => $isChanged ? $now : null,
                'provider_modified_at' => $providerModifiedAt,
                'source' => $source,
            ];
        }

        Settings::set(self::STATUS_KEY, $statuses);
    }

    public static function markDocumentError(
        string $type,
        array $languages,
        string $error,
        string $source
    ) : void {
        $statuses = self::statuses();
        $now = date('c');

        foreach ($languages as $lang) {
            $statuses[self::key($type, $lang)] = [
                'state' => 'warning',
                'reason' => 'error',
                'message' => trans('xitara.erecht24::lang.error.' . $error),
                'checked_at' => $now,
                'changed_at' => null,
                'provider_modified_at' => null,
                'source' => $source,
            ];
        }

        Settings::set(self::STATUS_KEY, $statuses);
    }

    public static function recordMessage(array $message) : void
    {
        Settings::set(self::MESSAGE_KEY, [
            'message' => (string) ($message['message_de'] ?? $message['message'] ?? ''),
            'action' => (string) ($message['call2action_de'] ?? $message['call2action'] ?? ''),
            'link' => (string) ($message['link'] ?? ''),
            'received_at' => date('c'),
        ]);
    }

    public static function clearMessage() : void
    {
        Settings::set(self::MESSAGE_KEY, null);
    }

    public static function recordTestResult(string $type, bool $success, string $message) : void
    {
        Settings::set('push_test_result', [
            'type' => $type,
            'success' => $success,
            'message' => $message,
            'tested_at' => date('c'),
        ]);
    }

    public static function rows() : array
    {
        $rows = [];
        $statuses = self::statuses();
        $documents = (array) Settings::get('docs', []);
        $languages = (array) Settings::get('langs', []);

        foreach ($documents as $type) {
            foreach ($languages as $lang) {
                $text = Text::where('name', $type)->where('lang', $lang)->first();
                $status = $statuses[self::key($type, $lang)] ?? null;

                if (!$text) {
                    $status = [
                        'state' => 'warning',
                        'reason' => 'missing',
                        'message' => trans('xitara.erecht24::lang.dashboard.warning_missing'),
                        'checked_at' => null,
                        'source' => null,
                    ];
                } elseif (!is_array($status)) {
                    $status = [
                        'state' => 'current',
                        'reason' => null,
                        'message' => null,
                        'checked_at' => $text->updated_at ? $text->updated_at->format('c') : null,
                        'source' => null,
                    ];
                }

                $status['type'] = $type;
                $status['lang'] = $lang;
                $status['label'] = self::documentLabel($type) . ' (' . self::languageLabel($lang) . ')';
                $rows[] = $status;
            }
        }

        $message = Settings::get(self::MESSAGE_KEY);
        if (is_array($message) && trim((string) ($message['message'] ?? '')) !== '') {
            $rows[] = [
                'state' => 'warning',
                'reason' => 'message',
                'message' => $message['message'],
                'checked_at' => $message['received_at'] ?? null,
                'source' => 'push',
                'type' => 'message',
                'lang' => null,
                'label' => trans('xitara.erecht24::lang.dashboard.provider_message'),
                'action' => $message['action'] ?? '',
                'link' => $message['link'] ?? '',
            ];
        }

        return $rows;
    }

    public static function warnings() : array
    {
        return array_values(array_filter(self::rows(), function ($row) {
            return ($row['state'] ?? null) === 'warning';
        }));
    }

    public static function acknowledgeChanges() : void
    {
        $statuses = self::statuses();

        foreach ($statuses as &$status) {
            if (($status['reason'] ?? null) !== 'changed') {
                continue;
            }

            $status['state'] = 'current';
            $status['reason'] = null;
            $status['message'] = null;
        }
        unset($status);

        Settings::set([
            self::STATUS_KEY => $statuses,
            self::MESSAGE_KEY => null,
        ]);
    }

    public static function hasAcknowledgeableWarnings() : bool
    {
        foreach (self::warnings() as $warning) {
            if (in_array($warning['reason'] ?? null, ['changed', 'message'], true)) {
                return true;
            }
        }

        return false;
    }

    private static function statuses() : array
    {
        $statuses = Settings::get(self::STATUS_KEY, []);

        return is_array($statuses) ? $statuses : [];
    }

    private static function key(string $type, string $lang) : string
    {
        return $type . '.' . $lang;
    }

    private static function documentLabel(string $type) : string
    {
        $labels = [
            'imprint' => trans('xitara.erecht24::lang.documents.imprint'),
            'privacyPolicy' => trans('xitara.erecht24::lang.documents.privacy_policy'),
            'privacyPolicySocialMedia' => trans('xitara.erecht24::lang.documents.privacy_policy_social_media'),
        ];

        return $labels[$type] ?? $type;
    }

    private static function languageLabel(string $lang) : string
    {
        $labels = [
            'de' => trans('xitara.erecht24::lang.languages.de'),
            'en' => trans('xitara.erecht24::lang.languages.en'),
        ];

        return $labels[$lang] ?? $lang;
    }
}
