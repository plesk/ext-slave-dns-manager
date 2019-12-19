# 1.9.2 (19 December 2019)
* [-] The status icons for the slave servers are now properly shown in the extension's interface. (EXTPLESK-1082)
* [-] On Plesk Obsidian servers, the extension's interface is no longer malformed and unusable. (EXTPLESK-1403)

# 1.9.1 (08 May 2018)
* [+] If slave DNS server supports flag "-clean" (BIND v9.10+), it will be added automatically for 'rndc delzone'

# 1.9.0 (29 May 2017)
* [-] Fixed an issue with IP-address if Plesk behind NAT (issue [#20](https://github.com/plesk/ext-slave-dns-manager/issues/20))
* [-] 'Resync' button hidden in Plesk 12.5 and earlier because required API not supported

# 1.8 (10 April 2017)
* [+] Add support choose server's IP (issue [#13](https://github.com/plesk/ext-slave-dns-manager/issues/13))
* [-] Fixed incompatibility with rndc on RedHat (issue [#8](https://github.com/plesk/ext-slave-dns-manager/issues/8))
* [+] Add support Split DNS (issue [#3](https://github.com/plesk/ext-slave-dns-manager/issues/3), [#7](https://github.com/plesk/ext-slave-dns-manager/issues/7))
* [*] The extension saves settings in key-value storage
* [*] Small increase verbosity in status icon tooltip if error occurred
* [+] Add button 'Resync' to sync all DNS zones from master to slaves
* [+] Add navigation pathbar

# 1.7-2 (4 April 2017)
* [+] Add "Troubleshooting" in README.md
* [*] Change link for [[learnMore]] from "http://devblog.plesk.com" to "https://www.plesk.com"
* [+] Add RU description in meta.xml
* [-] Fixed category in meta.xml
* [+] Add DESCRIPTION.md
* [+] Add CHANGES.md

