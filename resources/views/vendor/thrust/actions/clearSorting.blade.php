<?php

  use Illuminate\Support\Arr;

  $query = request()->query();

  if (isset($query['sort'])) {
      unset($query['sort']);
  }

  if (isset($query['sort_order'])) {
      unset($query['sort_order']);
  }

  $queryString = Arr::query($query);

?>

<a href='{{ request()->url() }}?{{$queryString}}' class="button secondary hide-mobile" >
    {{ $title }}
</a>
