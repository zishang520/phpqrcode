<?php
// ============================================================+
// File name   : qrcode.php
// Begin       : 2010-03-22
// Last Update : 2010-03-29
// Version     : 1.0.002
// License     : GNU LGPL v.3 (http://www.gnu.org/copyleft/lesser.html)
//     ----------------------------------------------------------------------------

//     This library is free software; you can redistribute it and/or
//     modify it under the terms of the GNU Lesser General Public
//     License as published by the Free Software Foundation; either
//     version 3 of the License, or any later version.

//     This library is distributed in the hope that it will be useful,
//     but WITHOUT ANY WARRANTY; without even the implied warranty of
//     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
//     Lesser General Public License for more details.

//     You should have received a copy of the GNU Lesser General Public
//     License along with this library; if not, write to the Free Software
//     Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
//  or browse http://www.gnu.org/copyleft/lesser.html

//  ----------------------------------------------------------------------------

// DESCRIPTION :

// Class to create QR-code arrays for TCPDF class.
// QR Code symbol is a 2D barcode that can be scanned by
// handy terminals such as a mobile phone with CCD.
// The capacity of QR Code is up to 7000 digits or 4000
// characters, and has high robustness.
// This class supports QR Code model 2, described in
// JIS (Japanese Industrial Standards) X0510:2004
// or ISO/IEC 18004.
// Currently the following features are not supported:
// ECI and FNC1 mode, Micro QR Code, QR Code model 1,
// Structured mode.

// This class is derived from the following projects:
// ---------------------------------------------------------
// "PHP QR Code encoder"
// License: GNU-LGPLv3
// Copyright (C) 2010 by Dominik Dzienia <deltalab at poczta dot fm>
// http://phpqrcode.sourceforge.net/
// https://sourceforge.net/projects/phpqrcode/

// The "PHP QR Code encoder" is based on
// "C libqrencode library" (ver. 3.1.1)
// License: GNU-LGPL 2.1
// Copyright (C) 2006-2010 by Kentaro Fukuchi
// http://megaui.net/fukuchi/works/qrencode/index.en.html

// Reed-Solomon code encoder is written by Phil Karn, KA9Q.
// Copyright (C) 2002-2006 Phil Karn, KA9Q

// QR Code is registered trademark of DENSO WAVE INCORPORATED
// http://www.denso-wave.com/qrcode/index-e.html
// ---------------------------------------------------------

// Author: Nicola Asuni

// (c) Copyright 2010:
//               Nicola Asuni
//               Tecnick.com S.r.l.
//               Via della Pace, 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
// ============================================================+
/**
 * Class to create QR-code arrays for TCPDF class.
 * QR Code symbol is a 2D barcode that can be scanned by handy terminals such as a mobile phone with CCD.
 * The capacity of QR Code is up to 7000 digits or 4000 characters, and has high robustness.
 * This class supports QR Code model 2, described in JIS (Japanese Industrial Standards) X0510:2004 or ISO/IEC 18004.
 * Currently the following features are not supported: ECI and FNC1 mode, Micro QR Code, QR Code model 1, Structured mode.
 *
 * This class is derived from "PHP QR Code encoder" by Dominik Dzienia (http://phpqrcode.sourceforge.net/) based on "libqrencode C library 3.1.1." by Kentaro Fukuchi (http://megaui.net/fukuchi/works/qrencode/index.en.html), contains Reed-Solomon code written by Phil Karn, KA9Q. QR Code is registered trademark of DENSO WAVE INCORPORATED (http://www.denso-wave.com/qrcode/index-e.html).
 * Please read comments on this class source file for full copyright and license information.
 *
 * @package com.tecnick.tcpdf
 * @abstract Class for generating QR-code array for TCPDF.
 * @author Nicola Asuni
 * @copyright 2010 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @link http://www.tcpdf.org
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @version 1.0.002
 */

