<?php

namespace Drupal\ef_social_share;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Drupal\ef_icon_library\IconLibraryInterface;
use Drupal\ef_sitewide_settings\SitewideSettingsManagerInterface;

class SocialService implements SocialServiceInterface {
  /** @var SitewideSettingsManagerInterface */
  protected $sitewideSettingsManager;

  /**
   * @var \Drupal\ef_icon_library\IconLibraryInterface
   */
  protected $iconLibrary;

  /**
   * @var LanguageManagerInterface
   */
  protected $languageManager;

  public function __construct(SitewideSettingsManagerInterface $sitewideSettingsManager, IconLibraryInterface $iconLibrary, LanguageManagerInterface $languageManager) {
    $this->sitewideSettingsManager = $sitewideSettingsManager;
    $this->iconLibrary = $iconLibrary;
    $this->languageManager = $languageManager;
  }

  /**
   * @inheritdoc
   */
  public function getFollowText () {
    $social_sites_settings = $this->sitewideSettingsManager->getSitewideSettingsForType('social_sites');

    if ($social_sites_settings) {
      $active_language = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();

      if ($social_sites_settings->hasTranslation($active_language)) {
        $social_sites_settings = $social_sites_settings->getTranslation($active_language);

        $field_social_sites_follow_text = $social_sites_settings->field_social_sites_follow_text;

        if ($field_social_sites_follow_text) {
          return $field_social_sites_follow_text->value;
        }
      }
    }

    return "";
  }

  /**
   * @inheritdoc
   */
  public function getSocialMenu () {
    $result = [];

    /** @var \Drupal\ef_sitewide_settings\SitewideSettingsInterface $social_sites_settings */
    $social_sites_settings = $this->sitewideSettingsManager->getSitewideSettingsForType('social_sites');

    if ($social_sites_settings) {
      $active_language = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();

      if ($social_sites_settings->hasTranslation($active_language)) {
        $social_sites_settings = $social_sites_settings->getTranslation($active_language);
        $social_sites_field = $social_sites_settings->field_social_sites;

        if ($social_sites_field) {
          foreach ($social_sites_field as $social_site) {
            $uri = $social_site->uri;
            $icon = $social_site->options['link_icon'];

            $url = Url::fromUri($uri);

            $result[] = [
              'url' => $url->toString(),
              'icon_name' => $icon,
            ];
          }
        }
      }
    }

    return $result;
  }

  /**
   * @inheritdoc
   */
  public function getSocialShareSites () {
    $result = [];

    /** @var \Drupal\ef_sitewide_settings\SitewideSettingsInterface $social_sites_settings */
    $social_sites_settings = $this->sitewideSettingsManager->getSitewideSettingsForType('social_sites');

    if ($social_sites_settings) {
      $active_language = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();

      if ($social_sites_settings->hasTranslation($active_language)) {
        $social_sites_settings = $social_sites_settings->getTranslation($active_language);
        $field_social_sharing_field = $social_sites_settings->field_social_sharing;

        if ($field_social_sharing_field) {
          foreach ($field_social_sharing_field as $social_sharing_site) {
            /** @var \Drupal\ef_social_share\Entity\SocialShareSite $site_entity */
            $site_entity = $social_sharing_site->entity;

            /** @var \Drupal\ef_social_share\SocialShareSiteInterface $site_plugin */
            $site_plugin = $site_entity->getPlugin();

            $result[] = $site_plugin;
          }
        }
      }
    }

    return $result;
  }
}