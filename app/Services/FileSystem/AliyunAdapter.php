<?php
/*
|-----------------------------------------------------------------------------------------------------------
| laravel-admin-cms [ 简单高效的开发插件系统 ]
|-----------------------------------------------------------------------------------------------------------
| Licensed ( MIT )
| ----------------------------------------------------------------------------------------------------------
| Copyright (c) 2020-2021 https://gitee.com/liaodeiy/laravel-admin-cms All rights reserved.
| ----------------------------------------------------------------------------------------------------------
| Author: 廖春贵 < liaodeity@gmail.com >
|-----------------------------------------------------------------------------------------------------------
*/

namespace App\Services\FileSystem;


use App\Enums\UploadDriverEnum;
use App\Exceptions\BusinessException;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use OSS\Core\OssException;
use OSS\OssClient;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;

class AliyunAdapter extends AbstractAdapter
{
    protected $uploadManager;
    protected $bucketManager;
    private   $bucketName;
    private   $url;
    private   $accessKeyId;
    private   $accessKeySecret;
    private   $endpoint;
    private   $isCName = false;
    private   $params  = [];


    public function __construct ($prefix = '')
    {
        $driver                = UploadDriverEnum::ALIYUN;
        $this->accessKeyId     = config ('filesystems.disks.' . $driver . '.key_id');
        $this->accessKeySecret = config ('filesystems.disks.' . $driver . '.key_secret');
        $this->bucketName      = config ('filesystems.disks.' . $driver . '.bucket');
        $this->endpoint        = config ('filesystems.disks.' . $driver . '.endpoint');
        $this->url             = config ('filesystems.disks.' . $driver . '.url');
        $this->setPathPrefix ($prefix);
        try {
            $this->uploadManager = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint, $this->isCName, ...$this->params);
        } catch (OssException $e) {
            throw new BusinessException($e->getMessage ());
        }
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
        if ($config->has ('params')) {
            $this->params = $config->get ('params');
        }

