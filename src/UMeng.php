<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\UMeng\Push;

use Illuminate\Support\Facades\Facade;

/**
 * 友盟
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class UMeng extends Facade
{
    /**
     * Return the facade accessor.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'umeng.push.android';
    }

    /**
     * Return the facade accessor.
     *
     * @return string
     */
    public static function android()
    {
        return app('umeng.push.android');
    }

    /**
     * Return the facade accessor.
     *
     * @return string
     */
    public static function ios()
    {
        return app('umeng.push.ios');
    }
}