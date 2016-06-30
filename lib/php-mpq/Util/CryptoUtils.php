<?php
/**
 * Copyright (c) 2016, Rogiel Sulzbach
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
 * THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace Rogiel\MPQ\Util;


class CryptoUtils {

	public static $cryptTable;

	public static function initTable() {
		if (!self::$cryptTable) {
			self::$cryptTable = array();

			$seed = 0x00100001;
			for ($index1 = 0; $index1 < 0x100; $index1++) {
				for ($index2 = $index1, $i = 0; $i < 5; $i++, $index2 += 0x100) {
					$seed = (self::uPlus($seed * 125, 3)) % 0x2AAAAB;
					$temp1 = ($seed & 0xFFFF) << 0x10;

					$seed = (self::uPlus($seed * 125, 3)) % 0x2AAAAB;
					$temp2 = ($seed & 0xFFFF);

					self::$cryptTable[$index2] = ($temp1 | $temp2);
				}
			}
		}
	}

	public static function uPlus($o1, $o2) {
		$o1h = ($o1 >> 16) & 0xFFFF;
		$o1l = $o1 & 0xFFFF;

		$o2h = ($o2 >> 16) & 0xFFFF;
		$o2l = $o2 & 0xFFFF;

		$ol = $o1l + $o2l;
		$oh = $o1h + $o2h;
		if ($ol > 0xFFFF) { $oh += (($ol >> 16) & 0xFFFF); }
		return ((($oh << 16) & (0xFFFF << 16)) | ($ol & 0xFFFF));
	}

	public static function rShift($num,$bits) {
		return (($num >> 1) & 0x7FFFFFFF) >> ($bits - 1);
	}

}