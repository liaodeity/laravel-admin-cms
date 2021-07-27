<?php

namespace App\Services\FileSystem;

use App\Enums\UploadDriverEnum;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Config;
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

class QiniuAdapter extends AbstractAdapter
{

    protected $uploadManager;
    protected $bucketManager;
    private   $accessKey;
    private   $accessSecret;
    private   $bucketName;
    private   $token;
    private   $url;

    public function __construct ($prefix = '')
    {
        $driver              = UploadDriverEnum::QINIU;
        $this->uploadManager = new UploadManager();
        $this->accessKey     = \config ('filesystems.disks.' . $driver . '.key');
        $this->accessSecret  = \config ('filesystems.disks.' . $driver . '.secret');
        $this->bucketName    = \config ('filesystems.disks.' . $driver . '.bucket');
        $this->url           = config ('filesystems.disks.' . $driver . '.url');
        $auth                = new Auth($this->accessKey, $this->accessSecret);
        $this->bucketManager = new BucketManager($auth);
        $this->token         = $auth->uploadToken ($this->bucketName);
        $this->setPathPrefix ($prefix);
    }

    public function put ($path, $contents, Config $config)
    {
        return $this->write ($path, $contents, $config);
    }

    /**
     * Write a new file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function write ($path, $contents, Config $config)
    {
        return $this->upload ($path, $contents);
    }

    protected function upload (string $path, $contents, $stream = false)
    {
        $path = $this->applyPathPrefix ($path);
        try {
            if ($stream) {
                $response = $this->uploadManager->put ($this->token, $path, $contents);
            } else {
                $response = $this->uploadManager->putFile ($this->token, $path, $contents);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
        list($uploadResult, $error) = $response;
        if ($uploadResult) {
            return $uploadResult;
        } else {
            throw new UploadException('上传文件到七牛失败：' . $error->message ());
        }
    }

    /**
     * Write a new file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream ($path, $resource, Config $config)
    {
        return $this->upload ($path, $resource, true);
    }

    /**
     * Update a file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function update ($path, $contents, Config $config)
    {
        return $this->upload ($path, $contents);
    }

    /**
     * Update a file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function updateStream ($path, $resource, Config $config)
    {
        return $this->upload ($path, $resource, true);
    }

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function rename ($path, $newpath)
    {
        $path    = $this->applyPathPrefix ($path);
        $newpath = $this->applyPathPrefix ($newpath);
        $error   = $this->bucketManager->rename ($this->bucketName, $path, $newpath);

        return $error == null ? true : false;
    }

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy ($path, $newpath)
    {
        $path    = $this->applyPathPrefix ($path);
        $newpath = $this->applyPathPrefix ($newpath);
        list($uploadResult, $error) = $this->bucketManager->copy ($this->bucketName, $path, $this->bucketName, $newpath);

        return $error == null ? true : false;
    }

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete ($path)
    {
        $path = $this->applyPathPrefix ($path);
        list($ret, $error) = $this->bucketManager->delete ($this->bucketName, $path);

        return $error == null ? true : false;
    }

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir ($dirname)
    {
        throw new \BadFunctionCallException('暂不支持该操作');
    }

    /**
     * Create a directory.
     *
     * @param string $dirname directory name
     * @param Config $config
     *
     * @return array|false
     */
    public function createDir ($dirname, Config $config)
    {
        throw new \BadFunctionCallException('暂不支持该操作');
    }

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false file meta data
     */
    public function setVisibility ($path, $visibility)
    {
        throw new \BadFunctionCallException('暂不支持该操作');
    }

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return array|bool|null
     */
    public function has ($path)
    {
        $path = $this->applyPathPrefix ($path);
        $stat = $this->bucketManager->stat ($this->bucketName, $path);
        if ($stat[0] == null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Read a file as a stream.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function readStream ($path)
    {
        throw new \BadFunctionCallException('暂不支持该操作');
    }

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function listContents ($directory = '', $recursive = false)
    {
        return $this->bucketManager->listFiles ($this->bucketName);
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata ($path)
    {
        return $this->read ($path);
    }

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read ($path)
    {
        $path = $this->applyPathPrefix ($path);
        list($fileInfo, $error) = $this->bucketManager->stat ($this->bucketName, $path);
        if ($fileInfo) {
            return $fileInfo;
        } else {
            throw new FileNotFoundException('对应文件不存在');
        }
    }

    /**
     * Get the size of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getSize ($path)
    {
        $fileInfo         = $this->read ($path);
        $fileInfo['size'] = $fileInfo['fsize'];

        return $fileInfo;
    }

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMimetype ($path)
    {
        $fileInfo = $this->read ($path);

        return $fileInfo['fileType'];
    }

    /**
     * Get the last modified time of a file as a timestamp.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getTimestamp ($path)
    {
        $fileInfo              = $this->read ($path);
        $fileInfo['timestamp'] = (int)$fileInfo['putTime'] / 10000000;

        return $fileInfo;
    }

    /**
     * Get the visibility of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getVisibility ($path)
    {
        throw new \BadFunctionCallException('暂不支持该操作');
    }

    public function getUrl ($path)
    {
        $path = $this->applyPathPrefix ($path);

        return trim ($this->url, '/') . '/' . trim ($path, '/');
    }
}
