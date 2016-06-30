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

interface Encryption {

	/**
	 * Resets the encryption context
	 *
	 * @param $key string the new encryption key
	 *
	 * @throws InvalidKeyException if the given key is invalid
	 */
	public function reset($key);

	/**
	 * Encrypts a block of data
	 *
	 * @param $data string the data block
	 * @param $length integer the data block size
	 * @return string the encrypted block
	 *
	 * @throws InvalidBlockSizeException if the block size is incorrect
	 */
	public function encrypt($data, $length);

	/**
	 * Decrypts a block of data
	 *
	 * @param $data string the data block
	 * @param $length integer the data block size
	 * @return string the decrypted block
	 *
	 * @throws InvalidBlockSizeException if the block size is incorrect
	 */
	public function decrypt($data, $length);

	/**
	 * Gets the cipher block size
	 *
	 * @return integer
	 */
	public function getBlockSize();

}