<?php

namespace App\Models;

class AiModel
{
    public $name;
    public $slug;
    public $total_prompts;
    public bool $is_ai_model = true;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
