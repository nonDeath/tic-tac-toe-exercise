<?php

namespace App\Http\Resources;

use function foo\func;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Matches extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
              'id' => $item->id,
              'name' => $item->name,
            ];
        })->all();
    }
}
