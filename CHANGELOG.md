## 更新日志

### 2020-11-24

* 增加 loadConfig 方法，实现多个 store 支持

  ```php
  # 实现方式
  ## 1、sls.php 配置多个 store;
  
  # 方法一，通过日志 channel
  ## 1、logging.php 配置选择第一种 handler 方式配置 多个 channel，同时配置 with 项；
  Log::channel('sls')->debug('admin create user.111', ['user' => 'bbb']);
  Log::channel('sls2')->debug('admin update user.222', ['user' => 'ccc']);
  Log::stack(['sls3', 'sls4'])->debug('admin delete user.333', ['user' => 'ddd']);
  
  ## 注意：同一次请求时请全部 channel 都配置 with.store 项，否则将继续使用上一次的 store
  Log::debug('admin create user.111', ['user' => 'bbb']); // 使用默认 store
  Log::channel('sls2')->debug('admin update user.222'); // 使用 sls2 的 store
  Log::debug('admin delete user.333', ['user' => 'ddd']); // 未配置 with.store 将使用 sls2 的 store
  
  # 方法二
  ## 1、写日志前先执行 loadConfig('store-new')；
  app('sls')->loadConfig('default-2')
  Log::debug('admin create user.333', ['user' => 'bbb', 'action' => 'cccccccccccc']);
  
  ## 注意：同一次请求时，要使用不同 store 需多次调用 loadConfig 方法
  ```