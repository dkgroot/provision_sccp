# Provisioner for Skinny/Sccp Phones

Skinny/SCCP Phones expect all the firmware and configuration jumbled together in the root of the tftp directory, which looks very messy to me.

To get around this, the provision_sccp project uses regex rewrite rules, to redirect incoming traffic to their destination (currently nginx and tftp-hpa).

The project also serves as a repository of current / up to date cisco skinny/sccp firmware maintained for posterity (incase cisco were to drop support/fw-download for these phones).

## Dependencies
- [tftp-hpa](http://www.chschneider.eu/linux/server/tftpd-hpa.shtml)
- [nginx](https://www.nginx.com/resources/wiki/)

## Usage
- Clone the repository
- Move the tftpboot directory (depends on your operating system):
  - OpenSuSE / Ubuntu / Debian : /srv
  - BSD : /var/lib/tftp
  - RedHat : /tftpboot
- Copy etc/nginx/sites-available/tftpboot to /etc/nginx/sites-available/tftpboot
- Update the tftpboot directory location in /etc/nginx/sites-available/tftpboot 
- Copy etc/tftp-hpa/rewrite-rules to /etc/tftp-hpa
- Update the tftpboot directory location in etc/tftp-hpa/rewrite-rules
- Update your init system for tftp-hpa so it can find the rewrite-rules:
  - Ubuntu / Debian : /etc/defaults    (Example present in etc/defaults)
  - OpenSuse / Redhat : /etc/sysconfig (No example yet, have a look as etc/default and adapt)
  - BSD : /etc/rc.conf                 (No example yet, have a look as etc/default and adapt)
- Restart your tftp-hpa and nginx services
- Monitor the debug logging:
  - tail -f /var/log/syslog /var/log/nginx/tftp.*.log

## Want to Contribute:
[Code of Conduct](https://github.com/dkgroot/provision_sccp/blob/master/CODE_OF_CONDUCT.md)

## License:
[License](https://github.com/dkgroot/provision_sccp/blob/master/LICENSE)
