**Basic Development Box using Ansible**

- Ubuntu 16.04 Xenial 64
- Apache 2.4
- PHP 7.1
- MySQL 5.7

**Setup**

- Make sure you have Ansible installed on your machine!
- Clone this repository and run `vagrant up`
- Connect to the box using `192.168.50.4`
    - Optionally, change the IP in the Vagrantfile
    
**To clone this box in an existing folder**

- Navigate to the desired folder
- Put a dot after the clone command, like the following.

Instead of 

```git clone git@github.com:LeanderHuysse/ubuntu-1604-php7-mysql57-vagrant.git```

use 

```git clone git@github.com:LeanderHuysse/ubuntu-1604-php7-mysql57-vagrant.git .```
