<?php

namespace App;

trait Taggable
{
    public function tagsString($glue = ', ')
    {
        $tags = array_map('ucfirst', $this->tags->pluck('name')->toArray());

        return implode($glue, $tags);
    }

    public function attachTags($tagNames)
    {
        $this->tags()->attach($this->findTagsIds($tagNames));

        return $this;
    }

    public function detachTag($tagName)
    {
        $this->tags()->detach(Tag::whereName(strtolower($tagName))->get());
    }

    private function findTagsIds($tagNames)
    {
        return collect(is_array($tagNames) ? $tagNames : explode(',', $tagNames))->map(function ($tagName) {
            return Tag::firstOrCreate(['name' => strtolower($tagName)]);
        })->unique('id')->pluck('id');
    }

    public function syncTags($tags)
    {
        $tags = $this->findCategoriesIds($tags);

        $this->tags()->sync($tags);

        return $this;
    }
}
