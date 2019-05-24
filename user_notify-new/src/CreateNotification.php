<?php

namespace Drupal\user_notify;

use Drupal\node\Entity\Node;
class CreateNotification {
	/**
	 * Constructs a new CustomService object.
	 */
	public function __construct() {

	}	
	public function send_notification($to_user,$title, $body) {
    //Create a notification
		$node = Node::create(['type' => 'notify']);
		$node->set('title', $title);
		$body_content = [
		'value' => $body,
		'format' => 'basic_html',
		];
		$node->set('body', $body_content);
		$node->set('uid', $to_user);
		$node->set('to_user', $to_user);
		$node->status = 1;
		$node->save();
  }
	public function notification_count($to_user) {
		$userData = \Drupal::service('user.data');
		$last_accessed = $userData->get('user_notify', $to_user, 'last_access_log');
		 $query = \Drupal::entityQuery('node')
			->condition('status', 1)
			->condition('created', $last_accessed, '>')
			->condition('type', 'notify');
    $result = $query->count()->execute();
    return $result;
	}
}
