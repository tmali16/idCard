<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'lac'=>$this->lac,
            'ci'=>$this->ci,
            'mnc'=>$this->mnc,
            'cell'=>new GeoResource($this->cell)
        ];
    }
}
