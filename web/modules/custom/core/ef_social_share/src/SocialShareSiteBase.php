<?php

namespace Drupal\ef_social_share;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Utility\Token;
use Drupal\ef_icon_library\IconLibraryInterface;

/**
 * Provides a base implementation for a configurable social share site plugin.
 */
abstract class SocialShareSiteBase extends PluginBase implements SocialShareSiteInterface, ConfigurablePluginInterface, PluginFormInterface {

  /**
   * @var IconLibraryInterface
   */
  protected $iconLibrary;

  /**
   * @var \Drupal\Core\Utility\Token
   */
  protected $tokenService;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, IconLibraryInterface $icon_library, Token $tokenService) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->iconLibrary = $icon_library;
    $this->tokenService = $tokenService;

    $this->setConfiguration($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'icon' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * @inheritdoc
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $icon_list = $this->iconLibrary->getIconList();

    $form['icon'] = [
      '#type' => 'select',
      '#title' => $this->t('Icon'),
      '#required' => TRUE,
      '#options' => $icon_list,
      '#default_value' => $this->configuration['icon'],
      '#description' => $this->t('This icon for this social site, drawn from our icon library.'),
    ];

    $form['token_tree'] = array(
      '#theme' => 'token_tree_link',
      '#token_types' => array('node'),
      '#show_restricted' => TRUE,
      '#weight' => 90,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getIcon() {
    return $this->configuration['icon'];
  }

  /**
   * @inheritdoc
   */
  public function getLibraries(array $context = []) {
    return [];
  }

  public function shouldOpenInPopup () {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration + $this->defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['icon'] = $form_state->getValue('icon');
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return [];
  }

  protected function getPageUrl ($context = []) {
    return $this->tokenService->replace('[current-page:url]', $context);
  }

}
