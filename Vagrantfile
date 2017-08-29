# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/xenial64"
  config.vm.network "private_network", ip: "192.168.50.10"
  config.vm.synced_folder "web/", "/vagrant", :owner => 'www-data', :group => 'www-data', :mount_options => ['dmode=755', 'fmode=755']
  config.vm.provision :ansible do |ansible|
      ansible.playbook = "deploy/development/playbook.yml"
    end
end
