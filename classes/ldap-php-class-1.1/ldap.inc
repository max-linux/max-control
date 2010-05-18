<?
/*****************************************************************************
	
ldap.inc - version 1.1 

Copyright (C) 1998  Eric Kilfoil eric@ipass.net

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

The author can be reached at eric@ypass.net

*****************************************************************************/

if (defined("LDAP_INC"))
	return("");
define("LDAP_INC", "1");

/*
*		The hostname of the LDAP server.  It may also be a space separated
*		list of LDAP servers
*/
define("LDAP_HOSTNAME", "localhost");

/* 
*	 The BASE DN of your ldap server. (if you don't know what it is, 
*	 you should check your slapd.conf file)
*/
define("LDAP_BASEDN", "dc=ipass, dc=net");

/*
*	 The BIND DN for your ldap server. (if you don't know what it is, 
*	 you should check your slapd.conf file)
*/
define("LDAP_BINDDN", "");

/*
*	 The BIND PW for your ldap server. (if you don't know what it is, 
*	 you should check your slapd.conf file)
*/
define("LDAP_BINDPW", "");

/*
*	 The FULL path to the configuration file with your objectClass
*	 definitions.  This file must be readable by the process using
*	 this class (probably your web server).  Do _NOT_ make your
*	 slapd.conf file world readable.  If you have objectClass definitions
*	 in your slapd.conf file, you should separate them into a slapd.oc.conf
*	 file.
*/
define("LDAP_SLAPD_OC_CONF_PATH", "/usr/local/etc/openldap/slapd.oc.conf");

class LDAP {
	var $hostname = LDAP_HOSTNAME;
	var $basedn = LDAP_BASEDN;
	var $binddn = LDAP_BINDDN;
	var $bindpw = LDAP_BINDPW;
	var $OCconfigFilePath = LDAP_SLAPD_OC_CONF_PATH;
	var $cid = 0; // LDAP Server Connection ID
	var $bid = 0; // LDAP Server Bind ID
	var $sr = 0; // Search Result
	var $re = 0; // Result Entry
	var $error = ""; // Any error messages to be returned can be put here
	var $start = 0; // 0 if we are fetching the first entry, otherwise 1
	var $objectClasses = array(); // Information read from slapd.oc.conf

	function LDAP($binddn = "", $bindpw = "", $hostname = "")
	{
		if ($binddn == "")
			$binddn = $this->binddn;
		if ($bindpw == "")
			$bindpw = $this->bindpw;
		if ($hostname == "")
			$hostname = $this->hostname;
		$this->connect($binddn, $bindpw, $hostname);
	}

	function readConfiguration($OCconfigFilePath = "")
	{
		if ($OCconfigFilePath == "")
			$OCconfigFilePath = $this->OCconfigFilePath;

		if ($cf = fopen($OCconfigFilePath, "r"))
		{
			$ctr = 0;
			while (! feof($cf))
			{
				$line = fgets($cf, 1024);
				if ((chop($line) == "") || ereg("^[ \t]*#", $line, $regs)) // It's blank or ONLY a comment
					continue;
				if (eregi("[ \t]*objectclass[ \t]+([^#]+)", $line, $regs))
				{
					$oc[$ctr] = "";
					while (chop($line) != "" && ! feof($cf))
					{
						$oc[$ctr] .= $line;
						$line = fgets($cf, 10240);
					}
				}
				$ctr++;
			}
			for ($ctr = 0; $ctr < count($oc); $ctr++)
			{
				$ocdef = split("[ \n\t\r]", $oc[$ctr]);
				for ($intctr=0, $def = 0; $def < count($ocdef); $def++)
				{
					if (chop($ocdef[$def]) != "")
					{
						$intctr++;
						switch ($intctr)
						{
							case 1:
								if (strcasecmp($ocdef[$def], "objectclass"))
								{
									echo "Error in objectclass $ocdef[1]. Expected 'objectclass', got '$ocdef[$def]'<br>";
									exit();
								}
								break;
							case 2:
								$ocname = strtolower($ocdef[$def]);
								break;
							case 3:
								if (strcasecmp($ocdef[$def], "requires") && strcasecmp($ocdef[$def], "allows"))
								{
									echo "Error in objectclass $ocdef[1]. Expected 'requires' or 'allows', got '$ocdef[$def]'<br>";
									exit();
								} else
									$curarray = $ocdef[$def];
								$occtr = 0;
								break;
							default:
								if (substr($ocdef[$def], strlen($ocdef[$def])-1, 1) == ",")
								{
									// it is _NOT_ the last entry
									$this->objectClasses[$ocname][$curarray][$occtr++] = strtolower(substr($ocdef[$def], 0, strlen($ocdef[$def])-1));
								} else {
									// it _IS_ the last entry
									$this->objectClasses[$ocname][$curarray][$occtr++] = strtolower($ocdef[$def]);
									$intctr = 2;
								}
								break;
						}
					}
				}
			}
		} else {
			$this->error = "Could not open $OCconfigFilePath for read";
			echo $this->error;
		}
	}

