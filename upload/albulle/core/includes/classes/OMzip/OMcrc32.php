<?php
/*
 * CRC32 calculation for big files : avoids loading whole file in RAM
 *
 * Author/Copyright : Olivier MATHERET
 *   matheret@free.fr  http://matheret.free.fr
 * Licenced under CeCILL2
 *
 * Based on :
 *  Reverse CRC adapted from "Reversing CRC – Theory and Practice"
 *  HU Berlin Public Report
 *  Martin Stigge, Henryk Plotz, Wolf Muller, Jens-Peter Redlich
 *
 */
 
 
    /**
    * Gives 4 bytes so that it afterwards will compute to the given crc
    *
    * This function uses the method of the multiplication with (x^N)^ -1.
    */
    function crc_reverse ( $crc )
    {
        $crc ^= 0xffffffff ;

        // calculate crc except for the last 4 bytes ; this is essentially crc32 ()*/
        $crcreg = 0xffffffff ;

        // calculate new content bits
        // new_content = tcrcreg * CRCINV mod CRCPOLY
        $new_content = 0;
        for ($i = 0; $i < 32; ++$i) {
            // reduce modulo CRCPOLY
            if ( $new_content & 1) {
                $new_content = ( ($new_content >> 1) & 0x7fffffff) ^ 0xedb88320 ;
            } else {
                $new_content = ($new_content >> 1) & 0x7fffffff;
            }
            // add CRCINV if corresponding bit of operand is set
            if ( $crc & 1) {
                $new_content ^= 0x5B358FD3 ;
            }
            $crc = ($crc >> 1) & 0x7fffffff;
        }

        $new_content ^= 0xffffffff;
        // return new content
        $buffer = "";
        for ($i = 0; $i < 4; ++$i)
            $buffer .= chr($new_content >> ($i*8) & 0xFF) ;
        return $buffer;
    }
   
    /* Calculates the CRC32 of the given file without loading the file in RAM at once */
    function crc32_file($name) {
        $sizeToRead = @filesize($name);
        if(($fp=fopen($name,'rb'))===false) return 0;

        $crcR = "";
        $crc = 0;
        while ($sizeToRead)
        {
            $buf         = $crcR . @fread($fp, min($sizeToRead,1048576));
            $sizeToRead -= min($sizeToRead,1048576);
            $crc         = crc32($buf);
            if ($sizeToRead)
                $crcR = crc_reverse($crc);
        }
        @fclose($fp);
        return $crc;
   }