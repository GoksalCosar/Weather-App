<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class WeatherRequestService
{
    //API Key almak için OpenWeatherAPI sitesine gidip kayıt olmanız gerekmektedir.
    //OpenWeatherAPI = https://openweathermap.org/
    private $apiKey = "YOUR_API_KEY";
    private $apiUrl = "https://api.openweathermap.org/data/2.5/weather";
    private $city;

    /**
     * @throws TransportExceptionInterface
     */
    public function getWeatherData($city): ResponseInterface
    {
        $this->city = $city;
        $httpClient = HttpClient::create();
        return $httpClient->request('GET', $this->apiUrl, [
            'query' => [
                'q' => $city,
                'appid' => $this->apiKey,
            ],
        ]);
    }

    public function weatherFormat($weatherData) {

        $weatherDescription = $weatherData["weather"][0]["description"];
        $skyTypes = ['clear sky', 'few clouds','overcast clouds', 'scattered clouds', 'broken clouds', 'shower rain', 'rain', 'thunderstorm','snow','mist'];
        $skyTypesTR = ['Güneşli', 'Az Bulutlu','Çok Bulutlu(Kapalı)', 'Alçak Bulutlu', 'Yer Yer Açık Bulutlu', 'Sağanak Yağmurlu', 'Yağmurlu', 'Gök Gürültülü Fırtına', 'Karlı', 'Puslu'];
        $skyImgUrl = [
            'Güneşli' => 'https://img.icons8.com/external-kosonicon-flat-kosonicon/64/external-clear-sky-weather-kosonicon-flat-kosonicon.png',
            'Az Bulutlu' => 'https://img.icons8.com/color/96/clouds.png',
            'Çok Bulutlu(Kapalı)' => 'https://img.icons8.com/stickers/100/clouds.png',
            'Alçak Bulutlu' => 'https://img.icons8.com/stickers/100/clouds.png',
            'Yer Yer Açık Bulutlu' => 'https://img.icons8.com/plasticine/100/sky.png',
            'Sağnak Yağmurlu' => 'https://img.icons8.com/officel/80/storm.png',
            'Yağmurlu' => 'https://img.icons8.com/color/96/rain--v1.png',
            'Gök Gürültülü Fırtına' => 'https://img.icons8.com/officel/80/cloud-lighting.png',
            'Karlı' => 'https://img.icons8.com/office/16/snow.png',
            'Puslu' => 'https://img.icons8.com/dusk/64/foggy-night-1.png'
        ];

        for ($i = 0; $i < $skyTypes; $i++){
            if ($weatherDescription == $skyTypes[$i]){
                $weatherDescription = $skyTypesTR[$i];
                break;
            }
        }

        //Api'dan gelen sıcaklık değerleri Kelvin olduğu için aşağıda Selsiyus dönüşümü yapılmıştır.
        $temp = round(($weatherData['main']['temp'] - 273.15), 2) ; //Genel sıcaklık
        $feels_temp = round(($weatherData['main']['feels_like'] - 273.15), 2); //Hissedilen Sıcaklık
        $temp_min = round(($weatherData['main']['temp_min'] - 273.15), 2); //En Düşük Sıcaklık
        $temp_max = round(($weatherData['main']['temp_max'] - 273.15), 2); //En Yüksek Sıcaklık

        return [
            "Şehir"         => $this->city,
            "Gökyüzü"       => $weatherDescription,
            "GökyüzüImg"    => $skyImgUrl[$weatherDescription],
            "Sıcaklık"      => $temp,
            "Hissedilen"    => $feels_temp,
            "Minimum"       => $temp_min,
            "Maksimum"      => $temp_max,
            "Nem"           => $weatherData['main']['humidity'],
            "Zaman"         => $weatherData['dt']
        ];
    }
}