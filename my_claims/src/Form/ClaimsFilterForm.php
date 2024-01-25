<?php

namespace Drupal\my_claims\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;


/**
 * Provides a form for filtering claims.
 */
class ClaimsFilterForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'claims_filter_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['patient_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Patient Name'),
      // Add more attributes as needed.
    ];

    $form['claims_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Claims Number'),
      // Add more attributes as needed.
    ];

    $form['service_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Service Type'),
      '#options' => [
        'medical' => $this->t('Medical'),
        'dental' => $this->t('Dental'),
        // Add more options as needed.
      ],
    ];

    $form['start_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Start Date'),
      '#date_date_format' => 'Y-m-d',
      // Add more attributes as needed.
    ];

    $form['end_date'] = [
      '#type' => 'date',
      '#title' => $this->t('End Date'),
      '#date_date_format' => 'Y-m-d',
      // Add more attributes as needed.
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Filter'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get the submitted values.
    $patient_name = $form_state->getValue('patient_name');
    $claims_number = $form_state->getValue('claims_number');
    $service_type = $form_state->getValue('service_type');
    $start_date = $form_state->getValue('start_date');
    $end_date = $form_state->getValue('end_date');

    // Redirect to the view page with the filter parameters as query parameters.
    $url = Url::fromRoute('my_claims.view_claims_page', [
      'patient_name' => $patient_name,
      'claims_number' => $claims_number,
      'service_type' => $service_type,
      'start_date' => $start_date,
      'end_date' => $end_date,
    ]);

    $form_state->setRedirectUrl($url);
  }
}
