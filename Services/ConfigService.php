<?php

namespace BlaubandOneClickSystem\Services;

class ConfigService
{
    private $data;

    public function __construct($path)
    {
        $configData = file_get_contents($path);
        $this->data = json_decode(json_encode((array)simplexml_load_string($configData)),1);
    }

    public function get($string, $asArray = false){
        try{
            $result = $this->data;
            $steps = explode('.', $string);
            foreach ($steps as $step){
                $result = $result[$step];
            }

            if($asArray && !empty($result) && !isset($result[0])){
                $result = [$result];
            }

            return $result;
        }catch (\Exception $e){
            return null;
        }
    }
}