<?php

namespace App\Services;

class AdmsRequestParser
{
    /**
     * Parse raw attendance logs submitted via POST table=ATTLOG
     * Format per line: PIN \t DateTime \t Status \t VerifyType \t WorkCode \t Reserved1 \t Reserved2
     */
    public function parseAttendanceLogs(string $body): array
    {
        $logs = [];
        $lines = preg_split('/\r\n|\r|\n/', trim($body));

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $fields = explode("\t", $line);

            if (count($fields) >= 4) {
                $logs[] = [
                    'pin' => trim($fields[0] ?? ''),
                    'punched_at' => trim($fields[1] ?? ''),
                    'status' => (int) trim($fields[2] ?? 0),
                    'verify_type' => (int) trim($fields[3] ?? 0),
                    'work_code' => isset($fields[4]) ? (int) trim($fields[4]) : null,
                    'reserved_1' => isset($fields[5]) ? trim($fields[5]) : null,
                    'reserved_2' => isset($fields[6]) ? trim($fields[6]) : null,
                    'raw_data' => $line,
                ];
            }
        }

        return $logs;
    }

    /**
     * Parse raw user data submitted via POST table=USER or table=USERINFO
     */
    public function parseUsers(string $body): array
    {
        $users = [];
        $lines = preg_split('/\r\n|\r|\n/', trim($body));

        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            $fields = explode("\t", $line);

            if (count($fields) >= 1) {
                $user = [];
                foreach ($fields as $field) {
                    $pair = explode('=', $field, 2);
                    if (count($pair) === 2) {
                        $user[trim($pair[0])] = trim($pair[1]);
                    }
                }

                if (isset($user['PIN']) || isset($user['User'])) {
                    $users[] = [
                        'pin' => $user['PIN'] ?? $user['User'] ?? '',
                        'name' => $user['Name'] ?? null,
                        'password' => $user['Passwd'] ?? null,
                        'card_number' => $user['Card'] ?? null,
                        'privilege' => isset($user['Pri']) ? (int) $user['Pri'] : 0,
                    ];
                }
            }
        }

        return $users;
    }
}
