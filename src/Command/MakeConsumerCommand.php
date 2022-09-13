<?php

namespace Wenstudio\BeanstalkQueue\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Webman\Console\Util;


class MakeConsumerCommand extends Command
{
    protected static $defaultName = 'beanstalk:consumer';
    protected static $defaultDescription = 'Make beanstalk work-queue consumer';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Consumer name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $output->writeln("Make consumer $name");

        $class = Util::nameToClass($name);
        $tube = Util::classToName($name);

        $file = app_path() . "/queue/beanstalk/$class.php";
        $this->createConsumer($class, $tube, $file);

        return self::SUCCESS;
    }

    /**
     * @param $class
     * @param $queue
     * @param $file
     * @return void
     */
    protected function createConsumer($class, $tube, $file)
    {
        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $controller_content = <<<EOF
<?php

namespace app\\queue\\beanstalk;

use Wenstudio\\BeanstalkQueue\\Consumer;
use Wenstudio\\BeanstalkQueue\\Client;

class $class implements Consumer
{
    // 要消费的队列名
    public \$tube = '$tube';
    // reserve 操作的超時秒數
    public \$reserve_timeout =10;
    // 指示退出訂閱
    public \$quit = false;
    // 连接名，对应 plugin/webman/beanstalk-queue/beanstalk.php 里的连接`
    public \$connection = 'default';

    // 消费
    public function consume(\$job)
    {
        \$data = \$job->getData();
        if (is_null(\$data)) {
            // Log
            return;
        }
        // processing

        Client::delete(\$job);
    }
}

EOF;
        file_put_contents($file, $controller_content);
    }

}
