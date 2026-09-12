<?php

namespace App\Http\Controllers\Traits;

use App\Services\SearchQueryEngine;

trait SearchTrait {

    /**
     * Scope a query that matches a full-text search of term across title, tags, and prompt with relevance scoring.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $term)
    {
        $booleanQuery = SearchQueryEngine::buildFulltextBooleanQuery($term);
        $expandedTerms = SearchQueryEngine::getExpandedTerms($term);

        if (empty($booleanQuery)) {
            return $this->scopeSearchLike($query, $term);
        }

        return $query->whereStatus('active')
            ->where(function($q) use ($booleanQuery, $expandedTerms) {
                $q->whereRaw("(MATCH(title, tags) AGAINST(? IN BOOLEAN MODE) OR MATCH(prompt) AGAINST(? IN BOOLEAN MODE))", [$booleanQuery, $booleanQuery]);

                foreach ($expandedTerms as $synonym) {
                    $synonymBool = SearchQueryEngine::buildFulltextBooleanQuery($synonym);
                    if (!empty($synonymBool)) {
                        $q->orWhereRaw("(MATCH(title, tags) AGAINST(? IN BOOLEAN MODE) OR MATCH(prompt) AGAINST(? IN BOOLEAN MODE))", [$synonymBool, $synonymBool]);
                    }
                }
            })
            ->groupBy('id')
            ->orderByRaw("((MATCH(title, tags) AGAINST(? IN BOOLEAN MODE) * 2.0) + MATCH(prompt) AGAINST(? IN BOOLEAN MODE)) DESC", [$booleanQuery, $booleanQuery]);
    }

    /**
     * Scope a query that matches a LIKE search of term across title, tags, prompt, and ai_model with AND matching and synonym expansion.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchLike($query, $term)
    {
        $tokens = SearchQueryEngine::tokenize($term);
        $expandedTerms = SearchQueryEngine::getExpandedTerms($term);

        return $query->whereStatus('active')
            ->where(function($q) use ($tokens, $expandedTerms) {
                // 1. Mandatory AND matching for primary clean tokens
                if (!empty($tokens)) {
                    $q->where(function($andQuery) use ($tokens) {
                        foreach ($tokens as $token) {
                            $andQuery->where(function($tokenMatch) use ($token) {
                                $tokenMatch->where('title', 'LIKE', '%' . $token . '%')
                                           ->orWhere('tags', 'LIKE', '%' . $token . '%')
                                           ->orWhere('prompt', 'LIKE', '%' . $token . '%')
                                           ->orWhere('ai_model', 'LIKE', '%' . $token . '%');
                            });
                        }
                    });
                }

                // 2. OR matching for expanded synonym phrases
                foreach ($expandedTerms as $synonym) {
                    $synonymTokens = SearchQueryEngine::tokenize($synonym);
                    if (!empty($synonymTokens)) {
                        $q->orWhere(function($synQuery) use ($synonymTokens) {
                            foreach ($synonymTokens as $stoken) {
                                $synQuery->where(function($stMatch) use ($stoken) {
                                    $stMatch->where('title', 'LIKE', '%' . $stoken . '%')
                                            ->orWhere('tags', 'LIKE', '%' . $stoken . '%')
                                            ->orWhere('prompt', 'LIKE', '%' . $stoken . '%')
                                            ->orWhere('ai_model', 'LIKE', '%' . $stoken . '%');
                                });
                            }
                        });
                    }
                }
            })
            ->groupBy('id');
    }
}
