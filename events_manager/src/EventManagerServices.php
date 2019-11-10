<?php

namespace Drupal\events_manager;


use DateTime;
use DateTimeZone;

class EventManagerServices{
  const SECONDS_IN_DAY = 86400;
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
      return $this->getTimeRemaining($currentTimestamp, $eventTimestamp);
    } catch (\Exception $e) {
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
    } catch (\Exception $e) {
      \Drupal::logger('events_manager')->debug($e->getMessage());
    }
    return Null;
  }

  /**
   * Gets current timestamp of the user.
   *
   * @return int
   * @throws \Exception
   */
  private function getCurrentTimestamp(){
    $currentTime = new DateTime();
    $currentTimestamp = $currentTime->getTimestamp();
    return $currentTimestamp;
  }

  /**
   * Calculates the time remaining between two timstamsps.
   * Based on the amount of time remaining it returns the string response.
   *
   * @param $currentTimestamp
   * @param $eventTimestamp
   * @return string
   * @throws \Exception
   */
  private function getTimeRemaining($currentTimestamp, $eventTimestamp){
    $output = '';
    if($currentTimestamp < $eventTimestamp){
      $secondsRemaining = $eventTimestamp - $currentTimestamp;
      if($secondsRemaining > self::SECONDS_IN_DAY){
        // More than one day remaining.
        $daysRemaining = floor($secondsRemaining / self::SECONDS_IN_DAY);
        $output = $daysRemaining . " days until event.";
      }else if($secondsRemaining < self::SECONDS_IN_DAY){
        // Event is happening today.
        $output = $this->isToday($eventTimestamp, $currentTimestamp);
      }
    }else{
      $output = "Event already happend.";
    }
    return $output;
  }

  /**
   * Determines if the difference in timestamps is less than 24ur and if the current time is this day.
   *
   * @param $eventTimestamp
   * @param $currentTimestamp
   * @return string; Resonse for displaying info for the user.
   */
  private function isToday($eventTimestamp, $currentTimestamp){
    if($eventTimestamp > $currentTimestamp && ($eventTimestamp - $currentTimestamp) < self::SECONDS_IN_DAY){
      $eventHour =  date('H', $eventTimestamp);
      $nowHour =  date('H', $currentTimestamp);
      return $nowHour < $eventHour ? "Event is happening today": "Events starts tomorrow";
    }
    return false;
  }
}
