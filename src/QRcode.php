<?php
namespace luoyy\phpqrcode;

use Exception;
use luoyy\phpqrcode\FrameFiller;
use luoyy\phpqrcode\QRencode;
use luoyy\phpqrcode\QRinput;
use luoyy\phpqrcode\QRmask;
use luoyy\phpqrcode\QRrawcode;
use luoyy\phpqrcode\QRsplit;
use luoyy\phpqrcode\QRtools;

/*
 * PHP QR Code encoder
 *
 * Bitstream class
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

class QRcode
{

    public $version;
    public $width;
    public $data;

    //----------------------------------------------------------------------
    public function encodeMask(QRinput $input, $mask)
    {
        if ($input->getVersion() < 0 || $input->getVersion() > QRSPEC_VERSION_MAX) {
            throw new Exception('wrong version');
        }
        if ($input->getErrorCorrectionLevel() > QR_ECLEVEL_H) {
            throw new Exception('wrong level');
        }

        $raw = new QRrawcode($input);

        QRtools::markTime('after_raw');

        $version = $raw->version;
        $width = QRspec::getWidth($version);
        $frame = QRspec::newFrame($version);

        $filler = new FrameFiller($width, $frame);
        if (is_null($filler)) {
            return null;
        }

        // inteleaved data and ecc codes
        for ($i = 0; $i < $raw->dataLength + $raw->eccLength; $i++) {
            $code = $raw->getCode();
            $bit = 0x80;
            for ($j = 0; $j < 8; $j++) {
                $addr = $filler->next();
                $filler->setFrameAt($addr, 0x02 | (($bit & $code) != 0));
                $bit = $bit >> 1;
            }
        }

        QRtools::markTime('after_filler');

        unset($raw);

        // remainder bits
        $j = QRspec::getRemainder($version);
        for ($i = 0; $i < $j; $i++) {
            $addr = $filler->next();
            $filler->setFrameAt($addr, 0x02);
        }

        $frame = $filler->frame;
        unset($filler);

        // masking
        $maskObj = new QRmask();
        if ($mask < 0) {

            if (QR_FIND_BEST_MASK) {
                $masked = $maskObj->mask($width, $frame, $input->getErrorCorrectionLevel());
            } else {
                $masked = $maskObj->makeMask($width, $frame, (intval(QR_DEFAULT_MASK) % 8), $input->getErrorCorrectionLevel());
            }
        } else {
            $masked = $maskObj->makeMask($width, $frame, $mask, $input->getErrorCorrectionLevel());
        }

        if ($masked == null) {
            return null;
        }

        QRtools::markTime('after_mask');

        $this->version = $version;
        $this->width = $width;
        $this->data = $masked;

        return $this;
    }

    //----------------------------------------------------------------------
    public function encodeInput(QRinput $input)
    {
        return $this->encodeMask($input, -1);
    }

    //----------------------------------------------------------------------
    public function encodeString8bit($string, $version, $level)
    {
        if (is_null($string) || ($strlen = strlen($string)) === 0) {
            throw new Exception('empty string!');
            return null;
        }

        $input = new QRinput($version, $level);
        if ($input == null) {
            return null;
        }

        $ret = $input->append($input, QR_MODE_8, $strlen, str_split($string));
        if ($ret < 0) {
            unset($input);
            return null;
        }
        return $this->encodeInput($input);
    }

    //----------------------------------------------------------------------
    public function encodeString($string, $version, $level, $hint, $casesensitive)
    {

        if ($hint != QR_MODE_8 && $hint != QR_MODE_KANJI) {
            throw new Exception('bad hint');
            return null;
        }

        $input = new QRinput($version, $level);
        if ($input == null) {
            return null;
        }

        $ret = QRsplit::splitStringToQRinput($string, $input, $hint, $casesensitive);
        if ($ret < 0) {
            return null;
        }

        return $this->encodeInput($input);
    }

    //----------------------------------------------------------------------
    public static function png($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint = false)
    {
        $enc = QRencode::factory($level, $size, $margin);
        return $enc->encodePNG($text, $outfile, $saveandprint = false);
    }

    //----------------------------------------------------------------------
    public static function text($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4)
    {
        $enc = QRencode::factory($level, $size, $margin);
        return $enc->encode($text, $outfile);
    }

    //----------------------------------------------------------------------
    public static function raw($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4)
    {
        $enc = QRencode::factory($level, $size, $margin);
        return $enc->encodeRAW($text, $outfile);
    }
    //----------------------------------------------------------------------
    public static function base64($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4)
    {
        $enc = QRencode::factory($level, $size, $margin);
        return $enc->encodeBase64($text, $outfile);
    }
}
