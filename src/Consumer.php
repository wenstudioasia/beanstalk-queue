<?php

/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    wenstudio<wenstudio@asia.com>
 * @copyright wenstudio<wenstudio@asia.com>
 * @link      https://github.com/wenstudioasia/beanstalk-queue
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace Wenstudio\BeanstalkQueue;


/**
 * Interface Consumer
 * @package Wenstudio\BeanstalkQueue
 */
interface Consumer
{
    public function consume($job);
}