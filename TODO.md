# 1. Class Name matching
Optimize the class name matching by using efficient data structures or 
algorithms (e.g. trie-based matching or regular expressions).

# 2. Cache management
More sophisticated cache management strategy, such as Least Recently Used (LRU) 
or Time-To-Live (TTL) based eviction, to ensure that cache remains up-to-date 
and efficient.

# 3. Parallelization
Parallelize the process of matching and transforming classes and methods to 
reduce the overall processing time.

# 4. Incremental updates
Incremental update mechanism to process only new or changed classes and methods.

# 5. Monitoring
Monitor the performance of the library to identify bottlenecks and areas that 
need optimization (e.g. Profilers or benchmarking suites).

# 6. Documentation
- Document how to use xdebug with php-unit tests that use the 
  `#[RunTestsInSeparateProcesses]` attribute (PhpStorm)
- Create a flowchart

# 7. Testing
- Add tests for the `order` property of the `Transformer` class

# 8. Production/Development support
- Add support for production/development environments:
  - Production: Cache will not be checked for updates (better performance).
  - Development: Cache will be checked for updates (better debugging experience).