	function getObjectClasses()
	{
		if (count($this->objectClasses) == 0)
			$this->readConfiguration();

		$ocs = array();
		for ($ctr = 0, reset($this->objectClasses); $OCName = key($this->objectClasses); next($this->objectClasses), $ctr++)
			$ocs[$ctr] = $OCName;
		return($ocs);
	}

	function isObjectClass($ocname)
	{
		$ocname = strtolower($ocname);

		if (count($this->objectClasses) == 0)
			$this->readConfiguration();

		if (is_array($this->objectClasses[$ocname]))
			return(1);
		return(0);
	}


	function getAllows($ocname)
	{
		$ocname = strtolower($ocname);

		if (count($this->objectClasses) == 0)
			$this->readConfiguration();

		if (! $this->isObjectClass($ocname))
			return(array());

		$allows = array();
		$allows = $this->objectClasses[$ocname]["allows"];
		return($allows);
	}

	function getRequires($ocname)
	{
		$ocname = strtolower($ocname);

		if (count($this->objectClasses) == 0)
			$this->readConfiguration();

		if (! $this->isObjectClass($ocname))
			return(array());

		$requires = array();
		$requires = $this->objectClasses[$ocname]["requires"];
		return($requires);
	}

	function isAllowed($ocname, $allowed)
	{
		$ocname = strtolower($ocname);
		$allowed = strtolower($allowed);

		if (count($this->objectClasses) == 0)
			$this->readConfiguration();

		if (! $this->isObjectClass($ocname))
			return(0);

		for ($ctr = 0; $ctr < count($this->objectClasses[$ocname]["allows"]); $ctr++)
			if (strcasecmp($this->objectClasses[$ocname]["allows"][$ctr], $allowed) == 0)
				return(1);

		return(0);
	}

	function isRequired($ocname, $required)
	{
		$ocname = strtolower($ocname);
		$required = strtolower($required);

		if (count($this->objectClasses) == 0)
			$this->readConfiguration();

		if (! $this->isObjectClass($ocname))
			return(0);

		for ($ctr = 0; $ctr < count($this->objectClasses[$ocname]["requires"]); $ctr++)
			if (strcasecmp($this->objectClasses[$ocname]["requires"][$ctr], $required) == 0)
				return(1);

		return(0);
	}

	function setLDAPHost($hostname)
	{
		$this->hostname = $hostname;
	}

	function getLDAPHost($hostname)
	{
		return($this->hostname);
	}

	function setBindDN($binddn)
	{
		$this->binddn = $binddn;
	}

	function getBindDN($binddn)
	{
		return($this->binddn);
	}

	function setBaseDN($basedn)
	{
		$this->basedn = $basedn;
	}

	function getBaseDN($basedn)
	{
		return($this->basedn);
	}

	function setBindPassword($bindpw)
	{
		$this->bindpw = $bindpw;
	}

	function getBindPassword($bindpw)
	{
		return($this->bindpw);
	}


	function cd($dir)
	{
		if ($dir == "..")
			$this->basedn = $this->getParentDir();
		else 
			$this->basedn = $dir;
	}

	function getParentDir($basedn = "")
	{
		if (!$basedn)
			$basedn = $this->basedn;
		if ($this->basedn == LDAP_BASEDN)
			return("");
		return(ereg_replace("[^,]*[,]*[ ]*(.*)", "\\1", $basedn));
	}

