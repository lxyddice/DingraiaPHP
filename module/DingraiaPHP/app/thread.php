<?php
if ($bot) {
    declare(ticks=1);
    
    class Thread {
      protected static $names = [];
      protected static $fibers = [];
      protected static $params = [];
    
      public static function register($name, callable $callback, array $params)
      {
        $uuid = uuid();
        self::$names[]  = $uuid;
        self::$fibers[] = new Fiber($callback);
        self::$params[] = $params;
        return $uuid;
      }
    
      public static function run() {
        $output = [];
    
        while (self::$fibers) {
          foreach (self::$fibers as $i => $fiber) {
              try {
                  if (!$fiber->isStarted()) {
                      register_tick_function('Thread::scheduler');
                      $fiber->start(...self::$params[$i]);
                  } elseif ($fiber->isTerminated()) {
                      $output[self::$names[$i]] = $fiber->getReturn();
                      unset(self::$fibers[$i]);
                  } elseif ($fiber->isSuspended()) {
                    $fiber->resume();
                  }                
              } catch (Throwable $e) {
                  $output[self::$names[$i]] = $e;
              }
          }
        }
    
        return $output;
      }

      public static function getThreads()
      {
        return self::$names;
      }

      public static function stop($name)
      { 
        if ($name === 'all') {
          foreach (self::$fibers as $fiber) {
            $fiber->terminate();
          }
          self::$names = [];
          self::$fibers = [];
          self::$params = [];
          return true;
        }
        foreach (self::$names as $i => $uuid) {
          if ($uuid === $name) {
            self::$fibers[$i]->terminate();
            unset(self::$names[$i], self::$fibers[$i], self::$params[$i]);
            break;
          }
        }
      }
    
      public static function scheduler () {
        if(Fiber::getCurrent() === null) {
          return;
        }
    
        // running Fiber::suspend() in this if condition will prevent an infinite loop!
        if(count(self::$fibers) > 1)
        {
          Fiber::suspend();
        }
      }
    }
    $bot["thread"]["class"] = new Thread();
}
    //Thread::register(随便填, 函数名, [参数数组]);
    //$outputs = Thread::run();
