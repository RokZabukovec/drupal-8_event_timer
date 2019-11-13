<?php

namespace Drupal\events_manager\Plugin\Block;

use DateTime;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'Drupalup Block' Block.
 *
 * @Block(
 *   id = "event_manager",
 *   admin_label = @Translation("Event manager"),
 *   category = @Translation("Block for displaying time until the event"),
 * )
 */
class EventManager extends BlockBase{
  /**
   * Builds and returns the renderable array for this block plugin.
   *
   * If a block should not be rendered because it has no content, then this
   * method must also ensure to return no content: it must then only return an
   * empty array, or an empty array with #cache set (with cacheability metadata
   * indicating the circumstances for it being empty).
   *
   * @return array|bool
   *   A renderable array representing the content of the block.
   *
   * @throws \Exception
   * @see \Drupal\block\BlockViewBuilder
   */
  public function build(){
    $route = $node = \Drupal::routeMatch();
    $node = $route->getParameter('node');
      if($node != null && $node->getType() == "event") {
        $dateRAW = $node->field_event_date->value;
        $date = new DateTime($dateRAW);
        $service = \Drupal::service('events_manager.event_manager');
        $output = $service->eventTimer($date);
        return [
          '#markup' => $output,
          '#title' => 'Event timer',
          '#cache' => [
            'max-age' => 0,
          ],
        ];
      }
    return [
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }
}
