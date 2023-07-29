<?php

namespace App\Controller;

use App\Service\WeatherRequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class WeatherController extends AbstractController
{
    #[Route("/index/{cityName}")]
    public function index($cityName){
        $weatherRequestService = new WeatherRequestService();
        $response = $weatherRequestService->getWeatherData($cityName);
        $responseData = $response->getContent();
        $data = json_decode($responseData, true);
        $formatedWeatherData = $weatherRequestService->weatherFormat($data);

        $unixTime = $formatedWeatherData["Zaman"];
        date_default_timezone_set('Europe/Istanbul');
        $dateTime = date('H:i', $unixTime);

        $formatedWeatherData["Zaman"] = $dateTime;

        return $this->render('weather-app/index.html.twig', [
            'formatedWeatherData' => $formatedWeatherData
        ]);
    }
}