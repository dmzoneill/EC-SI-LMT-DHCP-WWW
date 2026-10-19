<?php

namespace Intel\Lmt;

use PDO;
use PDOException;

/*
 * @author David O Neill david.m.oneill@intel.com
 * @version 1
 * @date 12/7/2012
 * @desc Simple wrapper for PDO mysql
 *       More work need to realise and pdo drivers
 *       @link http://php.net/manual/en/pdo.drivers.php
 * @copy Intel Corporation
 */

if (!class_exists('Intel\\Lmt\\DB', false)) {
    class DB
    {
        private static $instance = null;

        private $sqlquerylog = array();
        private $pdo = null;
        private $host = null;
        private $user = null;
        private $pass = null;
        private $dbname = null;
        private $lasterror = null;


        /*
         * Private constructor see gang of four singleton pattern
         */
        private function __construct()
        {
        }


        /*
         * No copying!
         */
        private function __clone()
        {
        }


        /*
         * Static getinstance returns an instance of DB
         * @return DB
         */
        public static function getinstance()
        {
            if (!self::$instance) {
                self::$instance = new DB();
            }

            return self::$instance;
        }


        /*
         * mysql_connect(.....)
         * Connect wrapper for PDO
         *
         * @param string $host          - hostname to connect to
         * @param string $username      - username to use for the connection
         * @param string $password      - password to use for the connection
         * @param string $dbname        - database of interest
         * @param bool $persistent      - whether to use a persistent database connection
         * @return bool                 - successful or not - use geterror() on false
         */
        public function connect($host, $username, $password, $dbname, $persistent = false)
        {
            try {
                $this->host = $host;
                $this->user = $username;
                $this->pass = $password;
                $this->dbname = $dbname;

                $options = array(
                            PDO::ATTR_PERSISTENT => $persistent
                        );

                $dsn = 'mysql:dbname=' . $this->dbname . ';host=' . $this->host;
                $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                return true;
            } catch (PDOException $e) {
                return $this->recordError($e, __METHOD__, __FILE__, __LINE__);
            }
        }


        /*
         * xxxx_connect(.....)
         * Connect wrapper for PDO
         *
         * @param string $dsn           - refer to  Data Source Names.. ie sqlite:/path/to/database.sdb
         * @param string $username      - username to use for the connection
         * @param string $password      - password to use for the connection
         * @param bool $persistent      - whether to use a persistent database connection
         * @return bool                 - successful or not - use geterror() on false
         */
        public function connectdsn($dsn, $user = "", $pass = "", $persistent = false)
        {
            try {
                $username = null;
                $password = null;
                $this->user = $username;
                $this->pass = $password;

                $options = array(
                            PDO::ATTR_PERSISTENT => $persistent
                        );

                $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                return true;
            } catch (PDOException $e) {
                return $this->recordError($e, __METHOD__, __FILE__, __LINE__);
            }
        }


        /*
         * mysql_real_escape_string(.....)
         * Sanitize those variables
         *
         * @param string $input         - the string to clean
         * @return bool|string          - cleaned string on success or false - use geterror() on false
         */
        public function clean($input = null)
        {
            if ($input == null) {
                return false;
            }

            try {
                return $this->pdo->quote($input);
            } catch (PDOException $e) {
                return $this->recordError($e, __METHOD__, __FILE__, __LINE__);
            }
        }


        /*
         * query(.....)
         * Non query ( insert , update, delete ) ie nothing to return
         *
         * @param string $sql           - the sql to execute
         * @param bool $lastinsertid    - to true if you want the auto incremented id returned
         * @return bool|int             - rows update or lastinsertid - use geterror() on false
         */
        public function nonquery($sql = null, $lastinsertid = false)
        {
            if ($sql == null) {
                return false;
            }

            try {
                $this->sqlquerylog[] = $sql;
                $numrows = $this->pdo->exec($sql);

                if ($lastinsertid) {
                    return $this->pdo->lastInsertId();
                }

                return $numrows;
            } catch (PDOException $e) {
                return $this->recordError($e, __METHOD__, __FILE__, __LINE__);
            }
        }


        /*
         * query(.....)
         * Query one - one column from a row
         *
         * @param string $sql           - the sql to execute
         * @return bool|<type>          - the value - use geterror() on false
         */
        public function queryone($sql = null)
        {
            if ($sql == null) {
                return false;
            }

            try {
                $this->sqlquerylog[] = $sql;
                $result = $this->pdo->query($sql);
                $col = $result->fetchColumn();
                return $col;
            } catch (PDOException $e) {
                return $this->recordError($e, __METHOD__, __FILE__, __LINE__);
            }
        }


        /*
         * query(.....)
         * Query row - return an entire row as a DBRow
         *
         * @param string $sql           - the sql to execute
         * @return bool|DBRow           - the row as a DBRow - use geterror() on false
         */
        public function queryrow($sql = null)
        {
            if ($sql == null) {
                return false;
            }

            try {
                $this->sqlquerylog[] = $sql;
                $result = $this->pdo->query($sql);
                $result->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'DBRow');
                $row = $result->fetch();
                return $row;
            } catch (PDOException $e) {
                return $this->recordError($e, __METHOD__, __FILE__, __LINE__);
            }
        }


        /*
         * query(.....)
         * Query rows - array of DBRow
         *
         * @param string $sql           - the sql to execute
         * @return bool|array<DBRow>    - array of DBRow - use geterror() on false
         */
        public function queryrows($sql = null)
        {
            if ($sql == null) {
                return false;
            }

            try {
                $results = array();
                $this->sqlquerylog[] = $sql;
                $result = $this->pdo->query($sql);
                $result->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'DBRow');

                foreach ($result as $row) {
                    $results[] = $row;
                }

                return $results;
            } catch (PDOException $e) {
                return $this->recordError($e, __METHOD__, __FILE__, __LINE__);
            }
        }


        /*
         * Turn formatting to utf8
         *
         * @return bool             - true on success - use geterror() on false
         */
        public function setutf8()
        {
            try {
                $this->pdo->exec("SET CHARACTER SET utf8");
                return true;
            } catch (PDOException $e) {
                return $this->recordError($e, __METHOD__, __FILE__, __LINE__);
            }
        }


        /*
         * Closes the database connection
         */
        public function close()
        {
            $this->pdo = null;
        }


        /*
         * Returns the last error used in conjunction with other methods that return false
         *
         * @return string               - the error message from the last operation
         */
        public function geterror()
        {
            return $this->lasterror;
        }



        private function recordError(PDOException $e, $method, $file, $line)
        {
            $this->lasterror = $e->getMessage();
            $this->printerror(__CLASS__, $method, $file, $line);
            return false;
        }

        private function printerror($class, $method, $file, $line)
        {
            print "<div style='border:dashed 1px #AA0000; margin:5px; padding:10px'>";
            print "<b style='color:red; font-size:16pt'>DB Error</b><br />Exception thrown in class <b>"
            . $method . "</b> on line <b>" . $line . "</b>";
            print " in <b>" . $file . "</b><br />\n";
            print "Exception suppressed for security purposes, use <i><b>geterror()</b></i>"
            . " for more information<br />\n";
            print "</div>";
        }

        /*
         * prints the log the queries for the page view
         */
        public function printqueries($htmloutput = false)
        {
            $breaker = ( $htmloutput ) ? "<br />" : "\n";

            foreach ($this->sqlquerylog as $sql) {
                print $sql . $breaker;
            }
        }


        /*
         * returns the number of queries executed so far
         */
        public function numqueries()
        {
            return count($this->sqlquerylog);
        }


        /*
        * list installed drivers
        */
        public function listdrivers()
        {
            foreach (PDO::getAvailableDrivers() as $driver) {
                echo $driver . "<br />\n";
            }
        }
    }
}
