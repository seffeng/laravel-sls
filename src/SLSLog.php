<?php

namespace Seffeng\LaravelSLS;

use Aliyun\SLS\Client;
use Aliyun\SLS\Requests\PutLogsRequest;
use Aliyun\SLS\Models\LogItem;
use Illuminate\Support\Arr;
use Seffeng\LaravelSLS\Helpers\ArrayHelper;
use Illuminate\Support\Facades\Log;

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
     * @var Client
     */
    private $client;

    /**
     *
     * @author zxf
     * @date    2020年4月18日
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->accessKeyId = Arr::get($config, 'accessKeyId');
        $this->accessKeySecret = Arr::get($config, 'accessKeySecret');
        $this->endpoint = Arr::get($config, 'endpoint');
        $this->project = Arr::get($config, 'project');
        $this->logStore = Arr::get($config, 'logStore');
        $this->topic = Arr::get($config, 'topic');
        $this->source = Arr::get($config, 'source');

        if (is_null($this->endpoint) || is_null($this->accessKeyId) || is_null($this->accessKeySecret) || is_null($this->project) || is_null($this->logStore))
        {
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
            $putLogsRequest  = new PutLogsRequest($this->project, $this->logStore, $this->getTopic(), $this->getSource(), $logItems);
            return $this->client->putLogs($putLogsRequest);
        } catch (\Aliyun\SLS\Exception $e) {
            Log::channel('daily')->error($e->getMessage(), $contents);
            return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * @author zxf
     * @date   2020年4月28日
     * @param string $topic
     * @return \Seffeng\LaravelSLS\SLSLog
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
     * @return \Seffeng\LaravelSLS\SLSLog
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
}
