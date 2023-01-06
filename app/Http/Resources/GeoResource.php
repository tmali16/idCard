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
            'status'=>$this->resource ? 200 : 404,
            'lat'=>$this->resource?->site_lat,
            'lon'=>$this->resource?->site_lon,
            'diapason'=>$this->resource?->diapason,
            'Generation'=>$this->resource?->g,
            'azimuth'=>$this->resource?->azimuth,
            'lac'=>$this->resource?->lac,
            'ci'=>$this->resource?->ci,
            'mnc'=>$this->resource?->mnc,
            'address'=>$this->resource?->adress,
            'sector_name'=>$this->resource?->sectorname,
            'sectorname'=>$this->resource?->sectorname,
        ];
    }
}
