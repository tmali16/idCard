<?php

namespace App\Http\Resources;

use App\Service\GeoService;
use Illuminate\Http\Resources\Json\JsonResource;

class TerminalLocationResource extends JsonResource
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
            'phone' => $this->ex('msisdn', $this) ? '' : $this['msisdn'],
            'text' => $this->ex('text', $this) ? '' : preg_replace_callback("/(\\\\x..)/isU", function($m) { return $this->hexToString($m[0] ); }, $this['text']),
            'lac' => $this->ex('lac', $this) ? '' : $this['lac'],
            'cid' => $this->ex('cellid', $this) ? '' : $this['cellid'],
            'abonent_state'=>$this->ex('abonent_state', $this) ? '' : preg_replace_callback("/(\\\\x..)/isU", function($m) { return $this->hexToString($m[0] ); }, $this['abonent_state']),
            'aol' => $this->ex('aol', $this) ? '' : $this['aol'],
            '_address' => $this->ex('address', $this) ? '' : preg_replace_callback("/(\\\\x..)/isU", function($m) { return $this->hexToString($m[0] ); }, $this['address']),
            'mnc' => $this->ex('mnc', $this) ? '' : $this['mnc'],
            'mp' => $this->ex('lac', $this) && $this->ex('cellid', $this) ? '' : new GeoResource(GeoService::get($this['mnc'], $this['lac'], $this['cellid'])),
            'loc'=>[
                'lat'=>$this->ex('lat', $this) ? null:$this['lat'],
                'lng'=>$this->ex('lat', $this) ? null:$this['lng'],
            ],
        ];
    }
    function getBounds(array $bounds){
        $ret = [];
        foreach ($bounds as $k => $v){
//            $ret[$k == 'northeast' ? '_northEast' : '_southWest'] = $v;
            $ret[] = array_values($v);
        }
        return $ret;
    }
    function ex(string $k, $ar){

        return array_key_exists($k, (array)$ar);
    }
    function hexToString($str){return chr(hexdec(substr($str, 2)));}
}
