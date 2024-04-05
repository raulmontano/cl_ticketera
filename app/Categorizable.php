<?php

namespace App;

trait Categorizable
{
    public function categoriesString($glue = ', ')
    {
        $categories = array_map('ucfirst', $this->categories->pluck('name')->toArray());

        return implode($glue, $categories);
    }

    public function attachCategories($tagNames)
    {
        $this->categories()->attach($this->findCategoriesIds($tagNames));

        return $this;
    }

    public function detachCategory($tagName)
    {
        $this->categories()->detach(Tag::whereName(strtolower($tagName))->get());
    }

    private function findCategoriesIds($tagNames)
    {
        return collect(is_array($tagNames) ? $tagNames : explode(',', $tagNames))->map(function ($tagName) {
            return Tag::firstOrCreate(['name' => strtolower($tagName)]);
        })->unique('id')->pluck('id');
    }

    public function syncCategories($tags)
    {
        $tags = $this->findCategoriesIds($tags);

        $this->categories()->sync($tags);

        return $this;
    }
}
