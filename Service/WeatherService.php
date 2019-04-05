<?php

namespace AppBundle\Service;
use Dwr\OpenWeatherBundle\Utility\Converter;
use Dwr\OpenWeatherBundle\Service\OpenWeather;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Weather;
use Symfony\Component\Serializer\SerializerInterface;

class WeatherService
{
  private $lat;
  private $lon;
  private $openWeatherService;
  private $em;
  private $serializer;

  public function __construct(OpenWeather $openWeatherService, EntityManagerInterface $entityManager, SerializerInterface $serializer) {
    $this->em = $entityManager;
    $this->openWeatherService = $openWeatherService;
    $this->serializer = $serializer;
  }

  /**
   * @return null|array
   */
  public function getWeather() {
    $weather = null;
    if ($this->openWeatherService) {
      $weather = $this->serializer->serialize(
        $this->saveWeather(
          $this->getWeatherDataByLatLon()
        ), 'json');
    }
    return $weather;
  }

  /**
   * @return float|string
   */
  public function setLat($lat) {
    $this->lat = (float) $lat;
    return $this;
  }

  /**
   * @return float|string
   */
  public function setLon($lon) {
    $this->lon = (float) $lon;
    return $this;
  }

  /**
   * @return array
   */
  private function getWeatherDataByLatLon() {
    return $this->openWeatherService->setType('Weather')->getByGeographicCoordinates($this->lon, $this->lat);
  }

  /**
   * @return array
   */
  public function getAllWeater() {
    $em = $this->em;
    return $this->serializer->serialize(
      $em->getRepository(Weather::class)->findAll(), 'json'
    );
  }

  /**
   * @return array
   */
  public function getWeatherStatistics() {

  }

  /**
   * @return Weather
   */
  private function saveWeather($data) {
    $em = $this->em;

    $weather = $em->getRepository(Weather::class)->findOneBy([
      'lat' => $data->coord()['lat'],
      'lon' => $data->coord()['lon'],
    ]);

    if (!$weather) {
      $weather = new Weather();
      $weather
        ->setCity($data->cityName())
        ->setName($data->weather()[0]['main'])
        ->setLat($data->coord()['lat'])
        ->setLon($data->coord()['lon'])
        ->setDescription($data->description())
        ->setTemp(Converter::kelvinToCelsius($data->temp()))
        ->setVisibility($data->visibility())
        ->setWindSpeed($data->windSpeed())
        ->setClouds($data->clouds()['all'])
        ->setDate(new \DateTime(Converter::intToDate($data->dt(), 'd-m-Y')))
      ;

      $em->persist($weather);
      $em->flush();
    }

    return $weather;
  }
}
