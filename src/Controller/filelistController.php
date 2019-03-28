<?php

namespace Drupal\filemanagement\Controller;
use Drupal\Core\Controller\ControllerBase;
Use \Drupal\Core\Routing;
/**
 * Class OffersController.
 ** @package Drupal\filemanagement\Controller
 */ 
 class filelistController extends ControllerBase
 {
    /**
      * Hello.
      * * @return string
      * Return Hello string.
      */
    public function file_list($term_id)
    {
			global $base_url;
			$curr_uid = \Drupal::currentUser()->id();
			$uid = \Drupal::request()->query->get('auth');
			$folder_name = \Drupal::request()->query->get('fname');
			if($curr_uid != $uid) {
				#check access permission
			}
			
			$nodes = \Drupal::entityTypeManager()->getStorage('node')->getQuery()
			->latestRevision()
			->condition('field_folder_name', $term_id, '=')
			->condition('uid', $uid, '=')
			->execute();
			if(!empty($nodes)) {
				$counter = 1; 
				foreach ($nodes as $node_id) {
					$node = \Drupal\node\Entity\Node::load($node_id);
					$fid = $node->field_file->target_id;
					$file = \Drupal\file\Entity\File::load($fid);
					$file_url = $file->url();
					$file_name = $file->getFilename();
					$files[] = array('filename'=> $file_name, 'url' => $file_url );
					$rows[] = array(
							'title' => array(
							'data' => $counter++,
						),
						'file' => array(
							'data' => array(
								'#markup' => '<a href="'.$file_url.'">'.$file_name.'</a>',
							),
						),
					);
					
				}
			}
			
			$prefix = '<h1>'.$folder_name.'/</h1>';
			$add_link = $base_url.'/node/add/user_files';
			$back_link = $base_url.'/folders/'.$uid;
			$prefix .= '<div><a href="'.$add_link.'">Add files</a> </div>';
			$prefix .= '<div><a href="'.$back_link.'">Back </a> </div>';
			$header = array(
      // We make it sortable by name.
				array('data' => $this->t('Slno')),
				array('data' => $this->t('File list')),
			);
			 // Generate the table.
			$build['config_table'] = array(
				'#prefix' => $prefix,
				'#theme' => 'table',
				'#header' => $header,
				'#rows' => $rows,
			);
			$build['pager'] = array(
					'#type' => 'pager'
				);
			
			return $build;
    }
 }