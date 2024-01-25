<?php

namespace Drupal\my_claims\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\my_claims\ClaimsService;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Provides a resource to handle storing claims data.
 *
 * @RestResource(
 *   id = "claims_resource",
 *   label = @Translation("Claims Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/claims"
 *   }
 * )
 */
class ClaimsResource extends ResourceBase {


    /**
   * Constructs a new ClaimsResource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\DependencyInjection\ContainerBuilder $container
   *   The container.
   */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger) {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    }

    /**
   * {@inheritdoc}
   */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
        $configuration,
        $plugin_id,
        $plugin_definition,
        $container->getParameter('serializer.formats'),
        $container->get('logger.factory')->get('my_claims')
        );
    }

    /**
   * Responds to POST requests.
   *
   * @param array $data
   *   The data to store.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
    public function post(array $data = []) {
        // Load existing data from the JSON file.
        $existing_data = $this->loadJsonFile();

        // Append new data.
        $existing_data[] = $data;

        // Save the updated data back to the JSON file.
        $this->saveJsonFile($existing_data);

        // Return a response.
        return new ResourceResponse(['message' => 'Data saved successfully.']);
    }

    // Helper function to load JSON file content.
    protected function loadJsonFile() {
        $file_path = 'public://claims_data.json';
        $file_contents = file_get_contents($file_path);
        return json_decode($file_contents, TRUE) ?: [];
    }

    // Helper function to save data to JSON file.
    protected function saveJsonFile(array $data) {
        $file_path = 'public://claims_data.json';
        $file_contents = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($file_path, $file_contents);
    }

}