// definitions
if (!defined('QRCODEDEFS')) {

    /**
     * Indicate that definitions for this class are set
     */
    define('QRCODEDEFS', true);

    // -----------------------------------------------------

    // Encoding modes (characters which can be encoded in QRcode)

    /**
     * Encoding mode
     */
    define('QR_MODE_NL', -1);

    /**
     * Encoding mode numeric (0-9). 3 characters are encoded to 10bit length. In theory, 7089 characters or less can be stored in a QRcode.
     */
    define('QR_MODE_NM', 0);

    /**
     * Encoding mode alphanumeric (0-9A-Z $%*+-./:) 45characters. 2 characters are encoded to 11bit length. In theory, 4296 characters or less can be stored in a QRcode.
     */
    define('QR_MODE_AN', 1);

    /**
     * Encoding mode 8bit byte data. In theory, 2953 characters or less can be stored in a QRcode.
     */
    define('QR_MODE_8B', 2);

    /**
     * Encoding mode KANJI. A KANJI character (multibyte character) is encoded to 13bit length. In theory, 1817 characters or less can be stored in a QRcode.
     */
    define('QR_MODE_KJ', 3);

    /**
     * Encoding mode STRUCTURED (currently unsupported)
     */
    define('QR_MODE_ST', 4);

    // -----------------------------------------------------

    // Levels of error correction.
    // QRcode has a function of an error correcting for miss reading that white is black.
    // Error correcting is defined in 4 level as below.

    /**
     * Error correction level L : About 7% or less errors can be corrected.
     */
    define('QR_ECLEVEL_L', 0);

    /**
     * Error correction level M : About 15% or less errors can be corrected.
     */
    define('QR_ECLEVEL_M', 1);

    /**
     * Error correction level Q : About 25% or less errors can be corrected.
     */
    define('QR_ECLEVEL_Q', 2);

    /**
     * Error correction level H : About 30% or less errors can be corrected.
     */
    define('QR_ECLEVEL_H', 3);

    // -----------------------------------------------------

    // Version. Size of QRcode is defined as version.
    // Version is from 1 to 40.
    // Version 1 is 21*21 matrix. And 4 modules increases whenever 1 version increases.
    // So version 40 is 177*177 matrix.

    /**
     * Maximum QR Code version.
     */
    define('QRSPEC_VERSION_MAX', 40);

    /**
     * Maximum matrix size for maximum version (version 40 is 177*177 matrix).
     */
    define('QRSPEC_WIDTH_MAX', 177);

    // -----------------------------------------------------

    /**
     * Matrix index to get width from $capacity array.
     */
    define('QRCAP_WIDTH', 0);

    /**
     * Matrix index to get number of words from $capacity array.
     */
    define('QRCAP_WORDS', 1);

    /**
     * Matrix index to get remainder from $capacity array.
     */
    define('QRCAP_REMINDER', 2);

    /**
     * Matrix index to get error correction level from $capacity array.
     */
    define('QRCAP_EC', 3);

    // -----------------------------------------------------

    // Structure (currently usupported)

    /**
     * Number of header bits for structured mode
     */
    define('STRUCTURE_HEADER_BITS', 20);

    /**
     * Max number of symbols for structured mode
     */
    define('MAX_STRUCTURED_SYMBOLS', 16);

    // -----------------------------------------------------

    // Masks

    /**
     * Down point base value for case 1 mask pattern (concatenation of same color in a line or a column)
     */
    define('N1', 3);

    /**
     * Down point base value for case 2 mask pattern (module block of same color)
     */
    define('N2', 3);

    /**
     * Down point base value for case 3 mask pattern (1:1:3:1:1(dark:bright:dark:bright:dark)pattern in a line or a column)
     */
    define('N3', 40);

    /**
     * Down point base value for case 4 mask pattern (ration of dark modules in whole)
     */
    define('N4', 10);

    // -----------------------------------------------------

    // Optimization settings

    /**
     * if true, estimates best mask (spec. default, but extremally slow; set to false to significant performance boost but (propably) worst quality code
     */
    define('QR_FIND_BEST_MASK', true);

    /**
     * if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
     */
    define('QR_FIND_FROM_RANDOM', 2);

    /**
     * when QR_FIND_BEST_MASK === false
     */
    define('QR_DEFAULT_MASK', 2);

    // -----------------------------------------------------

}
