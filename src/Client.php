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

use Pheanstalk\Pheanstalk;

/**
 * Class BeanstalkQueue
 * @package support
 *
 * Strings methods
 * @method static void send($queue, $data, $delay=0)
 */
class Client
{
    /**
     * @var array $_connections
     */
    protected static $_connections = null;


    /**
     * @param string $name
     * @return object Pheanstalk Client
     */
    public static function connection($name = 'default')
    {
        if (!isset(static::$_connections[$name])) {
            $config = config('beanstalk_queue', config('plugin.wenstudioasia.beanstalk-queue.beanstalk', []));
            if (!isset($config[$name])) {
                throw new \RuntimeException("BeanstalkQueue connection $name not found");
            }
            $ip = $config[$name]['ip'];
            $port = $config[$name]['port'];
            $timeout = $config[$name]['timeout'];
            // $options = $config[$name]['options'];
            $client = Pheanstalk::create($ip, $port, $timeout);
            static::$_connections[$name] = $client;
        }
        return static::$_connections[$name];
    }

    /**
     * Send data to beanstalk
     * 
     * @param string $tube          tube name
     * @param string|array $data    parameter
     * @param int $priority         
     * @param int $delay            delay in seconds
     * @param int $retry_after      in seconds
     * @param string $instance      beanstalkd instance
     * 
     * @return Pheanstalk\Job 
     */
    public static function send($tube, $data, $priority = Pheanstalk::DEFAULT_PRIORITY, $delay = Pheanstalk::DEFAULT_DELAY, $retry_after = Pheanstalk::DEFAULT_TTR, $instance = 'default')
    {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        return static::connection($instance)
            ->useTube($tube)
            ->put($data, $priority, $delay, $retry_after);
    }

    public static function watch($tube, $instance = 'default')
    {
        return static::connection($instance)->watch($tube);
    }

    /**
     * block until get a job
     * 
     * job has getData() method to get payload
     * 
     * @param string $instance
     * @return Job
     * 
     */
    public static function reserve($instance = 'default')
    {
        return static::connection($instance)->reserve();
    }

    /**
     * reserve() with timeout
     * 
     * @param int $timeout timeout seconds
     * @return Job|null
     */
    public static function reserve_with_timeout($timeout, $instance = 'default')
    {
        return static::connection($instance)->reserveWithTimeout($timeout);
    }

    /**
     * when beanstalk client couldn't finish a job in given time，the client
     * use this function to renew, or timeout.
     * 
     * @param object $job reserve 的返回值
     */
    public static function touch($job, $instance = 'default')
    {
        return static::connection($instance)->touch($job);
    }

    /**
     * delete a job
     * 
     */
    public static function delete($job, $instance = 'default')
    {
        return static::connection($instance)->delete($job);
    }

    /**
     * re-shedule a job en queue when unsatisfied conditions
     * 
     */
    public static function release($job, $instance = 'default')
    {
        return static::connection($instance)->release($job);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::connection('default')->{$name}(...$arguments);
    }
}
