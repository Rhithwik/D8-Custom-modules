<?php

namespace Drupal\user_notify\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\Request;
/**
 * Class OffersController.
 ** @package Drupal\user_notify\Controller
 */ 
 class delNotificationsController extends ControllerBase
 {
    /**
      * Hello.
      * * @return string
      * Return Hello string.
      */
    public function delNotificationsPage(NodeInterface $nid)
    {
		  global $base_url;
      $curr_uid = \Drupal::currentUser()->id();
      $nodes = \Drupal::entityTypeManager()->getStorage('node')->getQuery()
				->latestRevision()
				->condition('type', 'notify')
				->condition('to_user', $curr_uid)
				->condition('status', 1) 
				->sort('created' , 'DESC')
				->execute();
	
      $userData = \Drupal::service('user.data');
			$last_accessed = $userData->get('user_notify', $curr_uid, 'last_access_log');
			
      if(!empty($nodes)) {
        $counter = 1; 
        foreach ($nodes as $node_id) {
          $node = \Drupal\node\Entity\Node::load($node_id);
          $body =  $node->get('body')->value;
          $viewed =  $node->get('status_viewed')->value;
          $created_on =  $node->get('created')->value;
          $created =  date('m/d/Y g:i:s A', $created_on);
      //echo '<pre>'; print_r($viewed); echo'</pre>'; exit;
					$status_color = ($created_on > $last_accessed)? 'new' : '';
          $rows[] = array(
              'file' => array(
                'data' => array(
                  '#markup' => '<div class="viewed-'.$viewed.' '.$status_color.'">'.$body.'<span class="created_on"> - '.$created.'</span></div>',
                ),
              ),
            );
          
        }
      }
      
      //$add_link = $base_url.'/node/add/user_files';
      $prefix = '<section class="notifications">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="notification-head">
                    <h3>Notifications</h3>
                </div>
                <div class="notification-list">';
            $suffix = '</div>                   
            </div>
        </div>
    </div>
</section>'; 

      $header = array(
      // We make it sortable by name.
        array('data' => $this->t('Info')),
      );
       // Generate the table.
      $build['config_table'] = array(
        '#prefix' => $prefix,
        '#suffix' => $suffix,
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#attached' => [
        'library' => [
          'user_notify/user_notify_lib',
        ],]
      );
      $build['pager'] = array(
          '#type' => 'pager'
        );
			$time = time();	
      $userData = \Drupal::service('user.data');
			$userData->set('user_notify', $curr_uid, 'last_access_log', $time);
      return $build;
    }
 }