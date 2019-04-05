<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\WeatherRepository")
 * @ORM\Table(name="weather")
 */
class Weather
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(name="name", type="string", length=50, nullable=true)
   */
  private $name;

  /**
   * @ORM\Column(name="city", type="string", length=50, nullable=true)
   */
  private $city;

  /**
   * @ORM\Column(name="description", type="string", length=255)
   */
  private $description;

  /**
   * @ORM\Column(name="visibility", type="integer", length=50, nullable=true)
   */
  private $visibility;

  /**
   * @ORM\Column(name="date", type="date")
   */
  private $date;

  /**
   * @ORM\Column(name="temp", type="decimal", precision=11, scale=2, length=50)
   */
  private $temp;
  
  /**
   * @ORM\Column(name="lat", type="decimal", precision=11, scale=2, length=50)
   */
  private $lat;

  /**
   * @ORM\Column(name="lon", type="float", length=50)
   */
  private $lon;

  /**
   * @ORM\Column(name="wind_speed", type="float", length=50, nullable=true)
   */
  private $windSpeed;

  /**
   * @ORM\Column(name="clouds", type="integer", length=50)
   */
  private $clouds;

  public function getId() {
    return $this->id;
  }

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
    return $this;
  }

  public function getCity() {
    return $this->city;
  }

  public function setCity($city) {
    $this->city = $city;
    return $this;
  }

  public function getDescription() {
    return $this->description;
  }

  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

  public function getVisibility() {
    return $this->visibility;
  }

  public function setVisibility($visibility) {
    $this->visibility = $visibility;
    return $this;
  }

  public function getDate() {
    return $this->date;
  }

  public function setDate($date) {
    $this->date = $date;
    return $this;
  }

  public function getTemp() {
    return $this->temp;
  }

  public function setTemp($temp) {
    $this->temp = $temp;
    return $this;
  }

  public function getLat() {
    return $this->lat;
  }

  public function setLat($lat) {
    $this->lat = $lat;
    return $this;
  }

  public function getLon() {
    return $this->lon;
  }

  public function setLon($lon) {
    $this->lon = $lon;
    return $this;
  }

  public function getWindSpeed() {
    return $this->windSpeed;
  }

  public function setWindSpeed($windSpeed) {
    $this->windSpeed = $windSpeed;
    return $this;
  }

  public function getClouds() {
    return $this->clouds;
  }

  public function setClouds($clouds) {
    $this->clouds = $clouds;
    return $this;
  }
}