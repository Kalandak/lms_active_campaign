<?php

/**
 * @file
 * Contains \Drupal\ac_sample\Controller\Studentac_sampleController
 */

namespace Drupal\ac_sample\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;
use GuzzleHttp\Client;
use Drupal\key\KeyRepository;
use Drupal\Core\Entity\EntityTypeManager;

class Studentac_sampleController extends ControllerBase
{

  /**
   * The database connection object.
   * 
   * @var Drupal\Core\Database\Connection;
   */
  protected $connection;

  /**
   * Guzzle Http Client.
   * 
   * @var GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * Key repository service.
   * 
   * @var Drupal\key\KeyRepository
   */
  protected $keyRepository;

  /**
   * The entity type mmanager service.
   * 
   * @var Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Constructs a new Studentac_sampleController.
   * 
   * @param \GuzzleHttp\Client $http_client
   * @param \Drupal\Core\Database\Connection $connection
   * @param \Drupal\key\KeyRepository $key_repository
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   */
  public function __construct(Client $http_client, Connection $connection, KeyRepository $key_repository, EntityTypeManager $entity_type_manager)
  {
    $this->httpClient = $http_client;
    $this->connection = $connection;
    $this->keyRepository = $key_repository;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('http_client'),
      $container->get('database'),
      $container->get('key.repository'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Syncs contact for the first time into the Activecampaign system. 
   * 
   * @param array $params The options to set on the request.
   */
  public function syncContact($params = [])
  {
    // Http client
    $client = $this->httpClient;
    // Set base path
    $url = 'https://sitename.api-us1.com/admin/api.php';
    // Define request options
    $requestOptions = [
      'auth' => [
        $this->keyRepository->getKey('activecampaign_auth')->getKeyValues()['username'],
        $this->keyRepository->getKey('activecampaign_auth')->getKeyValues()['password'],
      ],
      'headers' => [
        'Content-Type' => 'application/x-www-form-urlencoded',
      ],
      'form_params' => [
        'api_key' => $this->keyRepository->getKey('activecampaign_api_key')->getKeyValue(),
        'api_action' => 'contact_sync',
        'api_output' => 'json',
      ],
    ];
    // Build form params from those passed into method
    foreach ($params as $key => $value) {
      $requestOptions['form_params'][$key] = $value;
    }
    // Try/catch request
    try {
      $response = $client->request('POST', $url, $requestOptions);
      $data = $response->getBody();
    } catch (RequestException $e) {
      watchdog_exception('ac_sample', $e->getMessage());
    }
    // Return data in JSON
    return json_decode($data,true);
  }

  /**
   * Provides debug output.
   */
  public function debug()
  {
    return [
      // TODO: Define testGet(); function.
      // '#markup' => print_r(json_decode($this->testGet()), TRUE),
    ];
  }

  /**
   * Count the number of flags for a given user.
   */
 public function countFlagUser($uid)
 {
     $flagStorage = $this->entityTypeManager->getStorage('flagging');
     $flagQuery = $flagStorage->getQuery()->condition('flag_id', 'add_course')->condition('uid', $uid)->execute();
      $numRows = count($flagQuery);
     return $numRows;
 }

  /**
   * Add student to enrollment automation.
   */
 public function studentEnroll($params = [])
  {
    // Http client
    $client = $this->httpClient;
    // Set base path
    $url = 'https://sitename.api-us1.com/admin/api.php';
    // Define request options
    $requestOptions = [
      'auth' => [
        $this->keyRepository->getKey('activecampaign_auth')->getKeyValues()['username'],
        $this->keyRepository->getKey('activecampaign_auth')->getKeyValues()['password'],
      ],
      'headers' => [
        'Content-Type' => 'application/x-www-form-urlencoded',
      ],
      'form_params' => [
        'api_key' => $this->keyRepository->getKey('activecampaign_api_key')->getKeyValue(),
        'api_action' => 'automation_contact_add',
        'api_output' => 'json',
      ],
    ];
    // Build form params from those passed into method
    foreach ($params as $key => $value) {
      $requestOptions['form_params'][$key] = $value;
    }
    // Try/catch request
    try {
      $response = $client->request('POST', $url, $requestOptions);
      $data = $response->getBody();
    } catch (RequestException $e) {
      watchdog_exception('ac_sample', $e->getMessage());
    }
    // Return data in JSON
    return json_decode($data);
  }
  
  
  /**
   * remove user from automation
   */
 public function RemoveContactAutomation($params = [])
  {
    // Http client
    $client = $this->httpClient;
    // Set base path
    $url = 'https://sitename.api-us1.com/admin/api.php';
    // Define request options
    $requestOptions = [
      'auth' => [
        $this->keyRepository->getKey('activecampaign_auth')->getKeyValues()['username'],
        $this->keyRepository->getKey('activecampaign_auth')->getKeyValues()['password'],
      ],
      'headers' => [
        'Content-Type' => 'application/x-www-form-urlencoded',
      ],
      'form_params' => [
        'api_key' => $this->keyRepository->getKey('activecampaign_api_key')->getKeyValue(),
        'api_action' => 'automation_contact_remove',
        'api_output' => 'json',
      ],
    ];
    // Build form params from those passed into method
    foreach ($params as $key => $value) {
      $requestOptions['form_params'][$key] = $value;
    }
    // Try/catch request
    try {
      $response = $client->request('POST', $url, $requestOptions);
      $data = $response->getBody();
    } catch (RequestException $e) {
      watchdog_exception('ac_sample', $e->getMessage());
    }
    // Return data in JSON
    return json_decode($data);
  }

 
 
}
