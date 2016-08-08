##Shibboleth installation guide - CentOS

Configuration of yum repository

```
cd /etc/yum.repos.d
wget http://download.opensuse.org/repositories/security://shibboleth/CentOS_CentOS-6/security:shibboleth.repo
```

Installation

```
yum install shibboleth
```

Configuration of the Apache

```
yum install mod_ssl
```

TODO
