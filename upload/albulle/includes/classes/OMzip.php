<?php

/**
 * On-the-fly Zip file creation class.
 * Makes and sends zip files without loading any file in RAM.
 * 2 possibilities (mixable) :
 *   - Use compression : the file will be sent after being gzcompressed
 *     BUT file must be able to fit in RAM for a GZ+CRC32+substr run
 *   - No compression : the file is directly sent : little RAM usage !
 *     Time saving, and best reactivity
 *     ZIP is not bigger when including photos or movies
 * + High level API to calculate the size of the ZIP, create & send it on the fly
 *   The download starts almost immediately, as there is no need to create the ZIP in advance
 *
 * Author/Copyright : Olivier MATHERET
 *   matheret@free.fr  http://matheret.free.fr
 * Licenced under CeCILL2
 *
 * Based on :
 *
 *  http://www.zend.com/codex.php?id=535&single=1
 *  By Eric Mueller <eric@themepark.com>
 *
 *  http://www.zend.com/codex.php?id=470&single=1
 *  by Denis125 <webmaster@atlant.ru>
 *
 *  a patch from Peter Listiak <mlady@users.sourceforge.net> for last modified
 *  date and time of the compressed file
 *
 *  ConvertCharset.class.php from Mikolaj Jedrzejak <mikolajj@op.pl> + unicode.org tables
 *  http://www.unicode.org  Unicode Homepage
 *  http://www.mikkom.pl    Mikolaj Jedrzejak Homepage
 *
 *  Reverse CRC adapted from "Reversing CRC � Theory and Practice"
 *  HU Berlin Public Report
 *  Martin Stigge, Henryk Plotz, Wolf Muller, Jens-Peter Redlich
 *
 * Official ZIP file format: http://www.pkware.com/appnote.txt
 *
 * @access  public
*/


require_once( 'OMzip/OMcrc32.php' );
require_once( 'OMzip/ConvertCharset.class.php' );



/**
 * Creates and sends a ZIP on the fly, with no compression (just great for Jpeg/Gif/Movies)
 * VERY little RAM consumption, as the files never get loaded in RAM
 * The download starts almost immediately, as the ZIP is not created before sending
 *
 * @param  name     name to give to the ZIP file
 * @param  array    array of files to include
 *
 * @return integer  the size of the zip in bytes
 *
 * @access public
 */
function OnTheFlyZIP ($name, $files )
{
    $zipsize = 0;
    $zip = new zipfile ($name, false);
    foreach ($files as $file)
        $zipsize = $zip -> addFile ($file, $file, false);
    $zipsize += $zip -> finalize ();

    if (!empty($name))
    {
        header( 'Content-length: '.$zipsize);
        flush();
        sleep(1);

        $zip = new zipfile ($name, true);
        foreach ($files as $file)
            $zip -> addFile ($file, $file, false);
        $zip -> finalize ();
    }
    return $zipsize;
}


class zipfile
{
    /**
     * Size of compressed data
     *
     * @var  integer    $datalen
     */
    var $datalen = 0;

    /**
     * Central directory
     *
     * @var  array    $ctrl_dir
     */
    var $ctrl_dir     = array();

    /**
     * End of central directory record
     *
     * @var  string   $eof_ctrl_dir
     */
    var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";


    /**
     * Constructor : send ZIP headers
     *
     * @param  string   archive name
     * @param  integer  actually send the data ?
     *
     * @access private
     */
    function zipfile($name = "archive.zip", $process = true) {
        $this -> process = $process;
        if ($process)
        {
            // If you're having a problem with a file download script not working with IE
            // if you call session_start() before sending the file, then try adding a
            // session_cache_limiter() call before session_start().
            header("Pragma: public");
            header('Content-Description: File Transfer');
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Transfer-Encoding: binary");
            header("Expires: 0");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: private",false);
            //header('Content-Type: application/force-download');
            header("Content-type: application/zip");
            header("Content-disposition: attachment; filename=\"$name\"");
            @set_time_limit(0);
        }
    }


    /**
     * Converts an Unix timestamp to a four byte DOS date and time format (date
     * in high two bytes, time in low two bytes allowing magnitude comparison).
     *
     * @param  integer  the current Unix timestamp
     *
     * @return integer  the current date in a four byte DOS format
     *
     * @access private
     */
    function unix2DosTime($unixtime = 0) {
        $timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);

        if ($timearray['year'] < 1980) {
        	$timearray['year']    = 1980;
        	$timearray['mon']     = 1;
        	$timearray['mday']    = 1;
        	$timearray['hours']   = 0;
        	$timearray['minutes'] = 0;
        	$timearray['seconds'] = 0;
        } // end if

