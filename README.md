# EC-SI-LMT-DHCP-WWW

PHP web interface for lab DHCP management. The shared page chrome in `intel_header.php` titles the site “Shannon Labs DHCP Server Status” and groups the navigation under LMT (Lab Management Tool). The checked-in version is in `version` (`version=0.0.7`).

This repository is the web tree itself. It does not contain an installer, a package manifest, a container definition, or a documented setup procedure. Pages and scripts assume a specific document root and a set of files and services that live outside this tree.

## Layout

PHP pages and classes sit at the repository root. Shared helpers are under `includes/`. `vendor/autoload.php` is not a Composer autoload file; it only `require`s those local PHP files so they can be analysed together.

| Path                                                          | Role                                                                                                                                                                                                           |
| ------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `index.php`                                                   | Home page. Loads `logs/dhcpd-stats.txt` and `logs/ping-stats.txt` and draws charts with the vendored Highcharts scripts.                                                                                       |
| `intel_header.php`, `intel_footer.php`                        | Shared layout, navigation, and client libraries. `intel_header.php` also includes `commonheader.php` from outside this repository (lab NFS path under `local/EC/IT/common`; the full path is in that include). |
| `login.php`, `ldap.class.php`                                 | Session login against LDAP. Admin-only pages check `getlogintype() == "admin"`.                                                                                                                                |
| `checker.php`                                                 | Search form target from the header. Matches a query against `/var/lib/dhcpd/dhcpd.leases` and fixed hosts parsed from `/etc/dhcpd.conf`.                                                                       |
| `config.php`                                                  | `opt=leases` lists rows from the `data/boards.*` files. Any other `opt` prints `/etc/dhcpd.conf`.                                                                                                              |
| `dhcpd-static.php`, `dhcpd-dynamic.php`                       | Static leases from `/etc/dhcpd.conf`, and a fixed dynamic-node table. Both can ping listed addresses.                                                                                                          |
| `dhcpd-unused.php`                                            | Compares `data/boards.*` leases with dead addresses in `logs/ping-stats.txt`. With a CLI argument it rewrites `logs/leases-30-days-not-found.txt` from `/var/log/dhcpd.log*`.                                  |
| `log.php`                                                     | `opt=lmtlog` renders the LMT sync log at `/scripts/log/LMTdhcpLog.txt`.                                                                                                                                        |
| `inventory.php`                                               | Admin view of CRB and asset lines in `/scripts/log/LMTall.txt`.                                                                                                                                                |
| `pxe.php`                                                     | Logged-in users edit PXE options stored in `pxe/pxe.txt`, using entries from `/scripts/log/LMTdhcpLog.txt`.                                                                                                    |
| `live-log.php`                                                | Tails `/var/log/dhcpd.log`.                                                                                                                                                                                    |
| `admin.php`                                                   | Admin actions: `sudo /etc/init.d/dhcpd restart`, or rebuild via `cqperl /scripts/dhcp.pl`. Both send mail through `mailer.class.php`.                                                                          |
| `servicepw.php`                                               | PC audit page. Connects to MySQL with the old `mysql_*` API and probes hosts over SSH (`ssh.class.php`).                                                                                                       |
| `firewall.php`, `whitelistvalidator.php`, `in.txt`, `out.txt` | Rule list and scan results. `whitelistvalidator.php` is invoked as a PHP CLI script.                                                                                                                           |
| `dhcpd-stats.php`, `ping-stats.php`                           | CLI scripts. They write `/var/www/html/logs/dhcpd-stats.txt` and `/var/www/html/logs/ping-stats.txt`.                                                                                                          |
| `ping.php`, `functions.php`                                   | ICMP and `nmap` helpers used by the stats and lease pages.                                                                                                                                                     |
| `ixalookup.php`                                               | Prints a match for `$mac` from files under `/var/www/html/data/`.                                                                                                                                              |
| `contacts.php`                                                | Static contact text.                                                                                                                                                                                           |
| `db.php`, `includes/db_class.php`, `includes/db_row.php`      | PDO MySQL wrapper (`Intel\Lmt\DB`). `pmd/test.php` is an example script that calls it and has its own connection values, which are not repeated here.                                                          |
| `includes/leases_class.php`, `leases.class.php`               | DHCP lease file parser.                                                                                                                                                                                        |
| `includes/dhcpd_hosts.php`, `includes/lab_tables.php`         | Shared dhcpd host parsing and LMT sync-log tables.                                                                                                                                                             |
| `conf.php`                                                    | Defines database and mail constants. No other file in this repository includes it.                                                                                                                             |
| `data/`                                                       | Board assignment files (`boards.*`) and a sample crontab (`data/crontab`).                                                                                                                                     |
| `logs/`                                                       | Runtime JSON and text snapshots produced by the CLI scripts. Listed in `.ignore`.                                                                                                                              |
| `css/`, `js/`, `images/`                                      | Vendored frontend libraries and images. `.ignore` records which of these are third-party.                                                                                                                      |

