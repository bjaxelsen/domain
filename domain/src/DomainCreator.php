<?php

/**
 * @file
 * Definition of Drupal\domain\DomainCreator.
 */

namespace Drupal\domain;

use Drupal\domain\DomainCreatorInterface;
use Drupal\domain\DomainInterface;
use Drupal\domain\DomainNegotiatorInterface;

/**
 * Creates new domain records.
 *
 * This class is a helper that replaces legacy procedural code.
 */
class DomainCreator implements DomainCreatorInterface {

  /**
   * @var \Drupal\domain\DomainLoaderInterface
   */
  protected $loader;

  /**
   * @var \Drupal\domain\DomainNegotiatorInterface
   */
  protected $negotiator;

  /**
   * Constructs a DomainCreator object.
   *
   * @param \Drupal\domain\DomainLoaderInterface $loader
   *   The domain loader.
   * @param \Drupal\domain\DomainNegotiatorInterface $negotiator
   *   The domain negotiator.
   */
  public function __construct(DomainLoaderInterface $loader, DomainNegotiatorInterface $negotiator) {
    $this->loader = $loader;
    $this->negotiator = $negotiator;
  }

  /**
   * {@inheritdoc}
   */
  public function createDomain(array $values = array()) {
    $default = $this->loader->loadDefaultId();
    $domains = $this->loader->loadMultiple();
    if (empty($values)) {
      $values['hostname'] = $this->createHostname();
      $values['name'] = \Drupal::config('system.site')->get('name');
      $values['id'] = $this->createMachineName($values['hostname']);
    }
    $values += array(
      'scheme' => empty($GLOBALS['is_https']) ? 'http' : 'https',
      'status' => 1,
      'weight' => count($domains) + 1,
      'is_default' => (int) empty($default),
    );
    $domain = \Drupal::entityManager()->getStorage('domain')->create($values);
    return $domain;
  }

  /**
   * {@inheritdoc}
   */
  public function createNextId() {
    $domains = $this->loader->loadMultiple();
    $max = 0;
    foreach ($domains as $domain) {
      $domain_id = $domain->getDomainId();
      if ($domain_id > $max) {
        $max = $domain_id;
      }
    }
    return $max + 1;
  }

  /**
   * {@inheritdoc}
   */
  public function createHostname() {
    return $this->negotiator->negotiateActiveHostname();
  }

  /**
   * {@inheritdoc}
   */
  public function createMachineName($hostname = NULL) {
    if (empty($hostname)) {
      $hostname = $this->createHostname();
    }
    return preg_replace('/[^a-z0-9_]+/', '_', $hostname);
  }

}
