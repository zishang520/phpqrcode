<?php
/*
 * PHP QR Code encoder
 *
 * Common constants
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

// Encoding modes

defined('QR_MODdE_NUL') || define('QR_MODE_NUL', -1);
defined('QR_MODE_NUM') || define('QR_MODE_NUM', 0);
defined('QR_MODE_AN') || define('QR_MODE_AN', 1);
defined('QR_MODE_8') || define('QR_MODE_8', 2);
defined('QR_MODE_KANJI') || define('QR_MODE_KANJI', 3);
defined('QR_MODE_STRUCTURE') || define('QR_MODE_STRUCTURE', 4);

// Levels of error correction.

defined('QR_ECLEVEL_L') || define('QR_ECLEVEL_L', 0);
defined('QR_ECLEVEL_M') || define('QR_ECLEVEL_M', 1);
defined('QR_ECLEVEL_Q') || define('QR_ECLEVEL_Q', 2);
defined('QR_ECLEVEL_H') || define('QR_ECLEVEL_H', 3);

// Supported output formats

defined('QR_FORMAT_TEXT') || define('QR_FORMAT_TEXT', 0);
defined('QR_FORMAT_PNG') || define('QR_FORMAT_PNG', 1);

/*
 * PHP QR Code encoder
 *
 * Config file, tuned-up for merged verion
 */

defined('QR_CACHEABLE') || define('QR_CACHEABLE', false); // use cache - more disk reads but less CPU power, masks and format templates are stored there
defined('QR_CACHE_DIR') || define('QR_CACHE_DIR', false); // used when QR_CACHEABLE === true
defined('QR_LOG_DIR') || define('QR_LOG_DIR', false); // default error logs dir

defined('QR_FIND_BEST_MASK') || define('QR_FIND_BEST_MASK', true); // if true, estimates best mask (spec. default, but extremally slow; set to false to significant performance boost but (propably) worst quality code
defined('QR_FIND_FROM_RANDOM') || define('QR_FIND_FROM_RANDOM', 2); // if false, checks all masks available, otherwise value tells count of masks need to be checked, mask id are got randomly
defined('QR_DEFAULT_MASK') || define('QR_DEFAULT_MASK', 2); // when QR_FIND_BEST_MASK === false

defined('QR_PNG_MAXIMUM_SIZE') || define('QR_PNG_MAXIMUM_SIZE', 1024); // maximum allowed png image width (in pixels), tune to make sure GD and PHP can handle such big images

defined('QR_IMAGE') || define('QR_IMAGE', true);

defined('STRUCTURE_HEADER_BITS') || define('STRUCTURE_HEADER_BITS', 20);
defined('MAX_STRUCTURED_SYMBOLS') || define('MAX_STRUCTURED_SYMBOLS', 16);

defined('N1') || define('N1', 3);
defined('N2') || define('N2', 3);
defined('N3') || define('N3', 40);
defined('N4') || define('N4', 10);

defined('QRSPEC_VERSION_MAX') || define('QRSPEC_VERSION_MAX', 40);
defined('QRSPEC_WIDTH_MAX') || define('QRSPEC_WIDTH_MAX', 177);

defined('QRCAP_WIDTH') || define('QRCAP_WIDTH', 0);
defined('QRCAP_WORDS') || define('QRCAP_WORDS', 1);
defined('QRCAP_REMINDER') || define('QRCAP_REMINDER', 2);
defined('QRCAP_EC') || define('QRCAP_EC', 3);
