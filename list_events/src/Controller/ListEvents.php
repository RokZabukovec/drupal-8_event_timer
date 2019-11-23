<?php

namespace Drupal\list_events\Controller;

use Drupal\Core\Controller\ControllerBase;


class ListEvents extends ControllerBase{

  public function page(){
    return array(
      '#type' => 'markup',
      '#markup' => $this->t('Hello, World!'),
    );
  }

}
