<?php

namespace Drupal\events_manager;

use \DateTime;
use DateTimeZone;
use Exception;

class EventManagerServices{
  /**
   * Process DateTime object and returns the response string which is displayed in the view.
   * This method is called when using the service.
   * @example 52 day until event
   * @param DateTime $dateTime
   * @return string|null
   */
  public function eventTimer(DateTime $dateTime){
    try {
      $eventTimestamp = $this->getTimezoneOffsetTimestamp($dateTime);
      $currentTimestamp = $this->getCurrentTimestamp();
      return $this->getTimeRemaining($eventTimestamp, $currentTimestamp);
    } catch (Exception $e) {
      \Drupal::logger('events_manager')->debug($e->getMessage());
    }
    return Null;
  }

  /**
   * Adds time offset to time value from database because time in database
   * is stored as UTC time.
   *
   * @param DateTime $dateTime
   * @return string|null; UNIX timestamp with offset.
   */
  private function getTimezoneOffsetTimestamp(DateTime $dateTime){
    $defaultTimezone = date_default_timezone_get();
    $currentTimezone = new DateTimeZone($defaultTimezone);
    try {
      $now = new DateTime("now", $currentTimezone);
      $offset = $currentTimezone->getOffset($now);
      return $dateTime->getTimestamp() + $offset;
    } catch (Exception $e) {
      \Drupal::logger('events_manager')->debug($e->getMessage());
    }
    return Null;
  }

  /**
   * Gets current timestamp.
   *
   * @return int
   * @throws Exception
   */
  private function getCurrentTimestamp(){
    $currentTime = new DateTime();
    $currentTimestamp = $currentTime->getTimestamp();
    return $currentTimestamp;
  }

  /**
   * Based on the difference between current and event time method returns a string response message.
   *
   * @param $currentTimestamp
   * @param $eventTimestamp
   * @return string
   * @throws Exception
   */
  private function getTimeRemaining($eventTimestamp, $currentTimestamp){
    $output = "";
    $daysFrom = $this->getDaysFrom($eventTimestamp);
    switch($daysFrom){
      case $daysFrom == 0:
        $output = $currentTimestamp < $eventTimestamp ? "Event is happening today.": "Event already happend.";
        break;
      case $daysFrom == 1:
        $output = "Event is happening tommorow.";
        break;
      case $daysFrom < 0:
        $output = "Event already happend.";
        break;
      case $daysFrom > 1:
        $daysFrom = (int)$daysFrom;
        $output = "{$daysFrom} days from event.";
        break;
    }
    return $output;
  }

  /**
   * Determines the difference in days from timestamps.
   *
   * @param $eventTimestamp
   * @return string;
   * @throws Exception
   */
  private function getDaysFrom($eventTimestamp){
    if(is_numeric($eventTimestamp)){
      $now = new DateTime();
      $now->setTime(0, 0, 0); // reset time to so I can consistenly compare two dates.
      $event = new DateTime();
      $event->setTimestamp($eventTimestamp);
      $event->setTime( 0, 0, 0 ); // reset time to so I can consistenly compare two dates.
      $difference = $now->diff($event);
      return $difference->format( "%R%a" );
    }
    return null;
  }

}
