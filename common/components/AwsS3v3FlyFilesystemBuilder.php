<?php
namespace common\components;

use Aws\S3\S3Client;
use League\Flysystem\Filesystem;
use trntv\filekit\filesystem\FilesystemBuilderInterface;
use yii\base\BaseObject;

/**
 * Class AwsS3v3FlyFilesystemBuilder
 * @author Eugene Terentev <eugene@terentev.net>
 */
class AwsS3v3FlyFilesystemBuilder extends BaseObject implements FilesystemBuilderInterface
{
    public $key;
    public $secret;
    public $region;
    public $bucket;
    public $endpoint;
    public $use_path_style_endpoint;

    /**
     * @return mixed
     */
    public function build()
    {
        $client = new S3Client([
//            'credentials' => [
//                'key'    => $this->key,
//                'secret' => $this->secret
//            ],
//            'region' => $this->region,
//            'version' => 'latest',

            'version' => 'latest',
            'region'  => $this->region,
            'endpoint'  => $this->endpoint,
            'use_path_style_endpoint' => $this->use_path_style_endpoint,
            'credentials' => [
                'key'    => $this->key,
                'secret' => $this->secret,
            ],
        ]);

        $adapter = new \League\Flysystem\AwsS3v3\AwsS3Adapter($client, $this->bucket);
        $filesystem = new Filesystem($adapter);

        return $filesystem;
    }
}