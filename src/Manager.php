<?php namespace Colin\Process;

class Manager
{
    protected $lock = false;
    protected $works = array();

    public function add(\Closure $work, array $params = array())
    {
        if ($this->lock) throw new \RuntimeException('process manager is locked !!');

        $this->works[] = array($work, $params);

        return $this;
    }

    public function exec(\Closure $callback = null, & $pid_pool = array())
    {
        if ($this->lock) throw new \RuntimeException('process is running !!');

        // 只上鎖,不解鎖
        $this->lock = true;

        $callback = $callback ?: function(){};

        while ( ! empty($this->works)) {

            $process = array_pop($this->works);
            // 開出子程序
            $pid = pcntl_fork();

            if (-1 === $pid) {
                throw new \RuntimeException('無法使用 pcntl_fork!');
            } elseif ($pid) {
                $pid_pool[] = $pid;
            } else {
                try {

                    $callback(call_user_func_array($process[0], $process[1]));

                    // 子程序順利結束
                    exit(0);
                } catch (\Exception $e) {
                    exit($e->getCode());
                }
            }
        }

    }

    public function top($n, \Closure $callback = null)
    {
        $pid_pool = array();
        $this->exec(null, $pid_pool);

        $n = min((int) $n, count($pid_pool));
        $finish = array();

        while ( ! empty($pid_pool)) {
            foreach ($pid_pool as $k => $pid) {
                $res = pcntl_waitpid($pid, $status, WNOHANG);

                if($res == -1 || $res > 0) {
                    $finish[] = $pid;
                    unset($pid_pool[$k]);
                }

            }
            if (count($finish) >= $n) break;
        }

        $callback and $callback($finish);

        return $finish;
    }
}
