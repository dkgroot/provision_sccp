# Provisioner for Skinny/Sccp Phones

Skinny/SCCP Phones expect all the firmware and configuration jumbled together in the root of the tftp directory, which looks very messy to me.

To get around this, the provision_sccp project uses regex rewrite rules, to redirect incoming traffic to their destination (currently nginx and tftp-hpa).

The project also serves as a repository of current / up to date cisco skinny/sccp firmware maintained for posterity (incase cisco were to drop support/fw-download for these phones).
