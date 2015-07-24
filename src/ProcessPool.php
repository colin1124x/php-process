<?php namespace Colin;

class ProcessPool
{
    protected $pool = array();
    public function add(Process $p)
    {
        $this->pool[] = $p;
    }

    public function top($n, \Closure $callback)
    {
        $n = min((int) $n, count($this->pool));

        $finish = array();
        while (count($this->pool) > 0) {
            foreach ($this->pool as $k => $p) {
                $res = pcntl_waitpid($p->pid(), $status, WNOHANG);

                if($res == -1 || $res > 0) {
                    $finish[] = $p;
                    unset($this->pool[$k]);
                }
            }
            if (count($finish) >= $n) break;
        }

        $callback($finish);
    }

    public function waitAll(\Closure $callback)
    {
        $this->top(count($this->pool), $callback);
    }
}
