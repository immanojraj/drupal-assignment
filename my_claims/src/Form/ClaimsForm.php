<?php

namespace Drupal\my_claims\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Datetime\DrupalDateTime;

class ClaimsForm extends FormBase {
    public function getFormId() {
        return 'submit_claims_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {

        $form['tabs'] = [
            '#type' => 'vertical_tabs',
          ];
        
        // Tab 1.
        $form['tab_1'] = [
            '#type' => 'details',
            '#title' => $this->t('Submit Claims'),
            '#group' => 'tabs',
        ];
        $form['tab_2'] = [
            '#type' => 'details',
            '#title' => $this->t('View Claims'),
            '#group' => 'tabs',
        ];
        $form['claims_number'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Claims Number'),
            '#disabled' => TRUE,
            '#attributes' => ['placeholder' => 'Auto-generated 9-digit number'],
            '#element_validate' => [
                [$this, 'generateClaimsNumber'],
            ],
        ];

        $form['patient_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Patient Name'),
            '#required' => true,
            '#element_validate' => [
                [$this, 'validateAlphabetical'],
            ],
        ];

        $form['service_type'] = [
            '#type' => 'select',
            '#title' => $this->t('Service Type'),
            '#options' => [
                'medical' => $this->t('Medical'),
                'dental' => $this->t('Dental'),
            ],
            '#element_validate' => [
                [$this, 'validateServiceType'],
            ],
        ];

        $form['provider_name'] = [
            '#type' => 'textfield',
            '#required' => true,
            '#title' => $this->t('Provider Name'),
        ];

        $form['claims_value'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Claims Value'),
            '#required' => true,
            '#field_prefix' => '$',
        ];

        $default_date = new DrupalDateTime('now');
        $form['submission_date'] = [
            '#type' => 'datetime',
            '#title' => $this->t('Submission Date'),
            '#default_value' => $default_date,
            '#element_validate' => [
                [$this, 'validateSubmissionDate'],
            ],
        ];

        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Submit Claims'),
        ];

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
    }

    public function generateClaimsNumber(array &$form, FormStateInterface $form_state) {
        // Auto-generate a 9-digit number and set it in the claims_number field.
        $claims_number = mt_rand(100000000, 999999999);
        $form_state->setValue('claims_number', $claims_number);
    }

    public function validateFreeText(array &$element, FormStateInterface $form_state) {
        $value = $element['#value'];
        // Example: Check if the value is empty.
        if (empty($value)) {
            $form_state->setError($element, $this->t('Free Text field cannot be empty.'));
        }
    }

    public static function validateAlphabetical($element, FormStateInterface $form_state, $form) {
        $value = $element['#value'];
        if (!preg_match('/^[a-zA-Z\s.\'-]+$/u', $value)) {
            $form_state->setError($element, t('Only alphabetical characters are allowed for Patient Name.'));
        }
    }

    public static function validateServiceType($element, FormStateInterface $form_state, $form) {
        $value = $element['#value'];
        $allowed_types = ['medical', 'dental'];

        if (!in_array($value, $allowed_types)) {
            $form_state->setError($element, t('Service Type must be either "Medical" or "Dental".'));
        }
    }

    public function validateNumericWithPrefix($element, FormStateInterface $form_state, $form) {
        //$value = $form_state->getValue(['claims_value', 0, 'value']);

        // Check if the value is numeric and starts with '$'.
        // if (!is_numeric($value)) {
        //     $form_state->setError($element, t('Claims Value must be numeric and start with "$".'));
        // }
    }

    public function validateSubmissionDate($element, FormStateInterface $form_state, $form) {
        $value = $element['#value'];
        // Check if the value is a valid timestamp or empty (allowing user input).
        if (!empty($submission_date) && strtotime($submission_date) === false) {
            $form_state->setError($element, t('Invalid Submission Date. Please enter a valid date or leave it empty to capture the system date and time.'));
        }
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        $data = [
            'claims_number' => $form_state->getValue('claims_number'),
            'patient_name' => $form_state->getValue('patient_name'),
            'service_type' => $form_state->getValue('service_type'),
            'provider_name' => $form_state->getValue('provider_name'),
            'claims_value' => '$' . $form_state->getValue('claims_value'),
            'submission_date' => $form_state->getValue('submission_date'),
        ];
        // Call the ClaimsResource to handle saving data to a local JSON file.
        $claims_resource = \Drupal::service('plugin.manager.rest')->createInstance('claims_resource');
        $claims_resource->post($data);

        // Display success message with the generated Claims Number.
        $this->messenger()->addMessage($this->t('Claims submitted successfully. Claims Number: @claims_number', ['@claims_number' => $data['claims_number']]));
    }




}
