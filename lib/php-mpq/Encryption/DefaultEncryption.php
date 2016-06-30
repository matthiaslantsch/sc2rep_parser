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

namespace Rogiel\MPQ\Encryption;


use Rogiel\MPQ\Exception\Encryption\InvalidBlockSizeException;
use Rogiel\MPQ\Exception\Encryption\InvalidKeyException;
use Rogiel\MPQ\Util\CryptoUtils;

class DefaultEncryption implements Encryption {

	/**
	 * The encryption/decryption key
	 *
	 * @var string
	 */
	private $key;

	/**
	 * The encryption/decryption seed
	 *
	 * @var string
	 */
	private $seed;

	/**
	 * DefaultEncryption constructor.
	 * @param $key string the encryption key. The key must have exactly 10 bytes
	 */
	public function __construct($key) {
		CryptoUtils::initTable();
		$this->reset($key);
	}

	/**
	 * {@inheritdoc}
	 */
	public function reset($key) {
		if(strlen($key) != 10) {
			throw new InvalidKeyException(sprintf('The key is expected to have 10 bytes, %i given.', strlen($key)));
		}

		$this->key = $key;
		$this->seed = ((0xEEEE << 16) | 0xEEEE);
	}

	/**
	 * {@inheritdoc}
	 */
	public function decrypt($string, $length) {
		if($length % 4 != 0) {
			throw new InvalidBlockSizeException(sprintf('The block size is invalid. Input expected to be a multiple of 4, %s given', $length));
		}

		$data = $this->createBlockArray($string, $length);

		$blocks = $length / 4;
		for($block = 0;$block < $blocks; $block++) {
			$this->seed = CryptoUtils::uPlus($this->seed,CryptoUtils::$cryptTable[0x400 + ($this->key & 0xFF)]);
			$ch = $data[$block] ^ (CryptoUtils::uPlus($this->key,$this->seed));

			$this->key = (CryptoUtils::uPlus(((~$this->key) << 0x15), 0x11111111)) | (CryptoUtils::rShift($this->key,0x0B));
			$this->seed = CryptoUtils::uPlus(CryptoUtils::uPlus(CryptoUtils::uPlus($ch,$this->seed),($this->seed << 5)),3);
			$data[$block] = $ch & ((0xFFFF << 16) | 0xFFFF);
		}
		
		return $this->createDataStream($data, $length);
	}

	/**
	 * {@inheritdoc}
	 */
	public function encrypt($string, $length) {
		if($length % 4 != 0) {
			throw new InvalidBlockSizeException(sprintf('The block size is invalid. Input expected to be a multiple of 4, %s given', $length));
		}

		$data = $this->createBlockArray($string, $length);

		$blocks = $length / 4;
		for($block = 0;$block < $blocks; $block++) {
			$this->seed = CryptoUtils::uPlus($this->seed,CryptoUtils::$cryptTable[0x400 + ($this->key & 0xFF)]);
			$ch = $data[$block] ^ (CryptoUtils::uPlus($this->key,$this->seed));

			$this->key = (CryptoUtils::uPlus(((~$this->key) << 0x15), 0x11111111)) | (CryptoUtils::rShift($this->key,0x0B));
			$this->seed = CryptoUtils::uPlus(CryptoUtils::uPlus(CryptoUtils::uPlus($data[$block],$this->seed),($this->seed << 5)),3);
			$data[$block] = $ch & ((0xFFFF << 16) | 0xFFFF);
		}

		return $this->createDataStream($data, $length);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockSize() {
		return 4;
	}

	private function createBlockArray($string, $length) {
		$data = array();
		for($i = 0; $i<$length / 4; $i++) {
			$t = unpack("V", substr($string, 4*$i, 4));
			$data[$i] = $t[1];
		}
		return $data;
	}

	private function createDataStream($data, $length) {
		$dataOutput = '';
		for($i = 0; $i<$length / 4; $i++) {
			$dataOutput .= pack("V", $data[$i]);
		}
		return $dataOutput;
	}

}