        return $this->upload ($path, $contents);
    }

    protected function upload (string $path, $contents, $stream = false)
    {
        $path = $this->applyPathPrefix ($path);
        try {
            if ($stream) {
                $response = $this->uploadManager->putObject ($this->bucketName, $path, $contents, $this->params);
            } else {
                $response = $this->uploadManager->uploadFile ($this->bucketName, $path, $contents, $this->params);
            }

            return $response;
        } catch (OssException $ex) {
            throw new UploadException('上传文件到失败：' . $ex->message ());
        }
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
        return $this->write ($path, $contents, $config);
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
        return $this->writeStream ($path, $resource, $config);
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
        $contents = stream_get_contents ($resource);

        if ($config->has ('params')) {
            $this->params = $config->get ('params');
        }

        return $this->upload ($path, $contents, true);
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
        if (!$this->copy ($path, $newpath)) {
            return false;
        }

        return $this->delete ($path);
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

        try {
            $this->uploadManager->copyObject ($this->bucketName, $path, $this->bucketName, $newpath);
        } catch (OssException $exception) {
            return false;
        }

        return true;
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

        try {
            $this->uploadManager->deleteObject ($this->bucketName, $path);
        } catch (OssException $ossException) {
            return false;
        }

        return !$this->has ($path);
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

        return $this->uploadManager->doesObjectExist ($this->bucketName, $path, $this->params);
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
        $fileList = $this->listContents ($dirname, true);
        foreach ($fileList as $file) {
            $this->delete ($file['path']);
        }

        return !$this->has ($dirname);
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
        $list      = [];
        $directory = '/' == substr ($directory, -1) ? $directory : $directory . '/';
        $result    = $this->listDirObjects ($directory, $recursive);

        if (!empty($result['objects'])) {
            foreach ($result['objects'] as $files) {
                if ('oss.txt' == substr ($files['Key'], -7) || !$fileInfo = $this->normalizeFileInfo ($files)) {
                    continue;
                }
                $list[] = $fileInfo;
            }
        }

        // prefix
        if (!empty($result['prefix'])) {
            foreach ($result['prefix'] as $dir) {
                $list[] = [
                    'type' => 'dir',
                    'path' => $dir,
                ];
            }
        }

        return $list;
    }

    /**
     * File list core method.
     *
     * @param string $dirname
     * @param bool   $recursive
     *
     * @return array
     *
     * @throws OssException
     */
    public function listDirObjects ($dirname = '', $recursive = false)
    {
        $delimiter  = '/';
        $nextMarker = '';
        $maxkeys    = 1000;

        $result = [];

        while (true) {
            $options = [
                'delimiter' => $delimiter,
                'prefix'    => $dirname,
                'max-keys'  => $maxkeys,
                'marker'    => $nextMarker,
            ];

            try {
                $listObjectInfo = $this->uploadManager->listObjects ($this->bucketName, $options);
            } catch (OssException $exception) {
                throw $exception;
            }

            $nextMarker = $listObjectInfo->getNextMarker ();
            $objectList = $listObjectInfo->getObjectList ();
            $prefixList = $listObjectInfo->getPrefixList ();

            if (!empty($objectList)) {
                foreach ($objectList as $objectInfo) {
                    $object['Prefix']       = $dirname;
                    $object['Key']          = $objectInfo->getKey ();
                    $object['LastModified'] = $objectInfo->getLastModified ();
                    $object['eTag']         = $objectInfo->getETag ();
                    $object['Type']         = $objectInfo->getType ();
                    $object['Size']         = $objectInfo->getSize ();
                    $object['StorageClass'] = $objectInfo->getStorageClass ();
                    $result['objects'][]    = $object;
                }
            } else {
                $result['objects'] = [];
            }

            if (!empty($prefixList)) {
                foreach ($prefixList as $prefixInfo) {
                    $result['prefix'][] = $prefixInfo->getPrefix ();
                }
            } else {
                $result['prefix'] = [];
            }

            // Recursive directory
            if ($recursive) {
                foreach ($result['prefix'] as $prefix) {
                    $next              = $this->listDirObjects ($prefix, $recursive);
                    $result['objects'] = array_merge ($result['objects'], $next['objects']);
                }
            }

            if ('' === $nextMarker) {
                break;
            }
        }

        return $result;
    }

    /**
     * normalize file info.
     *
     * @return array
     */
    protected function normalizeFileInfo (array $stats)
    {
        $filePath = ltrim ($stats['Key'], '/');

        $meta = $this->getMetadata ($filePath) ?? [];

        if (empty($meta)) {
            return [];
        }

        return [
            'type'      => 'file',
            'mimetype'  => $meta['content-type'],
            'path'      => $filePath,
            'timestamp' => $meta['info']['filetime'],
            'size'      => $meta['content-length'],
        ];
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
        $path = $this->applyPathPrefix ($path);

        try {
            $metadata = $this->uploadManager->getObjectMeta ($this->bucketName, $path);
        } catch (OssException $exception) {
            return false;
        }

        return $metadata;
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
        $defaultFile = trim ($dirname, '/') . '/oss.txt';

        return $this->write ($defaultFile, '临时文件，当虚拟目录下有其他文件时，可删除此文件~', $config);
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
        $object = $this->applyPathPrefix ($path);
        $acl    = (AdapterInterface::VISIBILITY_PUBLIC === $visibility) ? OssClient::OSS_ACL_TYPE_PUBLIC_READ : OssClient::OSS_ACL_TYPE_PRIVATE;

        try {
            $this->uploadManager->putObjectAcl ($this->bucketName, $object, $acl);
        } catch (OssException $exception) {
            return false;
        }

        return compact ('visibility');
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
        try {
            $contents = $this->uploadManager->getObject ($this->bucketName, $path);
        } catch (OssException $exception) {
            return false;
        }

        return compact ('contents', 'path');
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
        try {
            $stream   = fopen ('php://temp', 'w+b');
            $contents = $this->uploadManager->getObject ($this->bucketName, $path);
            fwrite ($stream, $contents);
            rewind ($stream);
        } catch (OssException $exception) {
            return false;
        }

        return compact ('stream', 'path');
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
        return $this->normalizeFileInfo (['Key' => $path]);
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
        return $this->normalizeFileInfo (['Key' => $path]);
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
        return $this->normalizeFileInfo (['Key' => $path]);
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
