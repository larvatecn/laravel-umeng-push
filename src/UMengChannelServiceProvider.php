<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\UMeng\Push;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

/**
 * UMeng 通知
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class UMengChannelServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('umeng.push.android', function () {
            return new Client(config('services.umeng.push.android'));
        });
        $this->app->singleton('umeng.push.ios', function () {
            return new Client(config('services.umeng.push.ios'));
        });
        Notification::extend('device', function () {
            return new DeviceChannel();
        });
    }

    /**
     * Get services.
     *
     * @return array
     * @author yansongda <me@yansongda.cn>
     *
     */
    public function provides()
    {
        return ['umeng.push.android', 'umeng.push.ios'];
    }
}