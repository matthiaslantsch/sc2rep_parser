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

namespace Rogiel\MPQ\Stream;

interface Stream {

	/**
	 * Closes the stream
	 */
	public function close();

	/**
	 * Reads a single byte from the stream
	 *
	 * @return string
	 */
	public function readByte();

	/**
	 * Read up to "$bytes" bytes from the stream
	 *
	 * @param $bytes integer the maximum number of bytes to read
	 * @return string a string with up to $bytes characters
	 */
	public function readBytes($bytes);

	/**
	 * Seeks the stream into the given position
	 *
	 * @param $position integer the position to seek to
	 */
	public function seek($position);

	/**
	 * Skips $bytes bytes in the stream
	 *
	 * @param $bytes integer the number of bytes to skip
	 */
	public function skip($bytes);

	/**
	 * Checks if the stream has already reached the EOF
	 *
	 * @return boolean if the end of file has been reached
	 */
	public function eof();

}