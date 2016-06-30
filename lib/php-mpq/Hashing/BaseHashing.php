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

namespace Rogiel\MPQ\Hashing;


use Rogiel\MPQ\Util\CryptoUtils;

abstract class BaseHashing implements Hashing {
	
	private $hashType;

	public function __construct($hashType) {
		$this->hashType = $hashType;
		CryptoUtils::initTable();
	}

	public function hash($string) {
		$seed1 = 0x7FED7FED;
		$seed2 = ((0xEEEE << 16) | 0xEEEE);
		$strLen = strlen($string);

		for ($i = 0;$i < $strLen;$i++) {
			$next = ord(strtoupper(substr($string, $i, 1)));

			$seed1 = CryptoUtils::$cryptTable[($this->hashType << 8) + $next] ^ (CryptoUtils::uPlus($seed1,$seed2));
			$seed2 = CryptoUtils::uPlus(CryptoUtils::uPlus(CryptoUtils::uPlus(CryptoUtils::uPlus($next,$seed1),$seed2),$seed2 << 5),3);
		}
		return $seed1;
	}


// function that adds up two integers without allowing them to overflow to floats
	private function uPlus($o1, $o2) {
		$o1h = ($o1 >> 16) & 0xFFFF;
		$o1l = $o1 & 0xFFFF;

		$o2h = ($o2 >> 16) & 0xFFFF;
		$o2l = $o2 & 0xFFFF;

		$ol = $o1l + $o2l;
		$oh = $o1h + $o2h;
		if ($ol > 0xFFFF) { $oh += (($ol >> 16) & 0xFFFF); }
		return ((($oh << 16) & (0xFFFF << 16)) | ($ol & 0xFFFF));
	}


}