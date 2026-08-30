<?php

namespace App\Services;

class SearchQueryEngine
{
    /**
     * Common stop-words that carry zero semantic weight in image searches.
     *
     * @return array
     */
    public static function getStopWords()
    {
        return [
            'in', 'at', 'on', 'the', 'with', 'a', 'an', 'by', 'for', 'of', 'to',
            'is', 'and', 'or', 'from', 'this', 'that', 'it', 'set', 'photo',
            'image', 'picture', 'photography', 'shot'
        ];
    }

    /**
     * Clean raw query input by stripping reserved MySQL symbols and extra spaces.
     *
     * @param string $term
     * @return string
     */
    public static function cleanQuery($term)
    {
        $term = mb_strtolower(trim($term));
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~', '*', '"', "'", '?', '!', '.', ','];
        $cleaned = str_replace($reservedSymbols, ' ', $term);
        return preg_replace('/\s+/', ' ', $cleaned);
    }

    /**
     * Tokenize search string into clean terms, removing stop-words.
     *
     * @param string $term
     * @return array
     */
    public static function tokenize($term)
    {
        $cleaned = static::cleanQuery($term);
        if (empty($cleaned)) {
            return [];
        }

        $words = explode(' ', $cleaned);
        $stopWords = static::getStopWords();

        $tokens = [];
        foreach ($words as $w) {
            $w = trim($w);
            if (strlen($w) >= 2 && !in_array($w, $stopWords)) {
                $tokens[] = $w;
            }
        }

        return array_values(array_unique($tokens));
    }

    /**
     * Expand query term using deterministic synonyms from config/search_synonyms.php.
     *
     * @param string $term
     * @return array
     */
    public static function getExpandedTerms($term)
    {
        $cleaned = static::cleanQuery($term);
        $synonymsMap = config('search_synonyms', []);
        
        $expanded = [$cleaned];

        // 1. Direct multi-word key match
        if (isset($synonymsMap[$cleaned])) {
            $expanded = array_merge($expanded, $synonymsMap[$cleaned]);
        }

        // 2. Token-level synonym lookup
        $tokens = static::tokenize($term);
        foreach ($tokens as $token) {
            if (isset($synonymsMap[$token])) {
                $expanded = array_merge($expanded, $synonymsMap[$token]);
            }
        }

        return array_values(array_unique(array_filter($expanded)));
    }

    /**
     * Build MySQL FULLTEXT Boolean mode search string with required (+) operators.
     *
     * @param string $term
     * @return string
     */
    public static function buildFulltextBooleanQuery($term)
    {
        $tokens = static::tokenize($term);
        if (empty($tokens)) {
            return static::cleanQuery($term);
        }

        $fulltextWords = [];
        foreach ($tokens as $token) {
            if (strlen($token) >= 2) {
                $fulltextWords[] = '+' . $token . '*';
            }
        }

        return implode(' ', $fulltextWords);
    }

    /**
     * Extract clean words for AND-based SQL LIKE queries.
     *
     * @param string $term
     * @return array
     */
    public static function getLikeTokens($term)
    {
        return static::tokenize($term);
    }
}
