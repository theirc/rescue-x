<?php

namespace Drupal\ef_secondary_menu;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\ef_icon_library\IconLibraryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SecondaryMenuThemeHelper implements ContainerInjectionInterface {
  /**
   * The entity storage for menu_link_content
   *
   * @var EntityStorageInterface
   */
  protected $menuStorage;

  /**
   * The icon library
   *
   * @var IconLibraryInterface
   */
  protected $iconLibrary;

  public function __construct(EntityStorageInterface $menuStorage, IconLibraryInterface $iconLibrary) {
    $this->menuStorage = $menuStorage;
    $this->iconLibrary = $iconLibrary;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')->getStorage('menu_link_content'),
      $container->get('ef.icon_library')
    );
  }

  /**
   * Ready the secondary menu to be rendered as a pattern
   *
   * @param $variables
   */
  public function preprocessSecondaryMenu (&$variables) {
    $menu_links = $this->menuStorage->getQuery()
      ->condition('menu_name', 'secondary-navigation',  '=')
      ->sort('weight')->sort('id')
      ->execute();

    $menu_links = $this->menuStorage->loadMultiple($menu_links);

    $menu_items= [];

    /** @var \Drupal\menu_link_content\MenuLinkContentInterface $menu_link */
    foreach ($menu_links as $menu_link) {
     $title = $menu_link->getTitle();
     $url = $menu_link->getUrlObject()->toString();
     $icon_field = $menu_link->field_icon->value;
     $icon = $this->iconLibrary->getIconInformation($icon_field)->id;

      $menu_items[] = [
        'title' => $title,
        'icon' => $icon,
        'url' => $url,
      ];
    }

    $variables['secondary_menu'] = [
      '#type' => "pattern",
      '#id' => 'secondary_menu',
      '#fields' => [
        'secondary_menu_menu_items' => $menu_items,
        'secondary_menu_social_share_sites' => [],
      ],
    ];
  }
}