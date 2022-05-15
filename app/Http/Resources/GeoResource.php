<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GeoResource extends JsonResource
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
            'lat'=>$this->site_lat,
            'lon'=>$this->site_lon,
            'azimuth'=>$this->azimuth,
            'lac'=>$this->lac,
            'ci'=>$this->ci,
            'mnc'=>$this->mnc,
            'address'=>$this->adress,
            'sector_name'=>$this->sectorname,
        ];
    }
}