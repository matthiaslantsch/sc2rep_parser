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

class FileStream implements Stream {

	private $file;
	private $handle;

	// -----------------------------------------------------------------------------------------------------------------

	public function __construct($file) {
		$this->file = $file;
		$this->handle = fopen($file, 'r');
	}

	// -----------------------------------------------------------------------------------------------------------------

	/**
	 * {@inheritdoc}
	 */
	public function close() {
		fclose($this->handle);
	}

	/**
	 * {@inheritdoc}
	 */
	public function readByte() {
		return fread($this->handle, 1);
	}

	/**
	 * {@inheritdoc}
	 */
	public function readBytes($bytes) {
		return fread($this->handle, $bytes);
	}

	/**
	 * {@inheritdoc}
	 */
	public function seek($position) {
		fseek($this->handle, $position);
	}

	/**
	 * {@inheritdoc}
	 */
	public function skip($position) {
		fseek($this->handle, $position, SEEK_CUR);
	}

	/**
	 * {@inheritdoc}
	 */
	public function eof() {
		return feof($this->handle);
	}

	// -----------------------------------------------------------------------------------------------------------------

	public function __clone() {
		return new FileStream($this->file);
	}

}