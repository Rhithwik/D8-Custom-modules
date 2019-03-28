<?php

namespace Drupal\filemanagement\Controller;
use Drupal\Core\Controller\ControllerBase;
/**
 * Class OffersController.
 ** @package Drupal\filemanagement\Controller
 */ 
 class folderlistController extends ControllerBase
 {
    /**
      * Hello.
      * * @return string
      * Return Hello string.
      */
    public function folders_list($userid)
    {
			$vid = 'folder_name';
			$terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
			global $base_url;
			$term_data = NULL;
			$base_path = $base_url;
			foreach ($terms as $term) {
				$authore_ar = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($term->tid)->get('field_folder_author')->getValue();
				$authors = array_column($authore_ar, 'target_id');
				if(in_array($userid, $authors)) {
					$term_data[] = array(
						'name' => $term->name,
						'link' => $base_path.'/folder/files/'.$term->tid.'?auth='.$userid.'&fname='.$term->name,
					);
				}
			}
			//echo '<pre>'; print_r($base_path); echo '</pre>';
			return [
				'#theme' => 'filefolderlist',
				'#folders' => $term_data,
			];
    }
 }