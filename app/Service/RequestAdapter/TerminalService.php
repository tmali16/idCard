<?php


namespace App\Service\RequestAdapter;


use GuzzleHttp\Client;

class TerminalService
{
    private $client;
    protected $url;

    /**
     * TerminalService constructor.
     * @param $url
     */
    public function __construct()
    {
        $this->url = env('TERMINAL_API_URL');
        $this->client = new Client([
            'base_uri' => $this->url,
            'timeout' => 60.0,
        ]);
    }

    public function getMp(string $phone):array{
        $uri = "/api/info?phone=996".$phone;
        $content = $this->client->get($uri);
        $str = $content->getBody()->getContents();
        return $this->convertData(json_decode($str, true));
    }

    private function convertData($original){
        $data = $original['data'];
        return [
            'phone'=> $this->ex('msisdn', $data) ? $data['msisdn']:'',
            'text'=>$this->ex('text', $data) ? $this->convertHex($data['text']):'',
            'abon_text'=>$this->ex('abonent_state', $data) ? $this->convertHex($data['abonent_state']):'',
            'operator'=>$this->ex('operator', $data) ? $this->convertHex($data['operator']):'',
            'address'=>$this->ex('address', $data) ? $this->convertHex($data['address']):'',
            'aol'=>$this->ex('aol', $data) ? ($data['aol']):null,
            'lac'=>$this->ex('lac', $data) ? ($data['lac']):null,
            'cellid'=>$this->ex('cellid', $data) ? ($data['cellid']):null,
            'priority'=>$this->ex('priority', $data) ? ($data['priority']):null,
            'stage'=>$this->ex('stage', $data) ? ($data['stage']):null,
            'mnc'=>$this->ex('mnc', $data) ? ($data['mnc']):null,
            'type'=>$this->ex('type', $data) ? ($data['type']):null,
            'accuracy'=>$this->ex('accuracy', $data) ? ($data['accuracy']):null,
        ];
    }

    private function hexToString($str){
        return chr(hexdec(substr($str, 2)));
    }

    private function convertHex($str){
        return preg_replace_callback("/(\\\\x..)/isU", fn($m)=>$this->hexToString($m[0] ), $str);
    }

    private function exist(array $data, array $filter){
        return array_filter($data, fn($k)=>in_array($k, $filter), ARRAY_FILTER_USE_KEY);
    }

    private function ex(string $k, array $data){
        return array_key_exists($k, $data);
    }
}
