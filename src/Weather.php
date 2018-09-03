<?php
namespace Alanliao\Weather;

use Alanliao\Weather\Exceptions\HttpException;
use Alanliao\Weather\Exceptions\InvalidArgumentException;
use GuzzleHttp\Client;

class Weather
{
    protected $key;
    protected $guzzleOptions = [];
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    public function getWeather($city, string $type = 'base', string $format = 'json')
    {
        $url = 'https://restapi.amap.com/v3/weather/weatherInfo';
        if (!\in_array(\strtolower($format), ['json', 'xml'])) {
            throw new InvalidArgumentException('Invalid response format: ' . $format);
        }
        if (!\in_array(\strtolower($type), ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid type value(base/all): ' . $type);
        }
        $query = array_filter([
            'key'        => $this->key,
            'city'       => $city,
            'output'     => strtolower($format),
            'extensions' => strtolower($type),
        ]);
        try {

            $response = $this->getHttpClient()->get($url, [
                'query' => $query,
            ])->getBody()->getContents();
            return $format === 'json' ? \json_decode($response, true) : $response;
        } catch (\Exception $e) {
            echo ">>>";
            var_dump($e->getMessage());
            echo ">>>";
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }

    }

}
