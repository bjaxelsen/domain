<?php

/**
 * @file
 * Contains \Drupal\domain\Plugin\Block\DomainSwitcherBlock.
 */

namespace Drupal\domain\Plugin\Block;

use Drupal\domain\Plugin\Block\DomainBlockBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a block that links to all domains.
 *
 * @Block(
 *   id = "domain_switcher_block",
 *   admin_label = @Translation("Domain switcher")
 * )
 */
class DomainSwitcherBlock extends DomainBlockBase {

  /**
   * Build the output.
   *
   * @TODO: abstract or theme this function?
   */
  public function build() {
    $active_domain = domain_get_domain();
    $items = array();
    foreach (domain_load_multiple() as $domain) {
      $string = l($domain->name, $domain->getProperty('url'), array('external' => TRUE));
      if (!$domain->status) {
        $string .= '*';
      }
      if ($domain->id() == $active_domain->id()) {
        $string = '<em>' . $string . '</em>';
      }

      $items[] = $string;
    }
    return array(
      '#theme' => 'item_list',
      '#items' => $items,
    );
  }

}
