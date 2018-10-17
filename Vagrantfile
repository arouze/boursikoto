Vagrant.configure("2") do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.box_version = "20181002.0.0"
  config.vm.synced_folder "./", "/home/vagrant/boursikoto", mount_options: ["dmode=777","fmode=777"]

  config.vm.network "forwarded_port", guest: 86, host: 8080
  config.vm.network "forwarded_port", guest: 3386, host: 3396
  # config.vm.provision "shell", inline: <<-SHELL
  #   apt-get update
  # SHELL

  config.vm.provision "docker" do |d|
    config.vm.provision :docker
    config.vm.provision :docker_compose, yml: "/home/vagrant/boursikoto/docker-compose.yml", run: "always"
  end
end
