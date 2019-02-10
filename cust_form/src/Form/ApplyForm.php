<?php
namespace Drupal\cust_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\user\Entity\User;

/**
* Implements a simple form.
*/
class ApplyForm extends FormBase {

  /**
  * Build the simple form.
  *
  * @param array $form
  *   Default form array structure.
  * @param FormStateInterface $form_state
  *   Object containing current form state.
  */
  public function buildForm(array $form, FormStateInterface $form_state) {
		$application_id = \Drupal::request()->query->get('application_id');
		$title = $body = '';
		if(!empty($application_id)){
			$form['application_id'] = [
				'#type' => 'textfield',
				'#title' => $this->t('Application ID'),
				'#description' => $this->t('Title must be at least 15 characters in length.'),
				'#required' => TRUE,
				'#value' => $application_id,
			];
			
		// Load a single node.
			$node = Node::load($application_id);
			if(!empty($node)){
				$title = $node->get('title')->value;
				$body = $node->get('body')->value;
				$employment_history = $node->employment_history->getValue();
				// $job_count_field  = sizeof($employment_history);		
				//$form_state->set('job_count', $job_count_field);
			}
		}
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#description' => $this->t('Title must be at least 15 characters in length.'),
      '#required' => TRUE,
			'#value' => $title,
    ];
    $form['body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Application data'),
      '#description' => $this->t('Application details.'),
      '#required' => TRUE,
			'#value' => $body,
    ];
		
    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
      '#type' => 'actions',
    ];
		$form['#tree'] = TRUE;
			
    $form['job'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Job info'),
      '#prefix' => '<div id="job-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];
		$job_count_field = $form_state->get('job_count');
		if (empty($job_count_field)) {
      $job_count_field = 1;
			$form_state->set('job_count', 1);
    }
		for ($i = 0; $i < $job_count_field; $i++) {
			$employer_name = $employment_years = NULL;
			if(!empty($employment_history)) {
				$para_target = $employment_history[$i]['target_id'];
				$job_para = \Drupal\paragraphs\Entity\Paragraph::load($para_target);
				$employer_name = $job_para->get('employer_name')->getString(); //get string to get single value
				$employment_years = $job_para->get('employment_years')->getString();
				//echo '<pre>'; print_r($employer_name); echo '</pre>'; exit;
			}
				
			$form['job'][$i]['employer_name'] = [
				'#type' => 'textfield',
				'#title' => $this->t('Employer name'),
        // '#value' => $employer_name,
			];
			$form['job'][$i]['employment_years'] = [
				'#type' => 'textfield',
				'#title' => $this->t('Employment years'),
				// '#value' => $employment_years,
			];
		}
		
    $form['job']['actions']['add_job'] = [
      '#type' => 'submit',
      '#value' => t('Add one more'),
      '#submit' => array('::addOne'),
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => 'job-fieldset-wrapper',
      ],
    ];
    if ($job_count_field > 1) {
      $form['job']['actions']['remove_job'] = [
        '#type' => 'submit',
        '#value' => t('Remove one'),
        '#submit' => array('::removeCallback'),
       '#ajax' => [
          'callback' => '::addmoreCallback',
          'wrapper' => 'job-fieldset-wrapper',
        ]
      ];
    }

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }
	
  public function addmoreCallback(array &$form, FormStateInterface $form_state) {
    $job_count_field = $form_state->get('job_count');
    return $form['job'];
  }
	
	public function addOne(array &$form, FormStateInterface $form_state) {
    $job_count_field = $form_state->get('job_count');
    $add_button = $job_count_field + 1;
    $form_state->set('job_count', $add_button);
    $form_state->setRebuild();
  }

  public function removeCallback(array &$form, FormStateInterface $form_state) {
    $job_count_field = $form_state->get('job_count');
    if ($job_count_field > 1) {
      $remove_button = $job_count_field - 1;
      $form_state->set('job_count', $remove_button);
    }
   $form_state->setRebuild();
  }

  /**
  * Getter method for Form ID.
  *
  * The form ID is used in implementations of hook_form_alter() to allow other
  * modules to alter the render array built by this form controller.  it must
  * be unique site wide. It normally starts with the providing module's name.
  *
  * @return string
  *   The unique ID of the form defined by this class.
  */
  public function getFormId() {
    return 'apply_form';
  }

  /**
  * Implements form validation.
  *
  * The validateForm method is the default method called to validate input on
  * a form.
  *
  * @param array $form
  *   The render array of the currently built form.
  * @param FormStateInterface $form_state
  *   Object describing the current state of the form.
  */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // $title = $form_state->getValue('title');
    // if (strlen($title) < 5) {
			// $len = strlen($title);
// // Logs a notice
// \Drupal::logger('my_module')->notice($len);
      // // Set an error for the form element with a key of "title".
      // $form_state->setErrorByName('title', $this->t('The title must be at least 5 characters long.'));
    // }
  }

  /**
  * Implements a form submit handler.
  *
  * The submitForm method is the default method called for any submit elements.
  *
  * @param array $form
  *   The render array of the currently built form.
  * @param FormStateInterface $form_state
  *   Object describing the current state of the form.
  */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /*
    * This would normally be replaced by code that actually does something
    * with the title.
    */
		$userCurrent = \Drupal::currentUser();
		$uid = $userCurrent->id();
    $title = $form_state->getValue('title');
    $body = $form_state->getValue('body');
    $jobs = $form_state->getValue('job');
		
		$targets = array();
		// foreach($jobs as $job){ 
			// $emp_name = $job['employer_name'];
			// $emp_years = $job['employment_years'];
			// $paragraph = Paragraph::create([
			// 'type' => 'job',
			// 'employer_name' => array(
					// "value"  =>  $emp_name,
				// ),
			// 'employment_years' => array(
					// "value"  =>  $emp_years,
				// ),
			// ]);
			// $paragraph->save();
			// $targets[] = array(
				// 'target_id' => $paragraph->id(),
				// 'target_revision_id' => $paragraph->getRevisionId(),
			// );
		// }
		// $node = Node::create(['type' => 'application']);
		// $node->set('title', $title);

		// //Body can now be an array with a value and a format.
		// //If body field exists.
	
		// $node->set('body', $body);
		// $node->set('uid', $uid);
		// $node->set('employment_history', $targets);
		// $node->status = 1;
		// $node->enforceIsNew();
		// $node->save();
		echo '<pre>'; print_r($title); echo '</pre>'; exit;
    drupal_set_message($this->t('Application %title is submitted successfully.', ['%title' => $title]));
  }

}