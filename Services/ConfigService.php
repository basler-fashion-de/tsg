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

    public function get($string){
        try{
            $result = $this->data;
            $steps = explode('.', $string);
            foreach ($steps as $step){
                $result = $result[$step];
            }

            return $result;
        }catch (\Exception $e){
            return null;
        }

    }
}