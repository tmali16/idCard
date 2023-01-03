<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\GeoResource;
use App\Service\GeoService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        info($this['lac']);
        return array_merge_recursive(parent::toArray($request), [
            'mp'=>
                !is_null($this['lac']) &&
                !is_null($this['cellid']) ? new GeoResource(GeoService::get($this['mnc'], $this['lac'], $this['cellid'])) : null
        ]);
    }

    function ex(string $k, $ar){
        return array_key_exists($k, (array)$ar);
    }

//    public function with($request)
//    {
//        return [
//            'mp'=> $this->ex('lac', $this) && $this->ex('cellid', $this) ? new GeoResource(GeoService::get($this['mnc'], $this['lac'], $this['cellid'])) : null
//        ];
//    }
}
