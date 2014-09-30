<?php

/**
 * @file
 * Definition of Drupal\domain\DomainValidatorInterface.
 */

namespace Drupal\domain;

use Drupal\domain\DomainInterface;

/**
 * Supplies validator methods for common domain requests.
 */
interface DomainValidatorInterface {

  /**
   * Validates the hostname for a domain.
   */
  public function validate(DomainInterface $domain);

  /**
   * Tests that a domain responds correctly.
   */
  public function checkResponse(DomainInterface $domain);

  public function getRequiredFields();
}
