# Debugging chdir & proc_open in travis

Steps to reproduce on official PHP binaries (via Docker):

```bash
    composer install
    docker build -t travis-debug .
    docker run -it --rm --name travis-debug-running travis-debug
``` 
