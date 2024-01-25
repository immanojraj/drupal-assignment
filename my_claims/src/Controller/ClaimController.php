<?php

// ViewClaimsController.php

namespace Drupal\my_claims\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Component\Serialization\Json;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Returns responses for View Claims routes.
 */
class ClaimController extends ControllerBase {

    /**
     * View claims page callback.
     */
    public function viewClaimsPage() {
        // Fetch claims data.
        $json_file_path = 'sites/default/files/claims_data.json';
        $json_content = file_get_contents($json_file_path);
        $data = Json::decode($json_content);

        // Build a link to the export CSV route.
        $url = Url::fromRoute('my_claims.export_csv');
        // Create the export button render array.
        $export_button = [
            '#markup' => Link::fromTextAndUrl($this->t('Export as CSV'), $url)->toString(),
        ];

        // Render claims table and export button.
        $claims_table = $this->buildClaimsTable($data);
        $rendered_export_button = \Drupal::service('renderer')->renderRoot($export_button);

        // Return a render array for the entire page.
        return [
            '#markup' => $claims_table . $rendered_export_button,
        ];
    }

    /**
     * Builds a sample claims table.
     */
    private function buildClaimsTable($data) {
        // Example: Build claims table render array.
        $rows = [];

        foreach ($data as $claim) {
            $rows[] = [
                'patient_name' => $claim['patient_name'],
                'service_type' => $claim['service_type'],
                'provider_name' => $claim['provider_name'],
                'claims_value' => $claim['claims_value'],
            ];
        }

        $header = [
            'patient_name' => $this->t('Patient Name'),
            'service_type' => $this->t('Service Type'),
            'provider_name' => $this->t('Provider Name'),
            'claims_value' => $this->t('Claims Number'),
        ];

        $table = [
            '#theme' => 'table',
            '#header' => $header,
            '#rows' => $rows,
        ];

        // Debug statements
        \Drupal::logger('my_claims')->debug('Header: @header', ['@header' => print_r($header, TRUE)]);
        \Drupal::logger('my_claims')->debug('Rows: @rows', ['@rows' => print_r($rows, TRUE)]);

        // Return the claims table render array.
        return \Drupal::service('renderer')->renderRoot($table);
    }

    /**
     * Export CSV controller method.
     */
    public function exportCsv() {
        // Fetch claims data.
        $json_file_path = 'sites/default/files/claims_data.json';
        $json_content = file_get_contents($json_file_path);
        $data = Json::decode($json_content);

        // Set headers for CSV download.
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="claims_export.csv"',
        ];

        // Create a CSV file.
        $file = fopen('php://output', 'w');

        // Write headers.
        $header = ['Patient Name', 'Service Type', 'Provider Name', 'Claims Number'];
        fputcsv($file, $header);

        // Write data.
        foreach ($data as $claim) {
            fputcsv($file, [
                $claim['patient_name'],
                $claim['service_type'],
                $claim['provider_name'],
                $claim['claims_value'],
            ]);
        }

        fclose($file);

        // Return the response.
        return new Response('', 200, $headers);
    }
}



