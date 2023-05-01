<?php
declare(strict_types=1);

namespace App\Controller\Laboratory\Ws;

use App\Constants\Laboratory\WsMessage;
use App\Controller\AbstractController;
use App\Model\Auth\User;
use App\Pool\Redis;

use App\Task\Laboratory\FriendWsTask;
use App\Task\Laboratory\GroupWsTask;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Exception\HttpException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\Utils\Context;
use Hyperf\WebSocketServer\Sender;
use Swoole\Http\Request;
use Swoole\Websocket\Frame;
use App\Constants\Laboratory\ChatRedisKey;
use App\Foundation\Facades\MessageParser;
use App\Service\Laboratory\InitService;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Swoole\WebSocket\Server as WebSocketServer;

/**
 * chat
 * Class WebsocketController
 * @package App\Controller\Laboratory\Ws
 * @Author YiYuan-Lin
 * @Date: 2021/4/25
 */
class WebsocketController extends AbstractController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{
    /**
     * @Inject()
     * @var Sender
     */
    private $sender;

    /**
     * User send information
     * @param \Swoole\Http\Response|WebSocketServer $server
     * @param Frame $frame
     */
    public function onMessage($server, Frame $frame): void
    {
            $message = json_decode($frame->data, true);
            conSet('chat_message', MessageParser::encode([
                'message' => $message['message'],
                'file' => $message['file'] ?? '',
            ]));
            $targetUri = $message['uri'] ?? '';
            $requestMethod = $message['method'] ?? 'GET';
            $dispatcher = $this->container
                ->get(DispatcherFactory::class)
                ->getDispatcher('ws');
            $dispatched = make(Dispatched::class, [
                $dispatcher->dispatch($requestMethod, $targetUri)
            ]);
            if ($dispatched->isFound()) {
                //Routing treatment
                $result = call_user_func([
                    make($dispatched->handler->callback[0]),
                    $dispatched->handler->callback[1],
                ]);
                if ($result !== NULL) {
                    if (!empty($result['fd'])){
                        if (is_array($result['fd'])) {
                            foreach ($result['fd'] as $fd) {
                                $server->push((int) $fd, json_encode($result['message_data']));
                            }
                        }else {
                            $server->push((int) $result['fd'], json_encode($result['message_data']));
                        }
                    }
                }
            }
    }

    /**
     * User connection server
     * @param \Swoole\Http\Response|WebSocketServer $server
     * @param Request $request
     */
    public function onOpen($server, Request $request): void
    {
        //Whether to re -connected, if it is a disconnection, the new user will not notify the new user of friends to go online.
        $isReconnection = conGet('is_reconnection') ?? false;
        //Get chatting information
        $initInfo = InitService::getInstance()->initialization();
        //Get user information
        $userInfo = conGet('user_info');
        //Place online users in redis
        Redis::getInstance()->hSet(ChatRedisKey::ONLINE_USER_FD_KEY, (string) $initInfo['user_info']['id'], (string) $request->fd);
        //Place the FD corresponding to the online user ID in redis
        Redis::getInstance()->hSet(ChatRedisKey::ONLINE_FD_USER_KEY, (string) $request->fd, (string) $initInfo['user_info']['id']);

        //Connect information sending
        $server->push($request->fd, MessageParser::encode($initInfo));
        //Notify your friend the user to log in
        $this->container->get(FriendWsTask::class)->friendOnlineAndOfflineNotify($userInfo, WsMessage::FRIEND_ONLINE_MESSAGE, $isReconnection);
    }

    /**
     * User closure connection
     * @param \Swoole\Http\Response|WebSocketServer $server
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose($server, int $fd, int $reactorId): void
    {
        $uid = Redis::getInstance()->hGet(ChatRedisKey::ONLINE_FD_USER_KEY, (string) $fd);
        $userInfo = User::findById($uid)->toArray();

        //Delete users in the online list
        Redis::getInstance()->hDel(ChatRedisKey::ONLINE_USER_FD_KEY, (string) $userInfo['id']);
        Redis::getInstance()->hDel(ChatRedisKey::ONLINE_FD_USER_KEY, (string) $fd);
        //Notify your friend the user to log in
        $this->container->get(FriendWsTask::class)->friendOnlineAndOfflineNotify($userInfo, WsMessage::FRIEND_OFFLINE_MESSAGE);
    }
}