`js/intel.js.php` is the page script (clock, live log, charts). It is generated as JavaScript.

## Dependencies

Nothing in the repository declares a PHP version or installs packages. From the code, a deployment needs:

- PHP with the `ldap`, `pdo_mysql`, `ssh2`, and `sockets` extensions, plus `mail()` and `proc_open` / `shell_exec`. `servicepw.php` calls the removed `mysql_*` functions, so that page does not run on a current PHP.
- A web server that executes PHP. `.htaccess` enables `mod_rewrite` and redirects `isos/` elsewhere. The rewrite target is an internal host and is not repeated here.
- ISC `dhcpd`, via `/etc/init.d/dhcpd`, with `/etc/dhcpd.conf`, `/var/lib/dhcpd/dhcpd.leases`, and `/var/log/dhcpd.log` (plus dated rotations `dhcpd-stats.php` reads).
- `/usr/bin/nmap`, used by `ping()` in `functions.php`.
- MySQL. Connection values are hardcoded in `servicepw.php` and defined, but unused by other files, in `conf.php`. Do not copy those values into documentation.
- LDAP controllers and directory names are hardcoded in `ldap.class.php`.
- ClearQuest `cqperl` and scripts under `/scripts/` and `/opt/rational/clearquest/bin/`. Those scripts are not in this repository. `admin.php` and `data/crontab` call them.
- `sudo` for the dhcpd restart and config rebuild in `admin.php`.

Frontend libraries are already in the tree. `.ignore` identifies them as jQuery 1.7.2, jQuery UI 1.8.21, jQuery Validation Plugin 1.7, Highcharts JS 2.3.2, ExplorerCanvas, and SyntaxHighlighter 3.0.83.

## Run and scheduled jobs

There is no run script. The pages are written to be served with the document root at `/var/www/html` (that path is hardcoded in several scripts). The header reads dhcpd status with `/etc/init.d/dhcpd status` and `/etc/init.d/dhcpd configtest` on each request.

`data/crontab` is a crontab snippet for that host. The entries that call this tree are:

- hourly, at minute 15: `php /var/www/html/ping-stats.php`
- hourly, at minute 45: `php /var/www/html/dhcpd-stats.php`
- every 2 hours: `php /var/www/html/dhcpd-unused.php dhcplogscan`
- every 20 minutes: `php /var/www/html/whitelistvalidator.php`

The same file also schedules `cqperl` jobs and `/scripts/main`, which are not part of this repository. It contains a mail recipient and an internal time-server name; those values are left in `data/crontab` only.

GitHub Actions under `.github/workflows/` run on pushes and pull requests to `main` by calling a reusable workflow in another repository, and a separate workflow responds to new issues and pull requests. `.github/dependabot.yml` updates GitHub Actions weekly. No test suite is defined in this repository.

## License

Apache License 2.0. See `LICENSE`.
