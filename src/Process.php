<?php namespace Colin;

class Process
{
    const EVENT_START = 1;
    const EVENT_CHILD_WORK_START = 10;
    const EVENT_CHILD_WORK_END = 11;

    protected $pid;
    protected $work;
    protected $payload;
    protected $events = array();
    public function __construct(\Closure $work, $payload = null)
    {
        $this->work = $work;
        $this->payload = $payload;
    }

    public function pid()
    {
        return $this->pid;
    }

    public function on($event, \Closure $callback)
    {
        if ( ! isset($this->events[$event])) $this->events[$event] = array();
        $this->events[$event][] = $callback;

        return $this;
    }

    public function exec(\Closure $callback = null)
    {
        $callback = $callback ?: function(){};

        // 開出子程序
        $pid = pcntl_fork();

        if (-1 === $pid) throw new \RuntimeException('無法使用 pcntl_fork!');

        // fire event

        if ($pid) {
            $this->pid = $pid;
            $this->fire(self::EVENT_START);
        } elseif (0 === $pid) {
            try {
                $this->fire(self::EVENT_CHILD_WORK_START);
                $work_result = call_user_func($this->work, $this->payload);
                $callback($work_result);
                $this->fire(self::EVENT_CHILD_WORK_END);

                // 子程序順利結束
                exit(0);
            } catch (\Exception $e) {
                exit($e->getCode());
            }
        }
    }

    private function fire($event)
    {
        if (isset($this->events[$event])) {
            foreach ($this->events[$event] as $event) {
                try {
                    $event();
                } catch (\Exception $e) {}
            }
        }
    }
}
