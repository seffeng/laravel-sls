<?php
/**
 * @link http://github.com/seffeng/
 * @copyright Copyright (c) 2020 seffeng
 */
namespace Seffeng\LaravelSLS;

use Aliyun\SLS\Client;
use Aliyun\SLS\Requests\PutLogsRequest;
use Aliyun\SLS\Models\LogItem;
use Illuminate\Support\Arr;
use Seffeng\LaravelSLS\Helpers\ArrayHelper;
use Illuminate\Support\Facades\Log;
use Seffeng\LaravelSLS\Exceptions\SLSException;

class SLSLog
{

    /**
     *
     * @var string
     */
    private $accessKeyId;

    /**
     *
     * @var string
     */
    private $accessKeySecret;

    /**
     *
     * @var string
     */
    private $endpoint;

    /**
     *
     * @var string
     */
    private $project;

    /**
     *
     * @var string
     */
    private $logStore;

    /**
     *
     * @var string
     */
    private $topic;

    /**
     *
     * @var string
     */
    private $source;

    /**
     *
     * @var string
     */
    private $errorLogChannel = 'daily';

    /**
     *
     * @var Client
     */
    private $client;

    /**
     *
     * @var array
     */
    private static $config;

    /**
     *
     * @var string
     */
    private $store = 'default';

    /**
     *
     * @author zxf
     * @date    2020年4月18日
     * @param array $config
     */
    public function __construct(array $config)
    {
        static::$config = $config;
        $store = ArrayHelper::getValue(static::$config, 'store');
        $store && $this->setStore($store);
        $this->loadConfig();

        if (is_null($this->endpoint) || is_null($this->accessKeyId) || is_null($this->accessKeySecret) || is_null($this->project) || is_null($this->logStore)) {
            throw new \RuntimeException('Warning: accesskeyid, accesskeysecret, endpoint, project, logStore cannot be empty.');
        }
        $this->client = new Client($this->endpoint, $this->accessKeyId, $this->accessKeySecret);
    }

    /**
     *
     * @author zxf
     * @date    2020年4月18日
     * @param string|array $content
     * @param array $content
     */
    public function putLogs(array $contents)
    {
        try {
            if (is_null($this->client)) {
                throw new \RuntimeException('Warning: accesskeyid, accesskeysecret, endpoint, project, logStore cannot be empty.');
            }
            $depth = ArrayHelper::getDepth($contents);
            if ($depth == 1) {
                $logItems = [
                    new LogItem($contents),
                ];
            } elseif ($depth == 2) {
                $logItems = [];
                foreach ($contents as $content) {
                    if (ArrayHelper::getDepth($content) === 1) {
                        $logItems[] = new LogItem($content);
                    } else {
                        throw new \Exception('Warning: Content Invalid');
                    }
                }
            } else {
                throw new \RuntimeException('Warning: Content Invalid.');
            }
            $putLogsRequest  = new PutLogsRequest($this->getProject(), $this->getLogStore(), $this->getTopic(), $this->getSource(), $logItems);
            return $this->client->putLogs($putLogsRequest);
        } catch (\Aliyun\SLS\Exception $e) {
            Log::channel($this->errorLogChannel)->error($e->getMessage(), $contents);
            return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * @author zxf
     * @date   2020年11月23日
     * @param string $store
     * @throws SLSException
     * @return \Seffeng\LaravelSLS\SLSLog
     */
    public function loadConfig(string $store = null)
    {
        !is_null($store) && $this->setStore($store);
        $customer = Arr::get(static::$config, 'stores.' . $this->getStore());
        if ($customer) {
            $this->accessKeyId = Arr::get($customer, 'accessKeyId');
            $this->accessKeySecret = Arr::get($customer, 'accessKeySecret');
            $this->endpoint = Arr::get($customer, 'endpoint');
            $this->project = Arr::get($customer, 'project');
            $this->logStore = Arr::get($customer, 'logStore');
            $this->topic = Arr::get($customer, 'topic');
            $this->source = Arr::get($customer, 'source');
            $errorLogChannel = Arr::get(static::$config, 'errorlogChannel');
            $errorLogChannel && $this->errorLogChannel = $errorLogChannel;

            if (empty($this->getAccessKeyId()) || empty($this->getAccessKeySecret())) {
                throw new SLSException('Warning: accessKeyId, accessKeySecret cannot be empty.');
            }
        } else {
            throw new SLSException('The store['. $this->getStore() .'] is not found.');
        }
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2020年11月23日
     * @return string
     */
    public function getAccessKeyId()
    {
        return $this->accessKeyId;
    }

    /**
     *
     * @author zxf
     * @date   2020年11月23日
     * @return string
     */
    public function getAccessKeySecret()
    {
        return $this->accessKeySecret;
    }

    /**
     *
     * @author zxf
     * @date   2020年11月23日
     * @param string $store
     * @return static
     */
    public function setStore(string $store)
    {
        $this->store = $store;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2020年11月23日
     * @return string
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     *
     * @author zxf
     * @date   2020年4月28日
     * @param string $topic
     * @return static
     */
    public function setTopic(string $topic)
    {
        $this->topic = $topic;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2020年4月28日
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     *
     * @author zxf
     * @date   2020年4月28日
     * @param string $source
     * @return static
     */
    public function setSource(string $source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2020年4月28日
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     *
     * @author zxf
     * @date   2020年11月23日
     * @return static
     */
    public function setProject(string $project)
    {
        $this->project = $project;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2020年11月23日
     * @return string
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     *
     * @author zxf
     * @date   2020年11月23日
     * @return static
     */
    public function setLogStore(string $logStore)
    {
        $this->logStore = $logStore;
        return $this;
    }

    /**
     *
     * @author zxf
     * @date   2020年11月23日
     * @return string
     */
    public function getLogStore()
    {
        return $this->logStore;
    }
}
