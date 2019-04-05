<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request) {
      return $this->render('@App/default/index.html.twig', []);
    }

    /**
     * @return Dwr\OpenWeatherBundle\Service\OpenWeather
     */
    private function getWeatherService() {
      return $this->get('app.weather_service');
    }

    /**
     * @Route("/api/get-weather-by-lat-lon/{lon}/{lat}", name="get-weather-by-lat-lon", methods={"GET"})
     */
    public function getWeatherByLatLon(Request $request, $lon, $lat) {
      $weather = $this->getWeatherService()->setLat($lat)->setLon($lon)->getWeather();
      return new JsonResponse($weather, 200, [], true);
    }

    /**
     * @Route("/api/get-all-weather", name="get-all-weather", methods={"GET"})
     */
    public function getAllWeaterData(Request $request) {
      $weather = $this->getWeatherService();
      return new JsonResponse($weather->getAllWeater(), 200, [], true);
    }
}
