<?php

namespace Drupal\cust_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "custom_user_dash_menu_block",
 *   admin_label = @Translation("User dashboard menu"),
 * )
 */
class CustUserDashMenu extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
		global $base_url;
		$curr_uid = \Drupal::currentUser()->id();
		$url_uid = \Drupal::request()->query->get('auth');
	
		$uid = (($url_uid != $curr_uid)&&(!empty($url_uid)))? $url_uid : $curr_uid;
		
		$data[] = array('link' => $base_url.'/user/'.$uid,
									'link_title' => 'My Profile',	
									'icon_class' =>'',
									);
		$data[] = array('link' => $base_url.'/my-favorite',
									'link_title' => 'Favorates',		
									'icon_class' =>'',
									);
		$data[] = array('link' => $base_url.'/#',
									'link_title' => 'Shortlisted',	
									'icon_class' =>'',	
									);
		$data[] = array('link' => $base_url.'/#',
									'link_title' => 'My networks',		
									'icon_class' =>'',
									);
		
		//echo '<pre>'; print_r($url_uid); echo '</pre>';
		
		return [
				'#theme' => 'userdashmenublock',
				'#dashmenublock' => $data,
			];
  }
	//Disabling caching
   public function getCacheMaxAge() {
        return 0;
    }
 
}