<?php

namespace Drupal\cust_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "custom_user_block",
 *   admin_label = @Translation("User info"),
 * )
 */
class CustUserBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
		global $base_url;
		$curr_uid = \Drupal::currentUser()->id();
		$url_uid = \Drupal::request()->query->get('auth');
	 	if(($url_uid != $curr_uid)&&(!empty($url_uid))) {
			$uid = $url_uid;
			$edit_link = '';
		} else {
			$uid = $curr_uid;
			$edit_link = $base_url.'/user/'.$uid.'/edit';
		}
	
		$user = \Drupal\user\Entity\User::load($uid);
		$style = \Drupal::entityTypeManager()->getStorage('image_style')->load('thumbnail');
		$picture = (($user->get('user_picture')->entity) != null)?  $style->buildUrl($user->get('user_picture')->entity->getFileUri()) : '';
		
		
		$first_name = $user->get('field_first_name')->getString();
		$last_name = $user->get('field_last_name')->getString();
		
		$data = array('name' => $first_name.' '.$last_name,
									'picture' => $picture,	
									'edit_link' => $edit_link,
									);
		
		//echo '<pre>'; print_r($url_uid); echo '</pre>';
		
		return [
				'#theme' => 'userblock',
				'#userdata' => $data,
			];
  }
	//Disabling caching
   public function getCacheMaxAge() {
        return 0;
    }
 
}