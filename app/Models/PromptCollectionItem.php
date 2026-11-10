<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromptCollectionItem extends Model
{
    protected $table = 'prompt_collection_items';

    protected $guarded = ['id'];

    public function collection()
    {
        return $this->belongsTo(PromptCollection::class, 'prompt_collection_id');
    }

    public function image()
    {
        return $this->belongsTo(Images::class, 'image_id');
    }
}
