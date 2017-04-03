# Slave DNS Manager

[![Apache 2](http://img.shields.io/badge/license-Apache%202-blue.svg)](http://www.apache.org/licenses/LICENSE-2.0)

The extension for managing a remote slave DNS server via rndc protocol (bind).

The following techniques are demonstrated:
* Integration with DNS subsystem.

# Troubleshooting
## CentOS 7
First of all, please read `man named` especially section NOTES. A few paragraphs from `named` manual:

> By default, Red Hat ships BIND with the most secure SELinux policy that will not prevent normal BIND operation and will prevent exploitation of all known BIND security vulnerabilities . See the selinux(8) man page for information about SElinux.

> It is not necessary to run named in a chroot environment if the Red Hat SELinux policy for named is enabled. When enabled, this policy is far more secure than a chroot environment. Users are recommended to enable SELinux and remove the bind-chroot package.

### Enabled SELinux
* By default, the SELinux policy does not allow named to write any master zone database files.

`# setsebool -P named_write_master_zones 1`

### Disabled SELinux
* Check group write privelege to /var/named, /var/named/chroot/var/named/

`# chmod g+w /var/named /var/named/chroot/var/named`