        return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
                ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
    } // end of the 'unix2DosTime()' method


    /**
     * Sends a file to stdout, using output control, megabyte per megabyte.
     * Useful when readfile or passthru corrupt data, and supposed to be more efficient, but
     * consumes PHP execution time.
     *
     * @param  string  file path
     *
     * @return boolean     success
     *
     * @access private
     */
    function readfile_chunked ($filename) {
        $chunksize = 1*(1024*1024); // how many bytes per chunk
        $buffer = '';
        $handle = fopen($filename, 'rb');
        if ($handle === false) {
            return false;
        }
        while (!feof($handle)) {
            $buffer = fread($handle, $chunksize);
            @ob_start();
            echo $buffer;
            @ob_end_flush();
            @flush();
        }
        return fclose($handle);
    }

    /**
     * Adds file to archive
     *
     * @param  filename content file to add
     * @param  string   name of the file in the archive (may contains the path)
     * @param  boolean  whether or not to compress the data  CAUTION: file must fit in RAM if "true" !
     * @param  integer  the current timestamp
     *
     * @return boolean  success of operations
     *
     * @access public
     */
    function addFile($filename, $name, $deflate = true, $time = 0)
    {
        if ($this -> process) {
            $cchset = new ConvertCharset();
            $name = $cchset -> Convert( str_replace('./', '', str_replace('\\', '/', $name)) , "CP1252", "CP437" );
        }
        else {
            $name = str_replace('./', '', str_replace('\\', '/', $name));
        }

        $unc_len = @filesize ($filename);
        if ($unc_len == false)
            return $this -> datalen;

        $dtime    = dechex($this->unix2DosTime($time));
        $hexdtime = '\x' . $dtime[6] . $dtime[7]
                  . '\x' . $dtime[4] . $dtime[5]
                  . '\x' . $dtime[2] . $dtime[3]
                  . '\x' . $dtime[0] . $dtime[1];
        eval('$hexdtime = "' . $hexdtime . '";');

        $fr1  = "\x50\x4b\x03\x04";
        $fr1 .= "\x14\x00";    // ver needed to extract
        $fr1 .= "\x00\x00";    // gen purpose bit flag
        if ($deflate)
            $fr1 .= "\x08\x00";                // compression method
        else
            $fr1 .= "\x00\x00";                // compression method
        $fr1 .= $hexdtime;             // last mod time and date
        if ($this -> process)
            echo $fr1;

        // "local file header" segment
        if ($deflate)
        {
            $data    = file_get_contents($filename);
            if ($this -> process) {
                $crc     = crc32($data);
            }
            else {
                $crc     = 0xFFFFFFFF;
            }
            $zdata   = gzcompress($data);
            $c_len   = strlen($zdata);
            if ($this -> process) {
                $zdata   = substr(substr($zdata, 0, $c_len - 4), 2); // fix crc bug
            }
            $c_len  -= 6;
        }
        else
        {
            if ($this -> process) {
                $crc     = crc32_file($filename);  // crc32 that reads byte per byte the file
            }
            else {
                $crc     = 0xFFFFFFFF;
            }
            $c_len   = $unc_len;
        }

        $fr2       = pack('V', $crc);             // crc32
        $fr2      .= pack('V', $c_len);           // compressed filesize
        $fr2      .= pack('V', $unc_len);         // uncompressed filesize
        $fr2      .= pack('v', strlen($name));    // length of filename
        $fr2      .= pack('v', 0);                // extra field length
        $fr2 .= $name;

        if ($this -> process) {
            // send this !
            echo $fr2;
            // "file data" segment
            if ($deflate)
                echo $zdata;
            else {
                if (JB_AL_PANIER_NO_READFILE === false)
                    readfile ($filename);
                else
                    $this -> readfile_chunked ($filename);
            }
        }

        // now add to central directory record
        $cdrec  ="\x50\x4b\x01\x02";
        $cdrec .= "\x00\x00";                // version made by
        $cdrec .= "\x14\x00";                // version needed to extract
        $cdrec .= "\x00\x00";                // gen purpose bit flag
        if ($deflate)
            $cdrec .= "\x08\x00";                // compression method
        else
            $cdrec .= "\x00\x00";                // compression method
        $cdrec .= $hexdtime;                 // last mod time & date
        $cdrec .= pack('V', $crc);           // crc32
        $cdrec .= pack('V', $c_len);         // compressed filesize
        $cdrec .= pack('V', $unc_len);       // uncompressed filesize
        $cdrec .= pack('v', strlen($name) ); // length of filename
        $cdrec .= pack('v', 0 );             // extra field length
        $cdrec .= pack('v', 0 );             // file comment length
        $cdrec .= pack('v', 0 );             // disk number start
        $cdrec .= pack('v', 0 );             // internal file attributes
        $cdrec .= pack('V', 32 );            // external file attributes - 'archive' bit set

        $cdrec .= pack('V', $this -> datalen);
        $this -> datalen += strlen($fr1) + strlen($fr2) + $c_len;

        $cdrec .= $name;

        // optional extra field, file comment goes here
        // save to central directory
        $this -> ctrl_dir[] = $cdrec;
        return $this -> datalen;
    } // end of the 'addFile()' method


    /**
     * Dumps out end of ZIP
     *
     * @access public
     */
    function finalize()
    {
        $ctrldir = implode('', $this -> ctrl_dir);

        $end =
            $ctrldir.
            $this -> eof_ctrl_dir.
            pack('v', sizeof($this -> ctrl_dir)) .  // total # of entries "on this disk"
            pack('v', sizeof($this -> ctrl_dir)) .  // total # of entries overall
            pack('V', strlen($ctrldir)) .           // size of central dir
            pack('V', $this -> datalen) .           // offset to start of central dir
            "\x00\x00";                             // .zip file comment length
        if ($this -> process) {
            echo $end;
            return true;
        }
        else {
            return strlen ($end);
        }
    } // end of the 'file()' method

} // end of the 'zipfile' class


?>