	function connect($binddn, $bindpw, $hostname)
	{
		$e = error_reporting(0);
		if (! $this->cid) 
		{
			if ($this->cid=ldap_connect($hostname)) {
				$this->error = "No Error";
				if ($this->bid = ldap_bind($this->cid, $binddn, $bindpw)) {
					$this->error = "Success";
					error_reporting($e);
					return($this->bid);
				} else {
					$this->error = "Could not bind to " . $binddn;
					error_reporting($e);
					return($this->bid);
				}
			} else {
				$this->error = "Could not connect to LDAP server";
				error_reporting($e);
				return($this->cid);
			}
		} else {
			error_reporting($e);
			return($this->cid);
		}
	}

	function disconnect()
	{
		ldap_close($this->cid);
	}

	function search($filter)
	{
		$e = error_reporting(0);
		$result = array();
		if (!$this->connect())
		{
			error_reporting($e);
			return(0);
		}
		
		$this->sr = ldap_search($this->cid, $this->basedn, $filter);
		$ldap->error = ldap_error($this->cid);
		$this->resetResult();
		error_reporting($e);
		return($this->sr);
	}

	function ls($filter = "(objectclass=*)", $basedn = "")
	{
		if ($basedn == "")
			$basedn = $this->basedn;
		if ($filter == "")
			$filter = "(objectclass=*)";

		$e = error_reporting(0);
		$result = array();
		if (!$this->connect())
		{
			error_reporting($e);
			return(0);
		}
		
		$this->sr = ldap_list($this->cid, $basedn, $filter);
		$ldap->error = ldap_error($this->cid);
		$this->resetResult();
		error_reporting($e);
		return($this->sr);
	}

	function cat($dn)
	{
		$e = error_reporting(0);
		$result = array();
		if (!$this->connect())
		{
			error_reporting($e);
			return(0);
		}
		$filter = "(objectclass=*)";
		
		$this->sr = ldap_read($this->cid, $dn, $filter);
		$ldap->error = ldap_error($this->cid);
		$this->resetResult();
		error_reporting($e);
		return($this->sr);
	}

	function fetch()
	{
		$e = error_reporting(0);
		if ($this->start == 0)
		{
			$this->start = 1;
			$this->re = ldap_first_entry($this->cid, $this->sr);
		} else {
			$this->re = ldap_next_entry($this->cid, $this->re);
		}
		if ($this->re)
		{
			$att = ldap_get_attributes($this->cid, $this->re);
		}
		$ldap->error = ldap_error($this->cid);
		error_reporting($e);
		return($att);
	}

	function resetResult()
	{
		$this->start = 0;
	}

	function getDN()
	{
		$e = error_reporting(0);
		$rv = ldap_get_dn($this->cid, $this->re);
		$ldap->error = ldap_error($this->cid);
		error_reporting($e);
		return($rv);
	}
	
	function count()
	{
		$e = error_reporting(0);
		$rv = ldap_count_entries($this->cid, $this->sr);
		$ldap->error = ldap_error($this->cid);
		error_reporting($e);
		return($rv);
	}

	function mkdir($attrname, $dirname, $basedn = "")
	{
		if ($basedn == "")
			$basedn = $this->basedn;
		$e = error_reporting(0);
		$info["objectclass"] = "top";
		//$info[$attrname] = $dirname;
		$r = ldap_add($this->cid, "$attrname=$dirname, " . $basedn, $info);
		$ldap->error = ldap_error($this->cid);
		error_reporting($e);
		return($r ? $r : 0);
	}

	function rm($attrs = "", $dn = "")
	{
		if ($dn == "")
			$dn = $this->basedn;

		$e = error_reporting(0);
		$r = ldap_mod_del($this->cid, $dn, $attrs);
		$ldap->error = ldap_error($this->cid);
		error_reporting($e);
		return($r);
	}

	function rename($attrs, $dn = "")
	{
		if ($dn == "")
			$dn = $this->basedn;

		$e = error_reporting(0);
		$r = ldap_mod_replace($this->cid, $dn, $attrs);
		$ldap->error = ldap_error($this->cid);
		error_reporting($e);
		return($r);
	}

	function rmdir($deletedn)
	{
		$e = error_reporting(0);
		$r = ldap_delete($this->cid, $deletedn);
		$this->error = ldap_error($this->cid);
		error_reporting($e);
		return($r ? $r : 0);
	}

	function modify($attrs)
	{
		$e = error_reporting(0);
		$r = ldap_modify($this->cid, $this->basedn, $attrs);
		$this->error = ldap_error($this->cid);
		error_reporting($e);
		return($r ? $r : 0);
	}
}
?>
