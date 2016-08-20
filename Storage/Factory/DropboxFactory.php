<?php

namespace Bordeux\Bundle\FileDropboxProviderBundle\Storage\Factory;


use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Bordeux\Bundle\FileBundle\Entity\Storage;
use Bordeux\Bundle\FileBundle\Storage\StorageFactory;
use Bordeux\Bundle\FileBundle\Storage\StorageProvider;
use Bordeux\Bundle\FileDropboxProviderBundle\Storage\Provider\DropboxStorageProvider;

/**
 * Class S3Factory
 * @package Bordeux\Bundle\FileDropboxProviderBundle\Storage\Factory
 * @author Krzysztof Bednarczyk
 */
class DropboxFactory implements StorageFactory
{

    /**
     * @var S3Client[]
     */
    protected $clients = [];

    /**
     * @param Storage $storage
     * @return StorageProvider
     * @author Krzysztof Bednarczyk
     */
    public function getProvider(Storage $storage)
    {
        $params = $storage->getParametersArray();

        return new DropboxStorageProvider(
            $this->getAwsClient($storage, $params),
            $params['bucket']
        );
    }


    /**
     * @param array $params
     * @author Krzysztof Bednarczyk
     */
    public function validParameters(array $params)
    {
        //@todo: check parameters and throw exceptions
    }

    /**
     * @param Storage $storage
     * @return S3Client
     * @author Krzysztof Bednarczyk
     */
    public function getAwsClient(Storage $storage, $params)
    {
        if (isset($this->clients[$storage->getId()])) {
            return $this->clients[$storage->getId()];
        }


        $this->validParameters($params);

        $client = new S3Client(array(
            'credentials' => new Credentials(
                $params['access_key'],
                $params['secret_key']
            ),
            'region' => $params['region'],
            'bucket' => $params['bucket'],
            'version' => "2006-03-01"
        ));

        $this->clients[$storage->getId()] = $client;

        return $client;
    }
}