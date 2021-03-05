<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\UMeng\Push;

use GuzzleHttp\Exception\GuzzleException;
use Larva\Support\BaseObject;
use Larva\Support\Exception\ConnectionException;
use Larva\Support\Traits\HasHttpRequest;
use Larva\UMeng\Push\Messages\BaseMessage;
use Psr\Http\Message\RequestInterface;

/**
 * 友盟客户端实例
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Client extends BaseObject
{
    use HasHttpRequest;

    /**
     * @var string
     */
    public $appKey;

    /**
     * @var string
     */
    public $appMasterSecret;

    /**
     * @var string 安卓推送时是否启用
     */
    public $miActivity;

    /**
     * 初始化
     */
    public function init()
    {
        $this->baseUrl('https://msgapi.umeng.com');
        $this->middlewares[] = function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $postBody = $request->getBody()->getContents();
                $sign = md5($request->getMethod() . $request->getUri() . $postBody . $this->appMasterSecret);
                $query = http_build_query(['sign' => $sign], '', '&');
                $request = \GuzzleHttp\Psr7\Utils::modifyRequest($request, ['body' => $postBody, 'query' => $query]);
                return $handler($request, $options);
            };
        };
    }

    /**
     * 发送
     * @param array|BaseMessage $message
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Larva\Support\Exception\ConnectionException
     */
    public function sendRaw($message)
    {
        if ($message instanceof BaseMessage) {
            $message = $message->getJsonBody();
        }
        return $this->postJSON('api/send', $message);
    }

    /**
     * 任务类消息状态查询
     * @param string $taskId
     * @return array
     */
    public function status($taskId)
    {
        return $this->postJSON('api/status', ['task_id' => $taskId]);
    }

    /**
     * 任务类消息取消
     * @param string $taskId
     * @return array
     */
    public function clean($taskId)
    {
        return $this->postJSON('api/cancel', ['task_id' => $taskId]);
    }

    /**
     * 文件上传
     * @param string $content
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Larva\Support\Exception\ConnectionException
     */
    public function upload($content)
    {
        return $this->postJSON('upload', ['content' => $content]);
    }

    /**
     * Issue a POST request to the given URL.
     *
     * @param string $url
     * @param array $data
     * @return array
     * @throws ConnectionException
     * @throws GuzzleException
     */
    public function postJSON(string $url, array $data = [])
    {
        $this->acceptJson();
        $this->asJson();
        $data['appkey'] = $this->appKey;
        $data['timestamp'] = time();
        $response = $this->post($url, $data);
        return $response->json();
    }
}