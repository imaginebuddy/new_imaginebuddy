<?php

namespace App\Services;

class SearchSecurityGuard
{
    /**
     * Max allowable characters for any search query.
     */
    const MAX_QUERY_LENGTH = 100;

    /**
     * Common SQL injection patterns used by vulnerability scanners and attackers.
     */
    protected static $sqlInjectionPatterns = [
        // Classic UNION SELECT attacks
        '/\bUNION\s+(ALL\s+)?SELECT\b/i',
        // Stacked subqueries & schema probing
        '/\bSELECT\b.*\b(FROM|TABLE|SCHEMA|INFORMATION_SCHEMA)\b/i',
        // Database manipulation keywords
        '/\b(INSERT\s+INTO|UPDATE\s+\w+\s+SET|DELETE\s+FROM|DROP\s+(TABLE|DATABASE)|ALTER\s+TABLE|TRUNCATE\s+TABLE)\b/i',
        // MySQL error-based injection
        '/\b(EXTRACTVALUE|UPDATEXML)\s*\(/i',
        // Time-based blind injection
        '/\b(SLEEP|BENCHMARK)\s*\(\s*\d+/i',
        '/\bWAITFOR\s+DELAY\b/i',
        // Function-based exfiltration / type casting
        '/\bCONCAT(_WS)?\s*\(.*0x[0-9a-fA-F]+/i',
        '/\bCAST\s*\(.*AS\s+(VARCHAR|CHAR|INT|SIGNED|UNSIGNED)/i',
        '/\bCONVERT\s*\(.*USING/i',
        '/\b(ELT|MAKE_SET|MID|SUBSTR|SUBSTRING)\s*\(/i',
        '/\b(LOAD_FILE|INTO\s+OUTFILE|INTO\s+DUMPFILE)\b/i',
        '/\b(CHAR|CHR|ASCII)\s*\(\s*\d+/i',
        // Hexadecimal string literals used by scanners (sqlmap, etc.)
        '/0x[0-9a-fA-F]{4,}/i',
        // SQL comments used to truncate queries (-- - or /* */)
        '/(--\s*|--$|#|\/\*|\*\/)/i',
        // SQL tautologies (e.g., '1'='1', 'a'='a', 1=1, 8178=8178)
        '/\b(OR|AND)\s+[\'"]?([a-zA-Z0-9_]+)[\'"]?\s*=\s*[\'"]?\2[\'"]?/i',
        '/\b(OR|AND)\s+\d+\s*=\s*\d+/i',
        // String concatenation tricks: 'xsrrag'||(6484*3004)
        '/\|\|\s*\(?\d+[\*\+\-\/]\d+\)?/i',
        // Arithmetic injection probes like (6484*3004) inside string quotes
        '/[\'"]\s*[\+\|]+\s*\(?\d+[\*\+\-\/]\d+\)?/i',
    ];

    /**
     * Cross-Site Scripting (XSS) patterns.
     */
    protected static $xssPatterns = [
        '/<\s*script\b[^>]*>/i',
        '/<\/\s*script\b[^>]*>/i',
        '/javascript\s*:/i',
        '/data\s*:\s*text\/html/i',
        '/vbscript\s*:/i',
        '/\bon(load|error|click|focus|blur|mouse\w+|key\w+)\s*=/i',
        '/<\s*(iframe|object|embed|svg|img|body|input|link|meta)\b/i',
    ];

    /**
     * Determine whether the search query is suspicious, malicious, or an attack probe.
     *
     * @param string|null $query
     * @return bool
     */
    public static function isMalicious(?string $query): bool
    {
        if (empty($query)) {
            return false;
        }

        $q = trim($query);

        // 1. Check length limit (legitimate visual prompt searches are concise)
        if (mb_strlen($q) > self::MAX_QUERY_LENGTH) {
            return true;
        }

        // 2. Check for null bytes or control characters
        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $q)) {
            return true;
        }

        // 3. Test SQL injection signatures
        foreach (self::$sqlInjectionPatterns as $pattern) {
            if (preg_match($pattern, $q)) {
                return true;
            }
        }

        // 4. Test XSS / Script signatures
        foreach (self::$xssPatterns as $pattern) {
            if (preg_match($pattern, $q)) {
                return true;
            }
        }

        // 5. Ratio check: legitimate search terms shouldn't consist primarily of SQL/script syntax symbols
        $symbolCount = preg_match_all('/[\'"`\-=\+<>\|\(\)\*\/\#\%\\\]/', $q);
        $totalLen = mb_strlen($q);
        if ($totalLen >= 10 && ($symbolCount / $totalLen) > 0.35) {
            return true;
        }

        return false;
    }

    /**
     * Sanitize a search query for display and safe usage.
     *
     * @param string|null $query
     * @return string
     */
    public static function sanitize(?string $query): string
    {
        if (empty($query)) {
            return '';
        }

        $q = trim($query);

        // If it was detected as malicious, completely scrub it
        if (self::isMalicious($q)) {
            return '';
        }

        // Truncate length
        $q = mb_substr($q, 0, self::MAX_QUERY_LENGTH);

        // Strip null bytes and control chars
        $q = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $q);

        // Strip HTML tags completely
        $q = strip_tags($q);

        return $q;
    }
}
