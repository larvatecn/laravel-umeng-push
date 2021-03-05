# larva/laravel-umeng-notification-channel

适用于 Laravel 的友盟消息推送通道适配器

## 安装

```shell

composer require "larva/laravel-umeng-push" -vv
```

## 配置

添加配置到 `services.php`

```php
'umeng'=>[
    'push'=>[
        'android' => [
            'appKey' => '',
            'appMasterSecret' => '',
            'miActivity' => '',
        ],
        'ios' => [
            'appKey' => '',
            'appMasterSecret' => '',
        ],
    ]
]
```

## 使用

编写如下 通知类然后发出去就行了

```php
namespace App\Models;

class User {
    /**
     * 获取移动端设备属性
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany|object
     */
     public function routeNotificationForDevice()
     {
         return $this->devices()->latest('id')->first();
     }
}
```

```php
namespace App\Notifications;

use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return [\Larva\UMeng\Notifications\DeviceChannel::class];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDevice($notifiable)
    {
        /** @var \App\Models\UserDevice $device */
        if (!$device = $notifiable->routeNotificationFor('device', $this)) {
            return false;
        }

        $message = [
            'ticker' => '我们刚刚给用户增加了个通知功能。',    // 必填，通知栏提示文字
            'title' => '我们刚刚给用户增加了个通知功能。',    // 必填，通知标题
            'text' => '所以得测试测试好使不好使！',    // 必填，通知文字描述
        ];
        if ($device->isAndroid) {
            $android = new AndroidMessage();
            $android->setDeviceTokens($device->token);
            $android->setType($this->notificationType);//点对点推送
            $android->setPayload('display_type', $this->displayType);//通知消息
            $android->setPayloadBody('ticker', $message['ticker']);// 必填，通知栏提示文字
            $android->setPayloadBody('title', $message['title']);// 必填，通知标题
            $android->setPayloadBody('text', $message['text']);// 必填，通知文字描述

            return $android;
        } else {
            $ios = new IOSMessage();
            $ios->setDeviceTokens($device->token);
            $ios->setType($this->notificationType);//点对点推送
            $ios->setPayload('display_type', $this->displayType);//通知消息
            $ios->setAPS('alert', [
                'title' => $message['ticker'],
                'subtitle' => $message['title'],
                'body' => $message['text'],
            ]);
            return $ios;
        }
    }
}
```